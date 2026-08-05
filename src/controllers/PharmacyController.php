<?php

class PharmacyController
{
    private static array $rolesLectura = ['admin', 'medico', 'farmacia'];

    public static function prescriptions(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT r.id, r.creado_en,
                    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    pe.nombre AS medico_nombre, pe.apellido AS medico_apellido
             FROM recetas r
             JOIN pacientes p ON p.id = r.paciente_id
             JOIN medicos m ON m.id = r.medico_id
             JOIN personal pe ON pe.id = m.personal_id
             ORDER BY r.creado_en DESC'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function createPrescription(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'medico']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['paciente_id', 'medico_id'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $receta = Receta::create(['paciente_id' => $data['paciente_id'], 'medico_id' => $data['medico_id']]);
        Audit::log('recetas', (string) $receta['id'], 'create', $receta);

        Response::json($receta, 201);
    }

    public static function prescriptionItems(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $recetaId = $request->query('receta_id');
        $filters = $recetaId ? ['receta_id' => $recetaId] : [];
        Response::json(RecetaItem::all(1, 100, $filters));
    }

    public static function createPrescriptionItem(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'medico']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['receta_id', 'medicamento_id', 'cantidad'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $item = RecetaItem::create([
            'receta_id' => $data['receta_id'],
            'medicamento_id' => $data['medicamento_id'],
            'cantidad' => $data['cantidad'],
            'indicaciones' => $data['indicaciones'] ?? null,
        ]);
        Audit::log('receta_items', (string) $item['id'], 'create', $item);

        Response::json($item, 201);
    }

    /**
     * Dispensa un ítem de receta usando el procedimiento almacenado sp_dispensar_receta,
     * que valida el stock disponible y descuenta del lote más próximo a vencer.
     */
    public static function dispense(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'farmacia']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['receta_item_id', 'cantidad'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $pdo = Database::connection();

        $stmtPersonal = $pdo->prepare('SELECT id FROM personal WHERE usuario_id = ?');
        $stmtPersonal->execute([Auth::id()]);
        $personal = $stmtPersonal->fetch();
        if (!$personal) {
            Response::error('El usuario actual no tiene un registro de personal asociado', 422);
        }

        try {
            $stmt = $pdo->prepare('CALL sp_dispensar_receta(?, ?, ?)');
            $stmt->execute([$data['receta_item_id'], $personal['id'], $data['cantidad']]);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            if ($e->getCode() === '45000' || str_contains($e->getMessage(), 'Stock insuficiente')) {
                Response::error('Stock insuficiente para dispensar la cantidad solicitada', 409);
            }
            if ($e->getCode() === '23000') {
                Response::error('Este ítem de receta ya fue dispensado anteriormente', 409);
            }
            throw $e;
        }

        Audit::log('dispensaciones', (string) $data['receta_item_id'], 'create', $data);
        Response::json(['message' => 'Dispensación registrada correctamente']);
    }
}
