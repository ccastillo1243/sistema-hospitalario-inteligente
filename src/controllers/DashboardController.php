<?php

class DashboardController
{
    private static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion', 'farmacia', 'laboratorio', 'radiologia', 'facturacion'];

    public static function bedOccupancy(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query('SELECT * FROM vw_ocupacion_camas')->fetchAll();
        Response::json(['items' => $rows]);
    }

    public static function appointmentsToday(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query('SELECT * FROM vw_citas_hoy')->fetchAll();
        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function lowStock(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query('SELECT * FROM vw_stock_bajo')->fetchAll();
        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function pendingLabs(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query('SELECT * FROM vw_examenes_pendientes')->fetchAll();
        Response::json(['items' => $rows, 'total' => count($rows)]);
    }

    public static function billingToday(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query('SELECT * FROM vw_facturacion_dia')->fetchAll();
        Response::json(['items' => $rows]);
    }

    public static function emergencyByPriority(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            "SELECT nt.nombre, COUNT(c.id) AS total
             FROM niveles_triage nt
             LEFT JOIN casos_emergencia c ON c.nivel_triage_id = nt.id AND c.estado != 'atendido'
             GROUP BY nt.id, nt.nombre
             ORDER BY nt.prioridad"
        )->fetchAll();
        Response::json(['items' => $rows]);
    }

    public static function summary(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();

        $totalPacientes = (int) $pdo->query('SELECT COUNT(*) AS c FROM pacientes WHERE eliminado_en IS NULL')->fetch()['c'];
        $camasOcupadas = (int) $pdo->query("SELECT COUNT(*) AS c FROM camas WHERE estado = 'ocupada'")->fetch()['c'];
        $camasTotal = (int) $pdo->query('SELECT COUNT(*) AS c FROM camas')->fetch()['c'];
        $citasHoy = (int) $pdo->query('SELECT COUNT(*) AS c FROM vw_citas_hoy')->fetch()['c'];
        $examenesPendientes = (int) $pdo->query('SELECT COUNT(*) AS c FROM vw_examenes_pendientes')->fetch()['c'];
        $casosEnEspera = (int) $pdo->query("SELECT COUNT(*) AS c FROM casos_emergencia WHERE estado != 'atendido'")->fetch()['c'];

        Response::json([
            'totalPacientes' => $totalPacientes,
            'camasOcupadas' => $camasOcupadas,
            'camasTotal' => $camasTotal,
            'citasHoy' => $citasHoy,
            'examenesPendientes' => $examenesPendientes,
            'casosEnEspera' => $casosEnEspera,
        ]);
    }
}
