-- =========================================================================
-- Datos de prueba complementarios: llena las tablas que seed.sql (Fase 1)
-- dejó vacías o con muy pocos registros, para que cada una de las 67 tablas
-- tenga información de ejemplo representativa. Usa variables de sesión
-- (LAST_INSERT_ID) para no depender de IDs fijos frágiles.
-- Ejecutar DESPUÉS de schema.sql + views.sql + triggers.sql + procedures.sql + seed.sql.
-- =========================================================================

-- =========================================================================
-- SEGURIDAD: permisos y rol_permisos
-- =========================================================================

INSERT INTO permisos (codigo, modulo, accion, descripcion) VALUES
    ('pacientes.leer', 'pacientes', 'leer', 'Ver pacientes'),
    ('pacientes.gestionar', 'pacientes', 'gestionar', 'Crear/editar/eliminar pacientes'),
    ('personal.leer', 'personal', 'leer', 'Ver personal'),
    ('personal.gestionar', 'personal', 'gestionar', 'Gestionar médicos y enfermería'),
    ('agenda.leer', 'agenda', 'leer', 'Ver agenda médica'),
    ('agenda.gestionar', 'agenda', 'gestionar', 'Gestionar citas'),
    ('consultas.leer', 'consultas', 'leer', 'Ver consultas'),
    ('consultas.gestionar', 'consultas', 'gestionar', 'Registrar consultas'),
    ('hospitalizacion.leer', 'hospitalizacion', 'leer', 'Ver hospitalización'),
    ('hospitalizacion.gestionar', 'hospitalizacion', 'gestionar', 'Gestionar ingresos/altas'),
    ('laboratorio.leer', 'laboratorio', 'leer', 'Ver laboratorio'),
    ('laboratorio.gestionar', 'laboratorio', 'gestionar', 'Gestionar órdenes de laboratorio'),
    ('farmacia.leer', 'farmacia', 'leer', 'Ver farmacia'),
    ('farmacia.gestionar', 'farmacia', 'gestionar', 'Gestionar recetas y dispensación'),
    ('radiologia.leer', 'radiologia', 'leer', 'Ver radiología'),
    ('radiologia.gestionar', 'radiologia', 'gestionar', 'Gestionar estudios de radiología'),
    ('facturacion.leer', 'facturacion', 'leer', 'Ver facturación'),
    ('facturacion.gestionar', 'facturacion', 'gestionar', 'Gestionar facturas y pagos'),
    ('emergencias.leer', 'emergencias', 'leer', 'Ver emergencias'),
    ('emergencias.gestionar', 'emergencias', 'gestionar', 'Gestionar casos de emergencia'),
    ('inventario.leer', 'inventario', 'leer', 'Ver inventario'),
    ('inventario.gestionar', 'inventario', 'gestionar', 'Gestionar inventario general'),
    ('reportes.leer', 'reportes', 'leer', 'Ver y descargar reportes'),
    ('usuarios.gestionar', 'seguridad', 'gestionar', 'Administrar usuarios y roles');

-- admin: todos los permisos
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT (SELECT id FROM roles WHERE nombre = 'admin'), id FROM permisos;

-- medico: lectura amplia + gestión clínica
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT (SELECT id FROM roles WHERE nombre = 'medico'), id FROM permisos
WHERE codigo IN ('pacientes.leer','agenda.leer','agenda.gestionar','consultas.leer','consultas.gestionar',
                  'hospitalizacion.leer','laboratorio.leer','farmacia.leer','radiologia.leer',
                  'emergencias.leer','emergencias.gestionar');

-- enfermeria
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT (SELECT id FROM roles WHERE nombre = 'enfermeria'), id FROM permisos
WHERE codigo IN ('pacientes.leer','hospitalizacion.leer','hospitalizacion.gestionar','agenda.leer');

-- farmacia
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT (SELECT id FROM roles WHERE nombre = 'farmacia'), id FROM permisos
WHERE codigo IN ('farmacia.leer','farmacia.gestionar','inventario.leer','inventario.gestionar','pacientes.leer');

-- laboratorio
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT (SELECT id FROM roles WHERE nombre = 'laboratorio'), id FROM permisos
WHERE codigo IN ('laboratorio.leer','laboratorio.gestionar','pacientes.leer');

