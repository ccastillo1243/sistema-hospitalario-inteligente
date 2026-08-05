<?php

class HospitalizationController
{
    private static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion'];
    private static array $rolesEscritura = ['admin', 'medico', 'recepcion'];

    public static function admissions(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT i.id, i.motivo, i.ingresado_en,
                    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    c.codigo AS cama_codigo, h.numero AS habitacion_numero,
                    pe.nombre AS medico_nombre, pe.apellido AS medico_apellido,
                    (a.id IS NOT NULL) AS tiene_alta
             FROM ingresos i
             JOIN pacientes p ON p.id = i.paciente_id
             JOIN camas c ON c.id = i.cama_id
             JOIN habitaciones h ON h.id = c.habitacion_id
             JOIN medicos m ON m.id = i.medico_id
             JOIN personal pe ON pe.id = m.personal_id
             LEFT JOIN altas a ON a.ingreso_id = i.id
             ORDER BY i.ingresado_en DESC'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function createAdmission(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['paciente_id', 'cama_id', 'medico_id', 'motivo'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT estado FROM camas WHERE id = ?');
        $stmt->execute([$data['cama_id']]);
        $cama = $stmt->fetch();

        if (!$cama) {
            Response::error('Cama no encontrada', 404);
        }
        if ($cama['estado'] !== 'libre') {
            Response::error('La cama seleccionada no está libre', 409);
        }

        $ingreso = Ingreso::create($data);
        Audit::log('ingresos', (string) $ingreso['id'], 'create', $ingreso);

        Response::json($ingreso, 201);
    }

    public static function discharge(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        if (empty($data['ingreso_id']) || empty($data['resumen'])) {
            Response::error("Los campos 'ingreso_id' y 'resumen' son requeridos", 400);
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id FROM altas WHERE ingreso_id = ?');
        $stmt->execute([$data['ingreso_id']]);
        if ($stmt->fetch()) {
            Response::error('Este ingreso ya tiene un alta registrada', 409);
        }

        $alta = Alta::create([
            'ingreso_id' => $data['ingreso_id'],
            'resumen' => $data['resumen'],
            'tipo' => $data['tipo'] ?? 'medica',
        ]);
        Audit::log('altas', (string) $alta['id'], 'create', $alta);

        Response::json($alta, 201);
    }

    public static function transfer(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['ingreso_id', 'cama_destino_id'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT estado FROM camas WHERE id = ?');
        $stmt->execute([$data['cama_destino_id']]);
        $cama = $stmt->fetch();

        if (!$cama || $cama['estado'] !== 'libre') {
            Response::error('La cama destino no está disponible', 409);
        }

        $traslado = Traslado::create($data);
        Audit::log('traslados', (string) $traslado['id'], 'create', $traslado);

        Response::json($traslado, 201);
    }

    public static function nursingRounds(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'enfermeria']);

        $ingresoId = $request->query('ingreso_id');
        $filters = $ingresoId ? ['ingreso_id' => $ingresoId] : [];
        Response::json(RondaEnfermeria::all(1, 100, $filters));
    }

    public static function createNursingRound(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'enfermeria']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['ingreso_id', 'enfermero_id'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $ronda = RondaEnfermeria::create($data);
        Audit::log('rondas_enfermeria', (string) $ronda['id'], 'create', $ronda);

        Response::json($ronda, 201);
    }
}
