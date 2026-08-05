<?php

class AppointmentController
{
    private static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion'];
    private static array $rolesEscritura = ['admin', 'recepcion'];

    public static function index(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT c.id, c.fecha_hora, c.estado, c.motivo,
                    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    pe.nombre AS medico_nombre, pe.apellido AS medico_apellido,
                    tc.nombre AS tipo_cita
             FROM citas c
             JOIN pacientes p ON p.id = c.paciente_id
             JOIN medicos m ON m.id = c.medico_id
             JOIN personal pe ON pe.id = m.personal_id
             JOIN tipos_cita tc ON tc.id = c.tipo_cita_id
             ORDER BY c.fecha_hora DESC'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function store(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['paciente_id', 'medico_id', 'tipo_cita_id', 'fecha_hora'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $cita = Cita::create([
            'paciente_id' => $data['paciente_id'],
            'medico_id' => $data['medico_id'],
            'tipo_cita_id' => $data['tipo_cita_id'],
            'fecha_hora' => $data['fecha_hora'],
            'motivo' => $data['motivo'] ?? null,
            'estado' => 'programada',
        ]);

        $pdo = Database::connection();
        $pdo->prepare('INSERT INTO historial_estado_cita (cita_id, estado) VALUES (?, ?)')
            ->execute([$cita['id'], 'programada']);

        Audit::log('citas', (string) $cita['id'], 'create', $cita);
        Response::json($cita, 201);
    }

    public static function update(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([...self::$rolesEscritura, 'medico']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $id = (int) $params['id'];
        if (!Cita::find($id)) {
            Response::error('Cita no encontrada', 404);
        }

        $data = $request->all();
        $cita = Cita::update($id, $data);

        if (!empty($data['estado'])) {
            $pdo = Database::connection();
            $pdo->prepare('INSERT INTO historial_estado_cita (cita_id, estado) VALUES (?, ?)')
                ->execute([$id, $data['estado']]);
        }

        Audit::log('citas', (string) $id, 'update', $cita);
        Response::json($cita);
    }

    public static function destroy(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $id = (int) $params['id'];
        if (!Cita::find($id)) {
            Response::error('Cita no encontrada', 404);
        }

        Cita::delete($id);
        Audit::log('citas', (string) $id, 'delete', null);

        Response::json(['message' => 'Eliminado correctamente']);
    }
}
