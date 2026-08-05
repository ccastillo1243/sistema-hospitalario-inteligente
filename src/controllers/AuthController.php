<?php

class AuthController
{
    public static function login(array $params, Request $request): void
    {
        $config = require __DIR__ . '/../config/config.php';
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');

        if ($email === '' || $password === '') {
            Response::error('Email y contraseña son requeridos', 400);
        }

        $pdo = Database::connection();

        $stmt = $pdo->prepare(
            "SELECT *, (bloqueado_hasta IS NOT NULL AND bloqueado_hasta > NOW()) AS esta_bloqueado
             FROM usuarios WHERE email = ? AND eliminado_en IS NULL"
        );
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && (bool) $usuario['esta_bloqueado']) {
            Response::error('Cuenta bloqueada temporalmente por múltiples intentos fallidos', 423);
        }

        $valido = $usuario
            && (bool) $usuario['activo']
            && password_verify($password, $usuario['password_hash']);

        self::registrarIntento($pdo, $usuario['id'] ?? null, $email, (bool) $valido);

        if (!$valido) {
            if ($usuario) {
                self::verificarBloqueo($pdo, $usuario['id'], $config['app']['max_login_attempts'], $config['app']['lockout_minutes']);
            }
            Response::error('Credenciales inválidas', 401);
        }

        $stmtRoles = $pdo->prepare(
            'SELECT r.nombre FROM roles r
             JOIN usuario_roles ur ON ur.rol_id = r.id
             WHERE ur.usuario_id = ?'
        );
        $stmtRoles->execute([$usuario['id']]);
        $roles = array_column($stmtRoles->fetchAll(), 'nombre');

        $pdo->prepare('UPDATE usuarios SET ultimo_login = NOW(), bloqueado_hasta = NULL WHERE id = ?')
            ->execute([$usuario['id']]);

        Auth::login($usuario, $roles);

