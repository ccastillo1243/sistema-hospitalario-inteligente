<?php

class MedicationController extends CrudController
{
    protected static string $model = Medicamento::class;
    protected static string $auditName = 'medicamentos';
    protected static array $rolesLectura = ['admin', 'medico', 'farmacia', 'enfermeria'];
    protected static array $rolesEscritura = ['admin', 'farmacia'];
    protected static array $requiredOnCreate = ['categoria_id', 'nombre', 'presentacion'];

    public static function stock(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(static::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT m.id, m.nombre,
                    COALESCE(SUM(CASE WHEN l.vencimiento >= CURDATE() THEN l.cantidad ELSE 0 END), 0) AS stock_total
             FROM medicamentos m
             LEFT JOIN lotes_inventario l ON l.medicamento_id = m.id
             GROUP BY m.id, m.nombre
             ORDER BY m.nombre'
        )->fetchAll();

        Response::json(['items' => $rows, 'total' => count($rows)]);
    }
}
