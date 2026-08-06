<?php

/**
 * Administración de usuarios y roles. Exclusivo del rol admin.
 */
class UserController
{
    private static array $rolesAdmin = ['admin'];

    public static function index(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);

        $pdo = Database::connection();
        $rows = $pdo->query(
            "SELECT u.id, u.nombre, u.apellido, u.email, u.activo, u.ultimo_login, u.bloqueado_hasta,
                    COALESCE(GROUP_CONCAT(r.nombre ORDER BY r.nombre SEPARATOR ', '), '') AS roles
             FROM usuarios u
             LEFT JOIN usuario_roles ur ON ur.usuario_id = u.id
             LEFT JOIN roles r ON r.id = ur.rol_id
             WHERE u.eliminado_en IS NULL
             GROUP BY u.id, u.nombre, u.apellido, u.email, u.activo, u.ultimo_login, u.bloqueado_hasta
             ORDER BY u.nombre"
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function roles(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);

        $pdo = Database::connection();
        Response::json(['items' => $pdo->query('SELECT id, nombre FROM roles ORDER BY nombre')->fetchAll()]);
    }

    public static function store(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['nombre', 'apellido', 'email', 'password'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }
        if (strlen((string) $data['password']) < 6) {
            Response::error('La contraseña debe tener al menos 6 caracteres', 400);
        }

        $pdo = Database::connection();

        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            Response::error('Ya existe un usuario con ese correo', 409);
        }

        $roleIds = self::normalizeRoleIds($data['roles'] ?? []);

        $pdo->beginTransaction();
        try {
            $pdo->prepare('INSERT INTO usuarios (nombre, apellido, email, password_hash) VALUES (?, ?, ?, ?)')
                ->execute([$data['nombre'], $data['apellido'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT)]);
            $userId = (int) $pdo->lastInsertId();

            $stmtRole = $pdo->prepare('INSERT INTO usuario_roles (usuario_id, rol_id) VALUES (?, ?)');
            foreach ($roleIds as $rolId) {
                $stmtRole->execute([$userId, $rolId]);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        Audit::log('usuarios', (string) $userId, 'create', ['email' => $data['email'], 'roles' => $roleIds]);
        Response::json(['id' => $userId], 201);
    }

    public static function update(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $id = (int) $params['id'];
        $data = $request->all();

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE id = ? AND eliminado_en IS NULL');
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            Response::error('Usuario no encontrado', 404);
        }

        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, activo = ? WHERE id = ?')
                ->execute([
                    $data['nombre'] ?? '',
                    $data['apellido'] ?? '',
                    $data['email'] ?? '',
                    isset($data['activo']) && $data['activo'] ? 1 : 0,
                    $id,
                ]);

            if (!empty($data['password'])) {
                if (strlen((string) $data['password']) < 6) {
                    throw new InvalidArgumentException('La contraseña debe tener al menos 6 caracteres');
                }
                $pdo->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?')
                    ->execute([password_hash($data['password'], PASSWORD_BCRYPT), $id]);
            }

            if (isset($data['roles'])) {
                $roleIds = self::normalizeRoleIds($data['roles']);
                $pdo->prepare('DELETE FROM usuario_roles WHERE usuario_id = ?')->execute([$id]);
                $stmtRole = $pdo->prepare('INSERT INTO usuario_roles (usuario_id, rol_id) VALUES (?, ?)');
                foreach ($roleIds as $rolId) {
                    $stmtRole->execute([$id, $rolId]);
                }
            }

            $pdo->commit();
        } catch (InvalidArgumentException $e) {
            $pdo->rollBack();
            Response::error($e->getMessage(), 400);
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        Audit::log('usuarios', (string) $id, 'update', $data);
        Response::json(['message' => 'Usuario actualizado correctamente']);
    }

    public static function unlock(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $id = (int) $params['id'];
        $pdo = Database::connection();
        $pdo->prepare('UPDATE usuarios SET bloqueado_hasta = NULL WHERE id = ?')->execute([$id]);

        Audit::log('usuarios', (string) $id, 'update', ['accion' => 'desbloqueo manual']);
        Response::json(['message' => 'Usuario desbloqueado correctamente']);
    }

    public static function destroy(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesAdmin);
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $id = (int) $params['id'];
        if ($id === Auth::id()) {
            Response::error('No puedes eliminar tu propio usuario', 400);
        }

        $pdo = Database::connection();
        $pdo->prepare('UPDATE usuarios SET eliminado_en = NOW() WHERE id = ?')->execute([$id]);

        Audit::log('usuarios', (string) $id, 'delete', null);
        Response::json(['message' => 'Usuario eliminado correctamente']);
    }

    private static function normalizeRoleIds($roles): array
    {
        if (!is_array($roles)) {
            return [];
        }
        return array_values(array_unique(array_map('intval', $roles)));
    }
}