-- radiologia
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT (SELECT id FROM roles WHERE nombre = 'radiologia'), id FROM permisos
WHERE codigo IN ('radiologia.leer','radiologia.gestionar','pacientes.leer');

-- facturacion
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT (SELECT id FROM roles WHERE nombre = 'facturacion'), id FROM permisos
WHERE codigo IN ('facturacion.leer','facturacion.gestionar','pacientes.leer');

-- recepcion
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT (SELECT id FROM roles WHERE nombre = 'recepcion'), id FROM permisos
WHERE codigo IN ('pacientes.leer','pacientes.gestionar','agenda.leer','agenda.gestionar');

-- =========================================================================
-- AUDITORÍA: notificaciones
-- =========================================================================

INSERT INTO notificaciones (usuario_id, titulo, mensaje, leida)
SELECT id, 'Bienvenido al sistema', 'Tu cuenta ha sido activada correctamente.', 1 FROM usuarios;

INSERT INTO notificaciones (usuario_id, titulo, mensaje, leida)
SELECT id, 'Recordatorio', 'Revisa las tareas pendientes de tu módulo.', 0
FROM usuarios WHERE email IN ('medico1@hospital.com', 'farmacia@hospital.com', 'laboratorio@hospital.com');

-- =========================================================================
-- PACIENTES: contactos, aseguradoras, seguros, alergias
-- =========================================================================

INSERT INTO contactos_paciente (paciente_id, nombre, parentesco, telefono, es_emergencia) VALUES
    (1, 'Ana Martínez', 'Esposa', '0999-111222', 1),
    (2, 'Pedro Hernández', 'Hermano', '0999-222333', 1),
    (3, 'Marta Díaz', 'Madre', '0999-333444', 1),
    (4, 'Jorge Ortega', 'Esposo', '0999-444555', 1),
    (5, 'Lucía Castro', 'Hija', '0999-555666', 0);

INSERT INTO aseguradoras (nombre, telefono) VALUES
    ('Seguros Vida Plus', '02-2345678'),
    ('Salud Total', '02-3456789');

INSERT INTO seguros_paciente (paciente_id, aseguradora_id, numero_poliza, vigente_desde, vigente_hasta) VALUES
    (1, 1, 'POL-0001', '2025-01-01', '2027-01-01'),
    (2, 2, 'POL-0002', '2025-03-01', '2026-03-01');

INSERT INTO alergias (nombre, descripcion) VALUES
    ('Penicilina', 'Alergia a antibióticos betalactámicos'),
    ('Polen', 'Rinitis alérgica estacional'),
    ('Látex', 'Reacción cutánea al contacto con látex'),
    ('Aspirina', 'Intolerancia a AINEs'),
    ('Mariscos', 'Alergia alimentaria');

INSERT INTO paciente_alergias (paciente_id, alergia_id, severidad) VALUES
    (1, 1, 'alta'),
    (2, 3, 'media'),
    (3, 4, 'baja'),
    (5, 5, 'alta');

-- =========================================================================
-- AGENDA MÉDICA: disponibilidad + historial de las citas ya existentes
-- =========================================================================

INSERT INTO disponibilidad_medico (medico_id, dia_semana, hora_inicio, hora_fin) VALUES
    (1, 1, '08:00:00', '13:00:00'),
    (1, 3, '08:00:00', '13:00:00'),
    (1, 5, '08:00:00', '12:00:00'),
    (2, 2, '14:00:00', '18:00:00'),
    (2, 4, '14:00:00', '18:00:00');

INSERT INTO historial_estado_cita (cita_id, estado)
SELECT id, estado FROM citas WHERE id NOT IN (SELECT DISTINCT cita_id FROM historial_estado_cita);

-- =========================================================================
-- CONSULTAS adicionales + signos vitales + diagnósticos
-- =========================================================================

INSERT INTO consultas (paciente_id, medico_id, motivo, notas, fecha) VALUES
    (3, 1, 'Dolor abdominal', 'Paciente refiere dolor leve en epigastrio, sin fiebre.', NOW() - INTERVAL 2 DAY),
    (4, 2, 'Control rutinario', 'Signos vitales dentro de parámetros normales.', NOW() - INTERVAL 1 DAY);

SET @consulta_dolor := (SELECT id FROM consultas WHERE paciente_id = 3 ORDER BY id DESC LIMIT 1);
SET @consulta_control := (SELECT id FROM consultas WHERE paciente_id = 4 ORDER BY id DESC LIMIT 1);

