<?php

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $config = require __DIR__ . '/../config/config.php';
            session_name($config['app']['session_name']);
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
            ]);
        }
    }

    public static function login(array $usuario, array $roles): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['roles'] = $roles;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    public static function id(): ?int
    {
        return $_SESSION['usuario_id'] ?? null;
    }

    public static function roles(): array
    {
        return $_SESSION['roles'] ?? [];
    }

    public static function hasRole(array $rolesPermitidos): bool
    {
        return count(array_intersect(self::roles(), $rolesPermitidos)) > 0;
    }

    public static function csrfToken(): string
    {
        return $_SESSION['csrf_token'] ?? '';
    }
}
