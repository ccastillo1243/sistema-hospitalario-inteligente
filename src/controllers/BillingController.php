<?php

class BillingController
{
    private const TASA_IMPUESTO = 0.12;

    private static array $rolesLectura = ['admin', 'facturacion', 'recepcion'];
    private static array $rolesEscritura = ['admin', 'facturacion'];

    public static function invoices(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT f.id, f.estado, f.subtotal, f.impuestos, f.total, f.creado_en,
                    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    COALESCE((SELECT SUM(monto) FROM pagos WHERE factura_id = f.id), 0) AS pagado
             FROM facturas f
             JOIN pacientes p ON p.id = f.paciente_id
             ORDER BY f.creado_en DESC'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    /**
     * Crea una factura a partir de una lista de ítems [{servicio_id, cantidad}],
     * calculando subtotal/impuestos/total en una transacción.
     */
    public static function createInvoice(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        if (empty($data['paciente_id']) || empty($data['items']) || !is_array($data['items'])) {
            Response::error("Los campos 'paciente_id' e 'items' (arreglo) son requeridos", 400);
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $subtotal = 0.0;
            $itemsPreparados = [];

            foreach ($data['items'] as $item) {
                if (empty($item['servicio_id']) || empty($item['cantidad'])) {
                    throw new InvalidArgumentException("Cada ítem requiere 'servicio_id' y 'cantidad'");
                }
                $stmt = $pdo->prepare('SELECT precio FROM servicios_facturables WHERE id = ?');
                $stmt->execute([$item['servicio_id']]);
                $servicio = $stmt->fetch();
                if (!$servicio) {
                    throw new InvalidArgumentException('Servicio facturable no encontrado: ' . $item['servicio_id']);
                }
                $precioUnitario = (float) $servicio['precio'];
                $cantidad = (int) $item['cantidad'];
                $subtotalItem = $precioUnitario * $cantidad;
                $subtotal += $subtotalItem;

                $itemsPreparados[] = [
                    'servicio_id' => $item['servicio_id'],
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalItem,
                ];
            }

            $impuestos = round($subtotal * self::TASA_IMPUESTO, 2);
            $total = round($subtotal + $impuestos, 2);

            $stmt = $pdo->prepare(
                'INSERT INTO facturas (paciente_id, estado, subtotal, impuestos, total) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$data['paciente_id'], 'pendiente', $subtotal, $impuestos, $total]);
            $facturaId = (int) $pdo->lastInsertId();

            $stmtItem = $pdo->prepare(
                'INSERT INTO factura_items (factura_id, servicio_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)'
            );
            foreach ($itemsPreparados as $it) {
                $stmtItem->execute([$facturaId, $it['servicio_id'], $it['cantidad'], $it['precio_unitario'], $it['subtotal']]);
            }

            $pdo->commit();
        } catch (InvalidArgumentException $e) {
            $pdo->rollBack();
            Response::error($e->getMessage(), 400);
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        $factura = ['id' => $facturaId, 'paciente_id' => $data['paciente_id'], 'subtotal' => $subtotal, 'impuestos' => $impuestos, 'total' => $total, 'estado' => 'pendiente'];
        Audit::log('facturas', (string) $facturaId, 'create', $factura);

        Response::json($factura, 201);
    }

    public static function invoiceItems(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $facturaId = (int) $params['id'];
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT fi.*, s.nombre AS servicio_nombre
             FROM factura_items fi
             JOIN servicios_facturables s ON s.id = fi.servicio_id
             WHERE fi.factura_id = ?'
        );
        $stmt->execute([$facturaId]);

        Response::json(['items' => $stmt->fetchAll()]);
    }

    /**
     * Registra un pago usando el procedimiento almacenado sp_registrar_pago_factura,
     * que actualiza el estado de la factura (parcial/pagada) automáticamente.
     */
    public static function registerPayment(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['factura_id', 'metodo_pago_id', 'monto'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('CALL sp_registrar_pago_factura(?, ?, ?)');
        $stmt->execute([$data['factura_id'], $data['metodo_pago_id'], $data['monto']]);
        $stmt->closeCursor();

        Audit::log('pagos', (string) $data['factura_id'], 'create', $data);
        Response::json(['message' => 'Pago registrado correctamente']);
    }
}