INSERT INTO signos_vitales (consulta_id, paciente_id, presion_arterial, frecuencia_cardiaca, temperatura, saturacion_o2) VALUES
    (@consulta_dolor, 3, '118/76', 78, 36.7, 98.0),
    (@consulta_control, 4, '120/80', 72, 36.5, 99.0);

INSERT INTO diagnosticos (consulta_id, codigo_cie10, descripcion, tipo) VALUES
    (@consulta_dolor, 'R10.4', 'Dolor abdominal no especificado', 'principal'),
    (@consulta_control, 'Z00.0', 'Examen médico general', 'principal');

-- =========================================================================
-- HOSPITALIZACIÓN: ingresos, altas, traslados, rondas de enfermería
-- =========================================================================

-- Ingreso activo (sin alta) en cama 1
INSERT INTO ingresos (paciente_id, cama_id, medico_id, motivo) VALUES (2, 1, 1, 'Observación por deshidratación');
SET @ingreso_activo := LAST_INSERT_ID();

INSERT INTO rondas_enfermeria (ingreso_id, enfermero_id, notas) VALUES
    (@ingreso_activo, 1, 'Paciente estable, hidratación en curso.'),
    (@ingreso_activo, 2, 'Signos vitales normales, continúa en observación.');

-- Ingreso con traslado (cama 3 -> cama 2, cama 3 queda libre)
INSERT INTO ingresos (paciente_id, cama_id, medico_id, motivo) VALUES (4, 3, 1, 'Postoperatorio menor');
SET @ingreso_traslado := LAST_INSERT_ID();

INSERT INTO traslados (ingreso_id, cama_destino_id, motivo) VALUES (@ingreso_traslado, 2, 'Cambio a habitación con mejor ventilación');

-- Ingreso ya finalizado con alta (usa temporalmente cama 3, que quedó libre tras el traslado anterior)
INSERT INTO ingresos (paciente_id, cama_id, medico_id, motivo) VALUES (5, 3, 2, 'Chequeo por caída leve');
SET @ingreso_alta := LAST_INSERT_ID();

INSERT INTO altas (ingreso_id, resumen, tipo) VALUES
    (@ingreso_alta, 'Paciente sin lesiones de gravedad, se retira con indicaciones de reposo.', 'medica');

-- =========================================================================
-- LABORATORIO: una orden completada y una pendiente
-- =========================================================================

INSERT INTO ordenes_laboratorio (paciente_id, medico_id, estado) VALUES (1, 1, 'pendiente');
SET @orden_lab_completa := LAST_INSERT_ID();

INSERT INTO muestras_laboratorio (orden_id, tipo_examen_id, codigo_barras, tipo_muestra) VALUES
    (@orden_lab_completa, 1, CONCAT('MU-SEED-', @orden_lab_completa), 'Sangre');
SET @muestra_lab := LAST_INSERT_ID();

INSERT INTO resultados_laboratorio (muestra_id, parametro_id, valor) VALUES
    (@muestra_lab, 1, '13.8');

UPDATE ordenes_laboratorio SET estado = 'completada' WHERE id = @orden_lab_completa;

INSERT INTO archivos_resultado (resultado_id, url)
SELECT id, CONCAT('/uploads/resultados/resultado_', id, '.pdf') FROM resultados_laboratorio WHERE muestra_id = @muestra_lab;

-- Orden pendiente, sin muestra aún (para probar el flujo desde cero en la UI)
INSERT INTO ordenes_laboratorio (paciente_id, medico_id, estado) VALUES (3, 2, 'pendiente');

-- =========================================================================
-- FARMACIA: receta dispensada + receta pendiente
-- =========================================================================

INSERT INTO recetas (paciente_id, medico_id) VALUES (2, 1);
SET @receta_dispensada := LAST_INSERT_ID();

INSERT INTO receta_items (receta_id, medicamento_id, cantidad, indicaciones) VALUES
    (@receta_dispensada, 1, 12, 'Una tableta cada 8 horas por 4 días');
SET @item_dispensado := LAST_INSERT_ID();

-- Nota: NO se resta manualmente del lote; el trigger trg_dispensacion_after_insert
-- (Fase 1) ya descuenta automáticamente del lote más próximo a vencer.
INSERT INTO dispensaciones (receta_item_id, farmaceutico_id, cantidad) VALUES (@item_dispensado, 7, 12);

