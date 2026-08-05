-- =========================================================================
-- Vistas para dashboards y reportes
-- =========================================================================

CREATE OR REPLACE VIEW vw_ocupacion_camas AS
SELECT
    h.piso,
    COUNT(*) AS total_camas,
    SUM(CASE WHEN c.estado = 'ocupada' THEN 1 ELSE 0 END) AS camas_ocupadas,
    SUM(CASE WHEN c.estado = 'libre' THEN 1 ELSE 0 END) AS camas_libres
FROM camas c
JOIN habitaciones h ON h.id = c.habitacion_id
GROUP BY h.piso;

CREATE OR REPLACE VIEW vw_citas_hoy AS
SELECT
    ci.id, ci.fecha_hora, ci.estado,
    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido,
    pe.nombre AS medico_nombre, pe.apellido AS medico_apellido
FROM citas ci
JOIN pacientes p ON p.id = ci.paciente_id
JOIN medicos m ON m.id = ci.medico_id
JOIN personal pe ON pe.id = m.personal_id
WHERE DATE(ci.fecha_hora) = CURDATE();

CREATE OR REPLACE VIEW vw_stock_bajo AS
SELECT
    med.id AS medicamento_id, med.nombre,
    COALESCE(SUM(l.cantidad), 0) AS stock_total
FROM medicamentos med
LEFT JOIN lotes_inventario l ON l.medicamento_id = med.id AND l.vencimiento >= CURDATE()
GROUP BY med.id, med.nombre
HAVING stock_total < 20;

CREATE OR REPLACE VIEW vw_examenes_pendientes AS
SELECT
    ol.id AS orden_id, ol.estado, ol.creado_en,
    p.nombre AS paciente_nombre, p.apellido AS paciente_apellido
FROM ordenes_laboratorio ol
JOIN pacientes p ON p.id = ol.paciente_id
WHERE ol.estado = 'pendiente';

CREATE OR REPLACE VIEW vw_facturacion_dia AS
SELECT
    DATE(f.creado_en) AS dia,
    COUNT(*) AS total_facturas,
    SUM(f.total) AS monto_total
FROM facturas f
WHERE DATE(f.creado_en) = CURDATE()
GROUP BY DATE(f.creado_en);
