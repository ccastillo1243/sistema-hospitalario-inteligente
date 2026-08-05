-- =========================================================================
-- Datos de prueba adicionales: asegura al menos 2-3 ejemplos en catálogos
-- que quedaron con solo 1 fila tras seed.sql + seed_test_data.sql.
-- =========================================================================

INSERT INTO medicamentos (categoria_id, nombre, presentacion) VALUES
    (2, 'Amoxicilina 500mg', 'Cápsulas'),
    (1, 'Ibuprofeno 400mg', 'Tabletas');

INSERT INTO lotes_inventario (medicamento_id, proveedor_id, numero_lote, cantidad, vencimiento) VALUES
    (2, 1, 'LOTE-002', 60, '2027-06-01'),
    (3, 1, 'LOTE-003', 80, '2026-12-01');

INSERT INTO proveedores (nombre, telefono) VALUES
    ('Farmacéutica del Pacífico', '02-2987654'),
    ('Insumos Médicos Andinos', '02-2765432');

INSERT INTO almacenes (nombre, ubicacion) VALUES
    ('Farmacia Central', 'Piso 1, ala norte'),
    ('Bodega de Suministros', 'Sótano 2');

INSERT INTO habitaciones (tipo_habitacion_id, numero, piso) VALUES
    (3, '201', 2);

INSERT INTO camas (habitacion_id, codigo) VALUES
    (3, 'A'),
    (3, 'B');
