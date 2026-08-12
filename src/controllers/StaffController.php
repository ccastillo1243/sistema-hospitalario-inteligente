<?php

class StaffController
{
    private static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion', 'farmacia', 'laboratorio', 'radiologia', 'facturacion'];
    private static array $rolesAdmin = ['admin'];

    /**
     * Lista plana de personal (cualquier tipo), usada para llenar combos
     * como "realizado por" / "radiólogo" donde no aplica un rol clínico
     * específico (médico o enfermero).
     */
    public static function personal(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT id, nombre, apellido, tipo FROM personal WHERE eliminado_en IS NULL ORDER BY nombre'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function doctors(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $search = trim((string) $request->query('q', ''));
        $pdo = Database::connection();

        $sql = 'SELECT m.id, p.id AS personal_id, p.nombre, p.apellido, p.telefono, p.cedula_profesional,
                    GROUP_CONCAT(e.nombre SEPARATOR ", ") AS especialidades
             FROM medicos m
             JOIN personal p ON p.id = m.personal_id
             LEFT JOIN medico_especialidades me ON me.medico_id = m.id
             LEFT JOIN especialidades e ON e.id = me.especialidad_id
             WHERE p.eliminado_en IS NULL';
        $paramsSql = [];
        if ($search !== '') {
            $sql .= ' AND (p.nombre LIKE ? OR p.apellido LIKE ? OR p.cedula_profesional LIKE ?)';
            $paramsSql = ["%$search%", "%$search%", "%$search%"];
        }
        $sql .= ' GROUP BY m.id, p.id, p.nombre, p.apellido, p.telefono, p.cedula_profesional ORDER BY m.id DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($paramsSql);
        $rows = $stmt->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function createDoctor(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['nombre', 'apellido', 'especialidad_id'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $pdo->prepare(
                'INSERT INTO personal (departamento_id, nombre, apellido, cedula_profesional, telefono, tipo) VALUES (?, ?, ?, ?, ?, ?)'
            )->execute([
                $data['departamento_id'] ?? null,
                $data['nombre'],
                $data['apellido'],
                $data['cedula_profesional'] ?? null,
                $data['telefono'] ?? null,
                'medico',
            ]);
            $personalId = (int) $pdo->lastInsertId();

            $pdo->prepare('INSERT INTO medicos (personal_id) VALUES (?)')->execute([$personalId]);
            $medicoId = (int) $pdo->lastInsertId();

            $pdo->prepare('INSERT INTO medico_especialidades (medico_id, especialidad_id) VALUES (?, ?)')
                ->execute([$medicoId, $data['especialidad_id']]);

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        Audit::log('medicos', (string) $medicoId, 'create', $data);
        Response::json(['id' => $medicoId, 'personal_id' => $personalId], 201);
    }

    public static function updateDoctor(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $id = (int) $params['id'];
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT personal_id FROM medicos WHERE id = ?');
        $stmt->execute([$id]);
        $medico = $stmt->fetch();
        if (!$medico) {
            Response::error('Médico no encontrado', 404);
        }

        $data = $request->all();
        $pdo->prepare('UPDATE personal SET nombre = ?, apellido = ?, cedula_profesional = ?, telefono = ? WHERE id = ?')
            ->execute([
                $data['nombre'] ?? '',
                $data['apellido'] ?? '',
                $data['cedula_profesional'] ?? null,
                $data['telefono'] ?? null,
                $medico['personal_id'],
            ]);

        Audit::log('medicos', (string) $id, 'update', $data);
        Response::json(['id' => $id, 'personal_id' => $medico['personal_id']]);
    }

    public static function deleteDoctor(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $id = (int) $params['id'];
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT personal_id FROM medicos WHERE id = ?');
        $stmt->execute([$id]);
        $medico = $stmt->fetch();
        if (!$medico) {
            Response::error('Médico no encontrado', 404);
        }

        $pdo->prepare('UPDATE personal SET eliminado_en = NOW() WHERE id = ?')->execute([$medico['personal_id']]);
        Audit::log('medicos', (string) $id, 'delete', null);

        Response::json(['message' => 'Eliminado correctamente']);
    }

    public static function nurses(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $search = trim((string) $request->query('q', ''));
        $pdo = Database::connection();

        $sql = 'SELECT en.id, p.id AS personal_id, p.nombre, p.apellido, p.telefono, p.cedula_profesional
             FROM enfermeros en
             JOIN personal p ON p.id = en.personal_id
             WHERE p.eliminado_en IS NULL';
        $paramsSql = [];
        if ($search !== '') {
            $sql .= ' AND (p.nombre LIKE ? OR p.apellido LIKE ? OR p.cedula_profesional LIKE ?)';
            $paramsSql = ["%$search%", "%$search%", "%$search%"];
        }
        $sql .= ' ORDER BY en.id DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($paramsSql);
        $rows = $stmt->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function createNurse(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['nombre', 'apellido'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $pdo->prepare(
                'INSERT INTO personal (departamento_id, nombre, apellido, cedula_profesional, telefono, tipo) VALUES (?, ?, ?, ?, ?, ?)'
            )->execute([
                $data['departamento_id'] ?? null,
                $data['nombre'],
                $data['apellido'],
                $data['cedula_profesional'] ?? null,
                $data['telefono'] ?? null,
                'enfermeria',
            ]);
            $personalId = (int) $pdo->lastInsertId();

            $pdo->prepare('INSERT INTO enfermeros (personal_id) VALUES (?)')->execute([$personalId]);
            $enfermeroId = (int) $pdo->lastInsertId();

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        Audit::log('enfermeros', (string) $enfermeroId, 'create', $data);
        Response::json(['id' => $enfermeroId, 'personal_id' => $personalId], 201);
    }

    public static function updateNurse(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $id = (int) $params['id'];
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT personal_id FROM enfermeros WHERE id = ?');
        $stmt->execute([$id]);
        $enfermero = $stmt->fetch();
        if (!$enfermero) {
            Response::error('Enfermero no encontrado', 404);
        }

        $data = $request->all();
        $pdo->prepare('UPDATE personal SET nombre = ?, apellido = ?, cedula_profesional = ?, telefono = ? WHERE id = ?')
            ->execute([
                $data['nombre'] ?? '',
                $data['apellido'] ?? '',
                $data['cedula_profesional'] ?? null,
                $data['telefono'] ?? null,
                $enfermero['personal_id'],
            ]);

        Audit::log('enfermeros', (string) $id, 'update', $data);
        Response::json(['id' => $id, 'personal_id' => $enfermero['personal_id']]);
    }

    public static function deleteNurse(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $id = (int) $params['id'];
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT personal_id FROM enfermeros WHERE id = ?');
        $stmt->execute([$id]);
        $enfermero = $stmt->fetch();
        if (!$enfermero) {
            Response::error('Enfermero no encontrado', 404);
        }

        $pdo->prepare('UPDATE personal SET eliminado_en = NOW() WHERE id = ?')->execute([$enfermero['personal_id']]);
        Audit::log('enfermeros', (string) $id, 'delete', null);

        Response::json(['message' => 'Eliminado correctamente']);
    }
}
