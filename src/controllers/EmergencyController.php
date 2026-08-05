<?php

class EmergencyController
{
    private static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion'];
    private static array $rolesEscritura = ['admin', 'enfermeria', 'recepcion'];

    /**
     * Lista los casos ordenados por prioridad de triage (menor número = más urgente)
     * y luego por hora de llegada, tal como funcionaría una sala de emergencias real.
     */
    public static function cases(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT c.id, c.motivo, c.estado, c.llegada_en,
                    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    nt.nombre AS nivel_triage, nt.prioridad
             FROM casos_emergencia c
             JOIN pacientes p ON p.id = c.paciente_id
             JOIN niveles_triage nt ON nt.id = c.nivel_triage_id
             ORDER BY nt.prioridad ASC, c.llegada_en ASC'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function createCase(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['paciente_id', 'nivel_triage_id', 'motivo'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $caso = CasoEmergencia::create([
            'paciente_id' => $data['paciente_id'],
            'nivel_triage_id' => $data['nivel_triage_id'],
            'motivo' => $data['motivo'],
            'estado' => 'en_espera',
        ]);
        Audit::log('casos_emergencia', (string) $caso['id'], 'create', $caso);

        Response::json($caso, 201);
    }

    public static function attend(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'medico']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['caso_id', 'medico_id'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT estado FROM casos_emergencia WHERE id = ?");
        $stmt->execute([$data['caso_id']]);
        $caso = $stmt->fetch();
        if (!$caso) {
            Response::error('Caso no encontrado', 404);
        }
        if ($caso['estado'] === 'atendido') {
            Response::error('Este caso ya fue atendido', 409);
        }

        $atencion = AtencionEmergencia::create([
            'caso_id' => $data['caso_id'],
            'medico_id' => $data['medico_id'],
            'notas' => $data['notas'] ?? null,
        ]);

        $pdo->prepare("UPDATE casos_emergencia SET estado = 'atendido' WHERE id = ?")
            ->execute([$data['caso_id']]);

        Audit::log('atenciones_emergencia', (string) $atencion['id'], 'create', $atencion);
        Response::json($atencion, 201);
    }
}
