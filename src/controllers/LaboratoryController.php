<?php

class LaboratoryController
{
    private static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'laboratorio'];
    private static array $rolesEscritura = ['admin', 'medico', 'laboratorio'];

    public static function orders(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT ol.id, ol.estado, ol.creado_en,
                    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
                    pe.nombre AS medico_nombre, pe.apellido AS medico_apellido
             FROM ordenes_laboratorio ol
             JOIN pacientes p ON p.id = ol.paciente_id
             JOIN medicos m ON m.id = ol.medico_id
             JOIN personal pe ON pe.id = m.personal_id
             ORDER BY ol.creado_en DESC'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function createOrder(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesEscritura);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['paciente_id', 'medico_id'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $orden = OrdenLaboratorio::create([
            'paciente_id' => $data['paciente_id'],
            'medico_id' => $data['medico_id'],
            'estado' => 'pendiente',
        ]);
        Audit::log('ordenes_laboratorio', (string) $orden['id'], 'create', $orden);

        Response::json($orden, 201);
    }

    public static function samples(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $ordenId = $request->query('orden_id');
        $filters = $ordenId ? ['orden_id' => $ordenId] : [];
        Response::json(MuestraLaboratorio::all(1, 100, $filters));
    }

    public static function createSample(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'laboratorio']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['orden_id', 'tipo_examen_id', 'tipo_muestra'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $codigo = 'MU-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $muestra = MuestraLaboratorio::create([
            'orden_id' => $data['orden_id'],
            'tipo_examen_id' => $data['tipo_examen_id'],
            'codigo_barras' => $codigo,
            'tipo_muestra' => $data['tipo_muestra'],
        ]);
        Audit::log('muestras_laboratorio', (string) $muestra['id'], 'create', $muestra);

        Response::json($muestra, 201);
    }

    public static function results(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $muestraId = $request->query('muestra_id');
        $filters = $muestraId ? ['muestra_id' => $muestraId] : [];
        Response::json(ResultadoLaboratorio::all(1, 100, $filters));
    }

    public static function createResult(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'laboratorio']);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['muestra_id', 'parametro_id', 'valor'] as $field) {
            if (empty($data[$field]) && $data[$field] !== '0') {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $resultado = ResultadoLaboratorio::create([
            'muestra_id' => $data['muestra_id'],
            'parametro_id' => $data['parametro_id'],
            'valor' => $data['valor'],
        ]);
        Audit::log('resultados_laboratorio', (string) $resultado['id'], 'create', $resultado);

        self::actualizarEstadoOrdenSiCompleta((int) $data['muestra_id']);

        Response::json($resultado, 201);
    }

    private static function actualizarEstadoOrdenSiCompleta(int $muestraId): void
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare(
            'SELECT ml.orden_id, ml.tipo_examen_id,
                    (SELECT COUNT(*) FROM parametros_examen WHERE tipo_examen_id = ml.tipo_examen_id) AS total_parametros,
                    (SELECT COUNT(*) FROM resultados_laboratorio r WHERE r.muestra_id = ml.id) AS total_resultados
             FROM muestras_laboratorio ml WHERE ml.id = ?'
        );
        $stmt->execute([$muestraId]);
        $info = $stmt->fetch();

        if ($info && (int) $info['total_resultados'] >= (int) $info['total_parametros'] && $info['total_parametros'] > 0) {
            $pdo->prepare("UPDATE ordenes_laboratorio SET estado = 'completada' WHERE id = ?")
                ->execute([$info['orden_id']]);
        }
    }
}
