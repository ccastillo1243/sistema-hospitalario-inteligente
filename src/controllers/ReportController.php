<?php

class ReportController
{
    private static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion', 'farmacia', 'laboratorio', 'radiologia', 'facturacion'];

    public static function patientsPdf(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(self::$rolesLectura);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT numero_expediente, nombre, apellido, documento_identidad, genero, telefono
             FROM pacientes WHERE eliminado_en IS NULL ORDER BY apellido'
        )->fetchAll();

        $filas = array_map(fn($r) => array_values($r), $rows);

        self::registrarEjecucion('Listado de pacientes', 'pdf');

        PdfReport::tabla(
            'Listado de Pacientes',
            ['Expediente', 'Nombre', 'Apellido', 'Documento', 'Género', 'Teléfono'],
            $filas,
            'pacientes.pdf'
        );
    }

    public static function invoicesExcel(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'facturacion']);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT f.id, p.nombre, p.apellido, f.subtotal, f.impuestos, f.total, f.estado, f.creado_en
             FROM facturas f JOIN pacientes p ON p.id = f.paciente_id
             ORDER BY f.creado_en DESC'
        )->fetchAll();

        $filas = array_map(fn($r) => array_values($r), $rows);

        self::registrarEjecucion('Listado de facturas', 'xlsx');

        ExcelReport::tabla(
            'Facturas',
            ['ID', 'Nombre', 'Apellido', 'Subtotal', 'Impuestos', 'Total', 'Estado', 'Fecha'],
            $filas,
            'facturas.xlsx'
        );
    }

    public static function lowStockPdf(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'farmacia']);

        $pdo = Database::connection();
        $rows = $pdo->query('SELECT medicamento_id, nombre, stock_total FROM vw_stock_bajo')->fetchAll();
        $filas = array_map(fn($r) => array_values($r), $rows);

        self::registrarEjecucion('Medicamentos con stock bajo', 'pdf');

        PdfReport::tabla(
            'Medicamentos con Stock Bajo',
            ['ID', 'Medicamento', 'Stock vigente'],
            $filas,
            'stock_bajo.pdf'
        );
    }

    private static function registrarEjecucion(string $nombreReporte, string $formato): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id FROM definiciones_reporte WHERE nombre = ?');
        $stmt->execute([$nombreReporte]);
        $reporte = $stmt->fetch();

        if (!$reporte) {
            $pdo->prepare('INSERT INTO definiciones_reporte (nombre, descripcion, modulo) VALUES (?, ?, ?)')
                ->execute([$nombreReporte, $nombreReporte, 'reportes']);
            $reporteId = (int) $pdo->lastInsertId();
        } else {
            $reporteId = (int) $reporte['id'];
        }

        $pdo->prepare('INSERT INTO ejecuciones_reporte (reporte_id, usuario_id, formato) VALUES (?, ?, ?)')
            ->execute([$reporteId, Auth::id(), $formato]);
    }
}
