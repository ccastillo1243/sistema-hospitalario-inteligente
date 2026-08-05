-- =========================================================================
-- Una transacción adicional por módulo, para que cada listado (citas,
-- órdenes de laboratorio, recetas, órdenes de radiología, facturas, casos
-- de emergencia) muestre al menos 2-3 ejemplos visibles en la UI.
-- =========================================================================

-- Agenda médica: una tercera cita
INSERT INTO citas (paciente_id, medico_id, tipo_cita_id, fecha_hora, estado, motivo) VALUES
    (5, 2, 2, NOW() + INTERVAL 2 DAY, 'programada', 'Control postoperatorio');
SET @cita3 := LAST_INSERT_ID();
INSERT INTO historial_estado_cita (cita_id, estado) VALUES (@cita3, 'programada');

-- Laboratorio: una tercera orden (pendiente, sin muestra, para variar los estados visibles)
INSERT INTO ordenes_laboratorio (paciente_id, medico_id, estado) VALUES (5, 2, 'pendiente');

-- Farmacia: una tercera receta con ítem sin dispensar
INSERT INTO recetas (paciente_id, medico_id) VALUES (5, 2);
SET @receta3 := LAST_INSERT_ID();
INSERT INTO receta_items (receta_id, medicamento_id, cantidad, indicaciones) VALUES
    (@receta3, 2, 15, 'Una cápsula cada 12 horas por 5 días');

-- Radiología: una tercera orden pendiente
INSERT INTO ordenes_radiologia (paciente_id, medico_id, tipo_estudio_id, estado) VALUES (2, 2, 1, 'pendiente');

-- Facturación: una tercera factura pendiente
INSERT INTO facturas (paciente_id, estado, subtotal, impuestos, total) VALUES (5, 'pendiente', 40.00, 4.80, 44.80);
SET @factura3 := LAST_INSERT_ID();
INSERT INTO factura_items (factura_id, servicio_id, cantidad, precio_unitario, subtotal) VALUES
    (@factura3, 3, 1, 40.00, 40.00);

-- Emergencias: un tercer caso, ya atendido, para ver el badge "atendido" también aquí
INSERT INTO casos_emergencia (paciente_id, nivel_triage_id, motivo, estado) VALUES (1, 2, 'Dificultad respiratoria', 'en_espera');
SET @caso3 := LAST_INSERT_ID();
INSERT INTO atenciones_emergencia (caso_id, medico_id, notas) VALUES (@caso3, 1, 'Se administra oxígeno, paciente estabiliza.');
UPDATE casos_emergencia SET estado = 'atendido' WHERE id = @caso3;
