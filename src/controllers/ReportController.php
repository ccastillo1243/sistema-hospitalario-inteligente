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

    public static function labOrdersPdf(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'medico', 'laboratorio']);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT ol.id, p.nombre, p.apellido, pe.nombre AS medico, ol.estado, ol.creado_en
             FROM ordenes_laboratorio ol
             JOIN pacientes p ON p.id = ol.paciente_id
             JOIN medicos m ON m.id = ol.medico_id
             JOIN personal pe ON pe.id = m.personal_id
             ORDER BY ol.creado_en DESC'
        )->fetchAll();

        $filas = array_map(fn($r) => array_values($r), $rows);
        self::registrarEjecucion('Órdenes de laboratorio', 'pdf');

        PdfReport::tabla(
            'Órdenes de Laboratorio',
            ['ID', 'Paciente', 'Apellido', 'Médico', 'Estado', 'Fecha'],
            $filas,
            'ordenes_laboratorio.pdf'
        );
    }

    public static function radiologyOrdersPdf(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'medico', 'radiologia']);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT o.id, p.nombre, p.apellido, te.nombre AS tipo_estudio, o.estado, o.creado_en
             FROM ordenes_radiologia o
             JOIN pacientes p ON p.id = o.paciente_id
             JOIN tipos_estudio_radiologico te ON te.id = o.tipo_estudio_id
             ORDER BY o.creado_en DESC'
        )->fetchAll();

        $filas = array_map(fn($r) => array_values($r), $rows);
        self::registrarEjecucion('Órdenes de radiología', 'pdf');

        PdfReport::tabla(
            'Órdenes de Radiología',
            ['ID', 'Paciente', 'Apellido', 'Tipo de estudio', 'Estado', 'Fecha'],
            $filas,
            'ordenes_radiologia.pdf'
        );
    }

    public static function admissionsPdf(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'medico', 'enfermeria', 'recepcion']);

        $pdo = Database::connection();
        $rows = $pdo->query(
            "SELECT i.id, p.nombre, p.apellido, c.codigo AS cama, i.motivo, i.ingresado_en,
                    IF(a.id IS NOT NULL, 'Sí', 'No') AS tiene_alta
             FROM ingresos i
             JOIN pacientes p ON p.id = i.paciente_id
             JOIN camas c ON c.id = i.cama_id
             LEFT JOIN altas a ON a.ingreso_id = i.id
             ORDER BY i.ingresado_en DESC"
        )->fetchAll();

        $filas = array_map(fn($r) => array_values($r), $rows);
        self::registrarEjecucion('Ingresos de hospitalización', 'pdf');

        PdfReport::tabla(
            'Ingresos de Hospitalización',
            ['ID', 'Paciente', 'Apellido', 'Cama', 'Motivo', 'Ingresado', 'Alta'],
            $filas,
            'ingresos_hospitalizacion.pdf'
        );
    }

    public static function emergencyCasesExcel(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin', 'medico', 'enfermeria', 'recepcion']);

        $pdo = Database::connection();
        $rows = $pdo->query(
            'SELECT c.id, p.nombre, p.apellido, nt.nombre AS triage, c.motivo, c.estado, c.llegada_en
             FROM casos_emergencia c
             JOIN pacientes p ON p.id = c.paciente_id
             JOIN niveles_triage nt ON nt.id = c.nivel_triage_id
             ORDER BY nt.prioridad ASC, c.llegada_en ASC'
        )->fetchAll();

        $filas = array_map(fn($r) => array_values($r), $rows);
        self::registrarEjecucion('Casos de emergencia', 'xlsx');

        ExcelReport::tabla(
            'Casos de Emergencia',
            ['ID', 'Paciente', 'Apellido', 'Triage', 'Motivo', 'Estado', 'Llegada'],
            $filas,
            'casos_emergencia.xlsx'
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
