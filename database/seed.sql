-- =========================================================================
-- Datos de prueba (contraseña de todos los usuarios: "password123")
-- =========================================================================

INSERT INTO roles (nombre, descripcion) VALUES
    ('admin', 'Administrador del sistema'),
    ('medico', 'Personal médico'),
    ('enfermeria', 'Personal de enfermería'),
    ('farmacia', 'Personal de farmacia'),
    ('laboratorio', 'Personal de laboratorio'),
    ('radiologia', 'Personal de radiología'),
    ('facturacion', 'Personal de facturación'),
    ('recepcion', 'Personal de recepción');

INSERT INTO usuarios (email, password_hash, nombre, apellido) VALUES
    ('admin@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'Ana', 'Administradora'),
    ('medico1@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'Carlos', 'Gómez'),
    ('medico2@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'Laura', 'Pérez'),
    ('enfermera1@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'María', 'López'),
    ('enfermera2@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'Sofía', 'Ramírez'),
    ('farmacia@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'Jorge', 'Torres'),
    ('laboratorio@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'Elena', 'Flores'),
    ('radiologia@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'Diego', 'Vargas'),
    ('facturacion@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'Paola', 'Reyes'),
    ('recepcion@hospital.com', '$2y$10$Jzkr.NGuRpLZD5Pua/G2/.GZxBZyLkE95DB9TwA.vbv2uFQGde9JO', 'Pedro', 'Sánchez');

INSERT INTO usuario_roles (usuario_id, rol_id)
SELECT u.id, r.id FROM usuarios u JOIN roles r ON
    (u.email = 'admin@hospital.com' AND r.nombre = 'admin') OR
    (u.email = 'medico1@hospital.com' AND r.nombre = 'medico') OR
    (u.email = 'medico2@hospital.com' AND r.nombre = 'medico') OR
    (u.email = 'enfermera1@hospital.com' AND r.nombre = 'enfermeria') OR
    (u.email = 'enfermera2@hospital.com' AND r.nombre = 'enfermeria') OR
    (u.email = 'farmacia@hospital.com' AND r.nombre = 'farmacia') OR
    (u.email = 'laboratorio@hospital.com' AND r.nombre = 'laboratorio') OR
    (u.email = 'radiologia@hospital.com' AND r.nombre = 'radiologia') OR
    (u.email = 'facturacion@hospital.com' AND r.nombre = 'facturacion') OR
    (u.email = 'recepcion@hospital.com' AND r.nombre = 'recepcion');

INSERT INTO departamentos (nombre) VALUES ('Medicina General'), ('Enfermería'), ('Administración');
INSERT INTO especialidades (nombre) VALUES ('Medicina General'), ('Pediatría'), ('Cardiología');

INSERT INTO personal (usuario_id, departamento_id, nombre, apellido, tipo)
SELECT u.id, 1, u.nombre, u.apellido, 'medico' FROM usuarios u WHERE u.email IN ('medico1@hospital.com','medico2@hospital.com');

INSERT INTO personal (usuario_id, departamento_id, nombre, apellido, tipo)
SELECT u.id, 2, u.nombre, u.apellido, 'enfermeria' FROM usuarios u WHERE u.email IN ('enfermera1@hospital.com','enfermera2@hospital.com');

INSERT INTO personal (usuario_id, departamento_id, nombre, apellido, tipo)
SELECT u.id, 3, u.nombre, u.apellido, 'farmaceutico' FROM usuarios u WHERE u.email = 'farmacia@hospital.com';

INSERT INTO personal (usuario_id, departamento_id, nombre, apellido, tipo)
SELECT u.id, 3, u.nombre, u.apellido, 'radiologo' FROM usuarios u WHERE u.email = 'radiologia@hospital.com';

INSERT INTO medicos (personal_id) SELECT id FROM personal WHERE tipo = 'medico';
INSERT INTO enfermeros (personal_id) SELECT id FROM personal WHERE tipo = 'enfermeria';

INSERT INTO medico_especialidades (medico_id, especialidad_id)
SELECT m.id, 1 FROM medicos m;

INSERT INTO pacientes (numero_expediente, nombre, apellido, fecha_nacimiento, genero, documento_identidad) VALUES
    ('EXP-1000', 'Juan', 'Martínez', '1990-01-01', 'M', '0001'),
    ('EXP-1001', 'Rosa', 'Hernández', '1990-02-01', 'F', '0002'),
    ('EXP-1002', 'Luis', 'Díaz', '1990-03-01', 'M', '0003'),
    ('EXP-1003', 'Carmen', 'Ortega', '1990-04-01', 'F', '0004'),
    ('EXP-1004', 'Miguel', 'Castro', '1990-05-01', 'M', '0005');

INSERT INTO expedientes_clinicos (paciente_id) SELECT id FROM pacientes;

INSERT INTO categorias_medicamento (nombre) VALUES ('Analgésicos'), ('Antibióticos');
INSERT INTO medicamentos (categoria_id, nombre, presentacion) VALUES (1, 'Paracetamol 500mg', 'Tabletas');
INSERT INTO proveedores (nombre) VALUES ('Distribuidora Médica S.A.');
INSERT INTO lotes_inventario (medicamento_id, proveedor_id, numero_lote, cantidad, vencimiento)
VALUES (1, 1, 'LOTE-001', 100, '2027-01-01');

INSERT INTO tipos_habitacion (nombre) VALUES ('Individual'), ('Compartida'), ('UCI');
INSERT INTO habitaciones (tipo_habitacion_id, numero, piso) VALUES (1, '101', 1), (1, '102', 1);
INSERT INTO camas (habitacion_id, codigo) VALUES (1, 'A'), (1, 'B'), (2, 'A');

INSERT INTO tipos_examen_laboratorio (nombre) VALUES ('Hemograma completo');
INSERT INTO parametros_examen (tipo_examen_id, nombre, unidad, valor_referencia_minimo, valor_referencia_maximo)
VALUES (1, 'Hemoglobina', 'g/dL', 12, 16);

INSERT INTO tipos_cita (nombre, duracion_minutos) VALUES ('Consulta general', 30), ('Control', 20);

INSERT INTO citas (paciente_id, medico_id, tipo_cita_id, fecha_hora, estado)
SELECT 1, m.id, 1, NOW(), 'programada' FROM medicos m LIMIT 1;

INSERT INTO tipos_estudio_radiologico (nombre) VALUES ('Radiografía de tórax'), ('Tomografía');

INSERT INTO metodos_pago (nombre) VALUES ('Efectivo'), ('Tarjeta'), ('Seguro médico');
INSERT INTO servicios_facturables (nombre, precio, modulo_origen) VALUES
    ('Consulta general', 25.00, 'consultas'),
    ('Hemograma completo', 15.00, 'laboratorio'),
    ('Radiografía de tórax', 40.00, 'radiologia');

INSERT INTO niveles_triage (nombre, prioridad) VALUES
    ('Resucitación', 1), ('Emergencia', 2), ('Urgencia', 3), ('Urgencia menor', 4), ('Sin urgencia', 5);

INSERT INTO almacenes (nombre, ubicacion) VALUES ('Almacén General', 'Sótano 1');
INSERT INTO articulos_inventario (almacen_id, nombre, categoria, unidad_medida) VALUES
    (1, 'Guantes de látex', 'Insumos médicos', 'caja');
INSERT INTO existencias_inventario (articulo_id, cantidad) VALUES (1, 50);
