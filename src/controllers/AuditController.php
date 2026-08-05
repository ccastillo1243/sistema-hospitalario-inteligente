<?php

/**
 * Visor del historial de auditoría (bitácora_auditoria). Exclusivo admin.
 */
class AuditController
{
    public static function index(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin']);

        $tabla = trim((string) $request->query('tabla', ''));
        $accion = trim((string) $request->query('accion', ''));
        $q = trim((string) $request->query('q', ''));

        $conditions = [];
        $paramsSql = [];

        if ($tabla !== '') {
            $conditions[] = 'b.tabla = ?';
            $paramsSql[] = $tabla;
        }
        if ($accion !== '') {
            $conditions[] = 'b.accion = ?';
            $paramsSql[] = $accion;
        }
        if ($q !== '') {
            $conditions[] = '(u.nombre LIKE ? OR u.apellido LIKE ? OR b.registro_id LIKE ?)';
            $paramsSql[] = "%$q%";
            $paramsSql[] = "%$q%";
            $paramsSql[] = "%$q%";
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            "SELECT b.id, b.tabla, b.registro_id, b.accion, b.datos_json, b.creado_en,
                    u.nombre AS usuario_nombre, u.apellido AS usuario_apellido
             FROM bitacora_auditoria b
             LEFT JOIN usuarios u ON u.id = b.usuario_id
             $where
             ORDER BY b.creado_en DESC
             LIMIT 200"
        );
        $stmt->execute($paramsSql);
        $items = $stmt->fetchAll();

        $tablas = $pdo->query('SELECT DISTINCT tabla FROM bitacora_auditoria ORDER BY tabla')->fetchAll(PDO::FETCH_COLUMN);

        Response::json(['items' => $items, 'total' => count($items), 'tablas' => $tablas]);
    }
}
