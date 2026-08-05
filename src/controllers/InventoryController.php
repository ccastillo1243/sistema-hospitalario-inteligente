<?php

class InventoryController
{
    private static array $rolesLectura = ['admin', 'farmacia', 'enfermeria'];
    private static array $rolesEscritura = ['admin', 'farmacia'];

    public static function warehouses(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);
        Response::json(Almacen::all(1, 100));
    }

    public static function createWarehouse(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        if (empty($data['nombre'])) {
            Response::error("El campo 'nombre' es requerido", 400);
        }

        $almacen = Almacen::create($data);
        Audit::log('almacenes', (string) $almacen['id'], 'create', $almacen);
        Response::json($almacen, 201);
    }

    public static function items(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT a.id, a.nombre, a.categoria, a.unidad_medida, alm.nombre AS almacen_nombre,
                    COALESCE(e.cantidad, 0) AS cantidad
             FROM articulos_inventario a
             JOIN almacenes alm ON alm.id = a.almacen_id
             LEFT JOIN existencias_inventario e ON e.articulo_id = a.id
             ORDER BY a.nombre'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function createItem(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['almacen_id', 'nombre', 'unidad_medida'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $articulo = ArticuloInventario::create([
                'almacen_id' => $data['almacen_id'],
                'nombre' => $data['nombre'],
                'categoria' => $data['categoria'] ?? null,
                'unidad_medida' => $data['unidad_medida'],
            ]);
            ExistenciaInventario::create([
                'articulo_id' => $articulo['id'],
                'cantidad' => (int) ($data['cantidad_inicial'] ?? 0),
            ]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        Audit::log('articulos_inventario', (string) $articulo['id'], 'create', $articulo);
        Response::json($articulo, 201);
    }

    /**
     * Registra un movimiento de inventario (entrada/salida/ajuste) y actualiza
     * la existencia correspondiente dentro de una transacción.
     */
    public static function createMovement(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['articulo_id', 'tipo', 'cantidad'] as $field) {
            if (empty($data[$field]) && $data[$field] !== '0') {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        if (!in_array($data['tipo'], ['entrada', 'salida', 'ajuste'], true)) {
            Response::error("El campo 'tipo' debe ser entrada, salida o ajuste", 400);
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT id, cantidad FROM existencias_inventario WHERE articulo_id = ? FOR UPDATE');
            $stmt->execute([$data['articulo_id']]);
            $existencia = $stmt->fetch();

            if (!$existencia) {
                $pdo->rollBack();
                Response::error('No existe registro de existencia para este artículo', 404);
            }

            $delta = $data['tipo'] === 'salida' ? -abs((int) $data['cantidad']) : abs((int) $data['cantidad']);
            $nuevaCantidad = (int) $existencia['cantidad'] + $delta;

            if ($nuevaCantidad < 0) {
                $pdo->rollBack();
                Response::error('Stock insuficiente para esta salida', 409);
            }

            $pdo->prepare('UPDATE existencias_inventario SET cantidad = ? WHERE id = ?')
                ->execute([$nuevaCantidad, $existencia['id']]);

            $pdo->prepare(
                'INSERT INTO movimientos_inventario (existencia_id, tipo, cantidad, motivo) VALUES (?, ?, ?, ?)'
            )->execute([$existencia['id'], $data['tipo'], $data['cantidad'], $data['motivo'] ?? null]);

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        Audit::log('movimientos_inventario', (string) $existencia['id'], 'create', $data);
        Response::json(['message' => 'Movimiento registrado', 'nueva_cantidad' => $nuevaCantidad], 201);
    }
}