        Response::json([
            'usuario' => [
                'id' => $usuario['id'],
                'email' => $usuario['email'],
                'nombre' => $usuario['nombre'],
                'apellido' => $usuario['apellido'],
                'roles' => $roles,
            ],
            'csrfToken' => Auth::csrfToken(),
        ]);
    }

    public static function logout(array $params, Request $request): void
    {
        Auth::logout();
        Response::json(['message' => 'Sesión cerrada']);
    }

    public static function me(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        Response::json([
            'usuario' => [
                'id' => Auth::id(),
                'email' => $_SESSION['email'],
                'nombre' => $_SESSION['nombre'],
                'apellido' => $_SESSION['apellido'],
                'roles' => Auth::roles(),
            ],
            'csrfToken' => Auth::csrfToken(),
        ]);
    }

    /**
     * Permite a cualquier usuario autenticado editar su propio nombre/apellido/email
     * y, opcionalmente, cambiar su contraseña (requiere la actual).
     */
    public static function updateProfile(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $data = $request->all();
        foreach (['nombre', 'apellido', 'email'] as $field) {
            if (empty($data[$field])) {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $pdo = Database::connection();
        $userId = Auth::id();

        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ?');
        $stmt->execute([$data['email'], $userId]);
        if ($stmt->fetch()) {
            Response::error('Ese correo ya está en uso por otro usuario', 409);
        }

        if (!empty($data['password_nueva'])) {
            if (empty($data['password_actual'])) {
                Response::error('Debes indicar tu contraseña actual para cambiarla', 400);
            }
            $stmt = $pdo->prepare('SELECT password_hash FROM usuarios WHERE id = ?');
            $stmt->execute([$userId]);
            $usuario = $stmt->fetch();
            if (!password_verify($data['password_actual'], $usuario['password_hash'])) {
                Response::error('La contraseña actual no es correcta', 401);
            }
            if (strlen((string) $data['password_nueva']) < 6) {
                Response::error('La nueva contraseña debe tener al menos 6 caracteres', 400);
            }
            $pdo->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($data['password_nueva'], PASSWORD_BCRYPT), $userId]);
        }

        $pdo->prepare('UPDATE usuarios SET nombre = ?, apellido = ?, email = ? WHERE id = ?')
            ->execute([$data['nombre'], $data['apellido'], $data['email'], $userId]);

        $_SESSION['nombre'] = $data['nombre'];
        $_SESSION['apellido'] = $data['apellido'];
        $_SESSION['email'] = $data['email'];

        Audit::log('usuarios', (string) $userId, 'update', ['nombre' => $data['nombre'], 'apellido' => $data['apellido'], 'email' => $data['email']]);
        Response::json(['message' => 'Perfil actualizado correctamente']);
    }

    /**
     * Genera un token de recuperación de contraseña. En un entorno real se enviaría
     * por correo; para esta demo se devuelve en la respuesta (documentado en PROGRESS.md).
     */
    public static function requestPasswordReset(array $params, Request $request): void
    {
        $email = trim((string) $request->input('email', ''));
        if ($email === '') {
            Response::error("El campo 'email' es requerido", 400);
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND eliminado_en IS NULL');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // Respuesta genérica aunque el usuario no exista, para no filtrar qué correos están registrados.
        if (!$usuario) {
            Response::json(['message' => 'Si el correo existe, se generó un enlace de recuperación']);
        }

        $token = bin2hex(random_bytes(32));
        $expiraEn = date('Y-m-d H:i:s', time() + 3600);

        $pdo->prepare('INSERT INTO tokens_recuperacion_password (usuario_id, token, expira_en) VALUES (?, ?, ?)')
            ->execute([$usuario['id'], $token, $expiraEn]);

        Response::json(['message' => 'Si el correo existe, se generó un enlace de recuperación', 'token_demo' => $token]);
    }

    public static function resetPassword(array $params, Request $request): void
    {
        $token = (string) $request->input('token', '');
        $password = (string) $request->input('password', '');

        if ($token === '' || strlen($password) < 6) {
            Response::error('Token y contraseña (mínimo 6 caracteres) son requeridos', 400);
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT * FROM tokens_recuperacion_password WHERE token = ? AND usado_en IS NULL AND expira_en > NOW()'
        );
        $stmt->execute([$token]);
        $registro = $stmt->fetch();

        if (!$registro) {
            Response::error('Token inválido o expirado', 400);
        }

        $pdo->prepare('UPDATE usuarios SET password_hash = ?, bloqueado_hasta = NULL WHERE id = ?')
            ->execute([password_hash($password, PASSWORD_BCRYPT), $registro['usuario_id']]);

        $pdo->prepare('UPDATE tokens_recuperacion_password SET usado_en = NOW() WHERE id = ?')
            ->execute([$registro['id']]);

        Response::json(['message' => 'Contraseña actualizada correctamente']);
    }

    private static function registrarIntento(PDO $pdo, ?int $usuarioId, string $email, bool $exitoso): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $pdo->prepare('INSERT INTO intentos_login (usuario_id, email, exitoso, ip) VALUES (?, ?, ?, ?)')
            ->execute([$usuarioId, $email, $exitoso ? 1 : 0, $ip]);
    }

    private static function verificarBloqueo(PDO $pdo, int $usuarioId, int $maxIntentos, int $minutosBloqueo): void
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS fallos FROM intentos_login
             WHERE usuario_id = ? AND exitoso = 0 AND creado_en > (NOW() - INTERVAL 15 MINUTE)'
        );
        $stmt->execute([$usuarioId]);
        $fallos = (int) $stmt->fetch()['fallos'];

        if ($fallos >= $maxIntentos) {
            $pdo->prepare('UPDATE usuarios SET bloqueado_hasta = (NOW() + INTERVAL ? MINUTE) WHERE id = ?')
                ->execute([$minutosBloqueo, $usuarioId]);
        }
    }
}