-- Receta sin dispensar (para demostrar el flujo pendiente en la UI)
INSERT INTO recetas (paciente_id, medico_id) VALUES (4, 2);
SET @receta_pendiente := LAST_INSERT_ID();

INSERT INTO receta_items (receta_id, medicamento_id, cantidad, indicaciones) VALUES
    (@receta_pendiente, 1, 6, 'Una tableta cada 12 horas por 3 días');

-- =========================================================================
-- RADIOLOGÍA: orden completa (informe) + orden pendiente
-- =========================================================================

INSERT INTO ordenes_radiologia (paciente_id, medico_id, tipo_estudio_id, estado) VALUES (1, 1, 1, 'pendiente');
SET @orden_rad_completa := LAST_INSERT_ID();

INSERT INTO estudios_radiologicos (orden_id, realizado_por, observaciones) VALUES
    (@orden_rad_completa, 8, 'Estudio realizado sin incidentes.');
SET @estudio_rad := LAST_INSERT_ID();

UPDATE ordenes_radiologia SET estado = 'en_proceso' WHERE id = @orden_rad_completa;

INSERT INTO informes_radiologicos (estudio_id, radiologo_id, contenido) VALUES
    (@estudio_rad, 8, 'Radiografía de tórax sin hallazgos patológicos relevantes.');

UPDATE ordenes_radiologia SET estado = 'completada' WHERE id = @orden_rad_completa;

INSERT INTO ordenes_radiologia (paciente_id, medico_id, tipo_estudio_id, estado) VALUES (3, 2, 2, 'pendiente');

-- =========================================================================
-- FACTURACIÓN: una factura pagada y una pendiente
-- =========================================================================

INSERT INTO facturas (paciente_id, estado, subtotal, impuestos, total) VALUES (1, 'pendiente', 25.00, 3.00, 28.00);
SET @factura_pagada := LAST_INSERT_ID();

INSERT INTO factura_items (factura_id, servicio_id, cantidad, precio_unitario, subtotal) VALUES
    (@factura_pagada, 1, 1, 25.00, 25.00);

INSERT INTO pagos (factura_id, metodo_pago_id, monto) VALUES (@factura_pagada, 1, 28.00);
UPDATE facturas SET estado = 'pagada' WHERE id = @factura_pagada;

INSERT INTO facturas (paciente_id, estado, subtotal, impuestos, total) VALUES (3, 'pendiente', 15.00, 1.80, 16.80);
SET @factura_pendiente := LAST_INSERT_ID();

INSERT INTO factura_items (factura_id, servicio_id, cantidad, precio_unitario, subtotal) VALUES
    (@factura_pendiente, 2, 1, 15.00, 15.00);

-- =========================================================================
-- EMERGENCIAS: un caso atendido y uno en espera
-- =========================================================================

INSERT INTO casos_emergencia (paciente_id, nivel_triage_id, motivo, estado) VALUES (5, 3, 'Esguince de tobillo', 'en_espera');
SET @caso_atendido := LAST_INSERT_ID();

INSERT INTO atenciones_emergencia (caso_id, medico_id, notas) VALUES
    (@caso_atendido, 2, 'Se inmoviliza tobillo y se receta antiinflamatorio.');

UPDATE casos_emergencia SET estado = 'atendido' WHERE id = @caso_atendido;

INSERT INTO casos_emergencia (paciente_id, nivel_triage_id, motivo, estado) VALUES (2, 4, 'Fiebre y malestar general', 'en_espera');

-- =========================================================================
-- INVENTARIO GENERAL: movimiento adicional (entrada)
-- =========================================================================

INSERT INTO movimientos_inventario (existencia_id, tipo, cantidad, motivo) VALUES
    (1, 'entrada', 20, 'Reposición mensual de insumos');
UPDATE existencias_inventario SET cantidad = cantidad + 20 WHERE articulo_id = 1;

INSERT INTO articulos_inventario (almacen_id, nombre, categoria, unidad_medida) VALUES
    (1, 'Mascarillas quirúrgicas', 'Insumos médicos', 'caja'),
    (1, 'Jeringas 5ml', 'Insumos médicos', 'unidad');

INSERT INTO existencias_inventario (articulo_id, cantidad)
SELECT id, 100 FROM articulos_inventario WHERE nombre IN ('Mascarillas quirúrgicas', 'Jeringas 5ml');
