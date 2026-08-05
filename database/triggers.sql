-- =========================================================================
-- Triggers de reglas de negocio
-- =========================================================================

DELIMITER $$

-- Al crear un ingreso, la cama pasa a "ocupada"
CREATE TRIGGER trg_ingreso_after_insert
AFTER INSERT ON ingresos
FOR EACH ROW
BEGIN
    UPDATE camas SET estado = 'ocupada' WHERE id = NEW.cama_id;
END$$

-- Al registrar un alta, la cama del ingreso vuelve a "libre"
CREATE TRIGGER trg_alta_after_insert
AFTER INSERT ON altas
FOR EACH ROW
BEGIN
    UPDATE camas c
    JOIN ingresos i ON i.cama_id = c.id
    SET c.estado = 'libre'
    WHERE i.id = NEW.ingreso_id;
END$$

-- Al trasladar, libera la cama origen y ocupa la cama destino
CREATE TRIGGER trg_traslado_after_insert
AFTER INSERT ON traslados
FOR EACH ROW
BEGIN
    UPDATE camas c
    JOIN ingresos i ON i.cama_id = c.id
    SET c.estado = 'libre'
    WHERE i.id = NEW.ingreso_id;

    UPDATE camas SET estado = 'ocupada' WHERE id = NEW.cama_destino_id;

    UPDATE ingresos SET cama_id = NEW.cama_destino_id WHERE id = NEW.ingreso_id;
END$$

-- Al dispensar, descuenta cantidad del lote más próximo a vencer con stock disponible
CREATE TRIGGER trg_dispensacion_after_insert
AFTER INSERT ON dispensaciones
FOR EACH ROW
BEGIN
    DECLARE v_medicamento_id INT;
    DECLARE v_lote_id INT;

    SELECT ri.medicamento_id INTO v_medicamento_id
    FROM receta_items ri WHERE ri.id = NEW.receta_item_id;

    SELECT id INTO v_lote_id
    FROM lotes_inventario
    WHERE medicamento_id = v_medicamento_id AND cantidad >= NEW.cantidad
    ORDER BY vencimiento ASC
    LIMIT 1;

    IF v_lote_id IS NOT NULL THEN
        UPDATE lotes_inventario SET cantidad = cantidad - NEW.cantidad WHERE id = v_lote_id;
    END IF;
END$$

DELIMITER ;
