-- =========================================================================
-- Procedimientos almacenados y funciones
-- =========================================================================

DELIMITER $$

-- Función: edad actual de un paciente
CREATE FUNCTION fn_edad_paciente(p_paciente_id INT)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_edad INT;
    SELECT TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) INTO v_edad
    FROM pacientes WHERE id = p_paciente_id;
    RETURN v_edad;
END$$

-- Función: stock total vigente (no vencido) de un medicamento
CREATE FUNCTION fn_stock_medicamento(p_medicamento_id INT)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_stock INT;
    SELECT COALESCE(SUM(cantidad), 0) INTO v_stock
    FROM lotes_inventario
    WHERE medicamento_id = p_medicamento_id AND vencimiento >= CURDATE();
    RETURN v_stock;
END$$

-- Procedimiento: dispensar un ítem de receta validando stock disponible (transacción)
CREATE PROCEDURE sp_dispensar_receta(
    IN p_receta_item_id INT,
    IN p_farmaceutico_id INT,
    IN p_cantidad INT
)
BEGIN
    DECLARE v_medicamento_id INT;
    DECLARE v_stock INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT medicamento_id INTO v_medicamento_id
    FROM receta_items WHERE id = p_receta_item_id FOR UPDATE;

    SET v_stock = fn_stock_medicamento(v_medicamento_id);

    IF v_stock < p_cantidad THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stock insuficiente para dispensar la cantidad solicitada';
    END IF;

    INSERT INTO dispensaciones (receta_item_id, farmaceutico_id, cantidad)
    VALUES (p_receta_item_id, p_farmaceutico_id, p_cantidad);

    COMMIT;
END$$

-- Procedimiento: registrar pago de una factura y actualizar su estado (transacción)
CREATE PROCEDURE sp_registrar_pago_factura(
    IN p_factura_id INT,
    IN p_metodo_pago_id INT,
    IN p_monto DECIMAL(10,2)
)
BEGIN
    DECLARE v_total DECIMAL(10,2);
    DECLARE v_pagado DECIMAL(10,2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT total INTO v_total FROM facturas WHERE id = p_factura_id FOR UPDATE;

    INSERT INTO pagos (factura_id, metodo_pago_id, monto)
    VALUES (p_factura_id, p_metodo_pago_id, p_monto);

    SELECT COALESCE(SUM(monto), 0) INTO v_pagado
    FROM pagos WHERE factura_id = p_factura_id;

    IF v_pagado >= v_total THEN
        UPDATE facturas SET estado = 'pagada' WHERE id = p_factura_id;
    ELSE
        UPDATE facturas SET estado = 'parcial' WHERE id = p_factura_id;
    END IF;

    COMMIT;
END$$

DELIMITER ;
