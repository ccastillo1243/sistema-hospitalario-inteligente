<?php

class RadiologyController
{
    private static array $rolesLectura = ['admin', 'medico', 'radiologia'];

    public static function orders(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT o.id, o.estado, o.creado_en,
                    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    pe.nombre AS medico_nombre, pe.apellido AS medico_apellido,
                    te.nombre AS tipo_estudio,
                    est.id AS estudio_id,
                    (est.id IS NOT NULL) AS tiene_estudio
             FROM ordenes_radiologia o
             JOIN pacientes p ON p.id = o.paciente_id
             JOIN medicos m ON m.id = o.medico_id
             JOIN personal pe ON pe.id = m.personal_id
             JOIN tipos_estudio_radiologico te ON te.id = o.tipo_estudio_id
             LEFT JOIN estudios_radiologicos est ON est.orden_id = o.id
             ORDER BY o.creado_en DESC'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function createOrder(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'medico']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['paciente_id', 'medico_id', 'tipo_estudio_id'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $orden = OrdenRadiologia::create([
            'paciente_id' => $data['paciente_id'],
            'medico_id' => $data['medico_id'],
            'tipo_estudio_id' => $data['tipo_estudio_id'],
            'estado' => 'pendiente',
        ]);
        Audit::log('ordenes_radiologia', (string) $orden['id'], 'create', $orden);

        Response::json($orden, 201);
    }

    public static function createStudy(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'radiologia']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        if (empty($data['orden_id']) || empty($data['realizado_por'])) {
            Response::error("Los campos 'orden_id' y 'realizado_por' son requeridos", 400);
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id FROM estudios_radiologicos WHERE orden_id = ?');
        $stmt->execute([$data['orden_id']]);
        if ($stmt->fetch()) {
            Response::error('Esta orden ya tiene un estudio registrado', 409);
        }

        $estudio = EstudioRadiologico::create([
            'orden_id' => $data['orden_id'],
            'realizado_por' => $data['realizado_por'],
            'observaciones' => $data['observaciones'] ?? null,
        ]);

        $pdo->prepare("UPDATE ordenes_radiologia SET estado = 'en_proceso' WHERE id = ?")
            ->execute([$data['orden_id']]);

        Audit::log('estudios_radiologicos', (string) $estudio['id'], 'create', $estudio);
        Response::json($estudio, 201);
    }

    public static function createReport(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'radiologia']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['estudio_id', 'radiologo_id', 'contenido'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $informe = InformeRadiologico::create([
            'estudio_id' => $data['estudio_id'],
            'radiologo_id' => $data['radiologo_id'],
            'contenido' => $data['contenido'],
        ]);

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT orden_id FROM estudios_radiologicos WHERE id = ?');
        $stmt->execute([$data['estudio_id']]);
        $ordenId = $stmt->fetchColumn();

        if ($ordenId) {
            $pdo->prepare("UPDATE ordenes_radiologia SET estado = 'completada' WHERE id = ?")
                ->execute([$ordenId]);
        }

        Audit::log('informes_radiologicos', (string) $informe['id'], 'create', $informe);
        Response::json($informe, 201);
    }
}
