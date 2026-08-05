<?php

/**
 * Notificaciones propias del usuario en sesión (cada quien ve solo las suyas).
 */
class NotificationController
{
    public static function index(array $params, Request $request): void
    {
        AuthMiddleware::handle();

        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT id, titulo, mensaje, leida, creado_en FROM notificaciones
             WHERE usuario_id = ? ORDER BY creado_en DESC LIMIT 20'
        );
        $stmt->execute([Auth::id()]);
        $items = $stmt->fetchAll();

        $stmtCount = $pdo->prepare('SELECT COUNT(*) AS c FROM notificaciones WHERE usuario_id = ? AND leida = 0');
        $stmtCount->execute([Auth::id()]);
        $noLeidas = (int) $stmtCount->fetch()['c'];

        Response::json(['items' => $items, 'noLeidas' => $noLeidas]);
    }

    public static function markRead(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $pdo = Database::connection();
        $pdo->prepare('UPDATE notificaciones SET leida = 1 WHERE id = ? AND usuario_id = ?')
            ->execute([(int) $params['id'], Auth::id()]);

        Response::json(['message' => 'Notificación marcada como leída']);
    }

    public static function markAllRead(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }

        $pdo = Database::connection();
        $pdo->prepare('UPDATE notificaciones SET leida = 1 WHERE usuario_id = ?')->execute([Auth::id()]);

        Response::json(['message' => 'Todas marcadas como leídas']);
    }
}
