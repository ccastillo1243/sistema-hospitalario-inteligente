-- =========================================================================
-- Sistema Hospitalario Inteligente — Esquema de base de datos (MySQL/MariaDB)
-- 66 tablas agrupadas por módulo. InnoDB, utf8mb4, normalizado a 3FN.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =========================================================================
-- MÓDULO: SEGURIDAD / USUARIOS
-- =========================================================================

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(100) NOT NULL UNIQUE,
    modulo VARCHAR(50) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    ultimo_login DATETIME NULL,
    bloqueado_hasta DATETIME NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    eliminado_en DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE rol_permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,
    UNIQUE KEY uq_rol_permiso (rol_id, permiso_id),
    CONSTRAINT fk_rp_rol FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_rp_permiso FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuario_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rol_id INT NOT NULL,
    UNIQUE KEY uq_usuario_rol (usuario_id, rol_id),
    CONSTRAINT fk_ur_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_ur_rol FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE intentos_login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    email VARCHAR(150) NOT NULL,
    exitoso TINYINT(1) NOT NULL,
    ip VARCHAR(45) NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_il_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: AUDITORÍA / SISTEMA
-- =========================================================================

CREATE TABLE bitacora_auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    tabla VARCHAR(100) NOT NULL,
    registro_id VARCHAR(50) NOT NULL,
    accion VARCHAR(20) NOT NULL,
    datos_json JSON NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ba_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_ba_tabla_registro (tabla, registro_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    mensaje VARCHAR(500) NOT NULL,
    leida TINYINT(1) NOT NULL DEFAULT 0,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: PACIENTES / EXPEDIENTES
-- =========================================================================

CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_expediente VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    genero VARCHAR(1) NOT NULL,
    documento_identidad VARCHAR(30) NOT NULL UNIQUE,
    telefono VARCHAR(30) NULL,
    email VARCHAR(150) NULL,
    direccion VARCHAR(255) NULL,
    tipo_sangre VARCHAR(5) NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    eliminado_en DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contactos_paciente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    parentesco VARCHAR(50) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    es_emergencia TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_cp_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE aseguradoras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30) NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE seguros_paciente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    aseguradora_id INT NOT NULL,
    numero_poliza VARCHAR(50) NOT NULL,
    vigente_desde DATE NOT NULL,
    vigente_hasta DATE NULL,
    CONSTRAINT fk_sp_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_sp_aseguradora FOREIGN KEY (aseguradora_id) REFERENCES aseguradoras(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE expedientes_clinicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL UNIQUE,
    antecedentes TEXT NULL,
    observaciones TEXT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ec_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE alergias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE paciente_alergias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    alergia_id INT NOT NULL,
    severidad VARCHAR(30) NULL,
    UNIQUE KEY uq_paciente_alergia (paciente_id, alergia_id),
    CONSTRAINT fk_pa_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_pa_alergia FOREIGN KEY (alergia_id) REFERENCES alergias(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: PERSONAL
-- =========================================================================

CREATE TABLE departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE personal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL UNIQUE,
    departamento_id INT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    cedula_profesional VARCHAR(30) NULL UNIQUE,
    telefono VARCHAR(30) NULL,
    tipo VARCHAR(20) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    eliminado_en DATETIME NULL,
    CONSTRAINT fk_personal_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_personal_departamento FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id INT NOT NULL UNIQUE,
    CONSTRAINT fk_medico_personal FOREIGN KEY (personal_id) REFERENCES personal(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE enfermeros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id INT NOT NULL UNIQUE,
    CONSTRAINT fk_enfermero_personal FOREIGN KEY (personal_id) REFERENCES personal(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE medico_especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medico_id INT NOT NULL,
    especialidad_id INT NOT NULL,
    UNIQUE KEY uq_medico_especialidad (medico_id, especialidad_id),
    CONSTRAINT fk_me_medico FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    CONSTRAINT fk_me_especialidad FOREIGN KEY (especialidad_id) REFERENCES especialidades(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: AGENDA MÉDICA
-- =========================================================================

CREATE TABLE tipos_cita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    duracion_minutos INT NOT NULL DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE disponibilidad_medico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medico_id INT NOT NULL,
    dia_semana TINYINT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    CONSTRAINT fk_dm_medico FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    tipo_cita_id INT NOT NULL,
    fecha_hora DATETIME NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'programada',
    motivo VARCHAR(255) NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cita_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    CONSTRAINT fk_cita_medico FOREIGN KEY (medico_id) REFERENCES medicos(id),
    CONSTRAINT fk_cita_tipo FOREIGN KEY (tipo_cita_id) REFERENCES tipos_cita(id),
    INDEX idx_cita_fecha (fecha_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE historial_estado_cita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cita_id INT NOT NULL,
    estado VARCHAR(20) NOT NULL,
    cambiado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_hec_cita FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: CONSULTAS
-- =========================================================================

CREATE TABLE consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cita_id INT NULL UNIQUE,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    motivo VARCHAR(255) NULL,
    notas TEXT NULL,
    fecha DATETIME NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_consulta_cita FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE SET NULL,
    CONSTRAINT fk_consulta_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    CONSTRAINT fk_consulta_medico FOREIGN KEY (medico_id) REFERENCES medicos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE signos_vitales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consulta_id INT NULL,
    paciente_id INT NOT NULL,
    presion_arterial VARCHAR(20) NULL,
    frecuencia_cardiaca INT NULL,
    temperatura DECIMAL(4,1) NULL,
    saturacion_o2 DECIMAL(4,1) NULL,
    registrado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sv_consulta FOREIGN KEY (consulta_id) REFERENCES consultas(id) ON DELETE CASCADE,
    CONSTRAINT fk_sv_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE diagnosticos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consulta_id INT NOT NULL,
    codigo_cie10 VARCHAR(10) NULL,
    descripcion VARCHAR(255) NOT NULL,
    tipo VARCHAR(20) NOT NULL DEFAULT 'principal',
    CONSTRAINT fk_diag_consulta FOREIGN KEY (consulta_id) REFERENCES consultas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: HOSPITALIZACIÓN
-- =========================================================================

CREATE TABLE tipos_habitacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE habitaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_habitacion_id INT NOT NULL,
    numero VARCHAR(20) NOT NULL UNIQUE,
    piso INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_hab_tipo FOREIGN KEY (tipo_habitacion_id) REFERENCES tipos_habitacion(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE camas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habitacion_id INT NOT NULL,
    codigo VARCHAR(10) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'libre',
    UNIQUE KEY uq_habitacion_codigo (habitacion_id, codigo),
    CONSTRAINT fk_cama_habitacion FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ingresos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    cama_id INT NOT NULL,
    medico_id INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    ingresado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ing_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    CONSTRAINT fk_ing_cama FOREIGN KEY (cama_id) REFERENCES camas(id),
    CONSTRAINT fk_ing_medico FOREIGN KEY (medico_id) REFERENCES medicos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE altas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingreso_id INT NOT NULL UNIQUE,
    resumen TEXT NOT NULL,
    tipo VARCHAR(30) NOT NULL DEFAULT 'medica',
    fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alta_ingreso FOREIGN KEY (ingreso_id) REFERENCES ingresos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE traslados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingreso_id INT NOT NULL,
    cama_destino_id INT NOT NULL,
    motivo VARCHAR(255) NULL,
    trasladado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tras_ingreso FOREIGN KEY (ingreso_id) REFERENCES ingresos(id) ON DELETE CASCADE,
    CONSTRAINT fk_tras_cama FOREIGN KEY (cama_destino_id) REFERENCES camas(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE rondas_enfermeria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingreso_id INT NOT NULL,
    enfermero_id INT NOT NULL,
    notas TEXT NULL,
    registrado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_re_ingreso FOREIGN KEY (ingreso_id) REFERENCES ingresos(id) ON DELETE CASCADE,
    CONSTRAINT fk_re_enfermero FOREIGN KEY (enfermero_id) REFERENCES enfermeros(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: LABORATORIO
-- =========================================================================

CREATE TABLE tipos_examen_laboratorio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE parametros_examen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_examen_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    unidad VARCHAR(20) NULL,
    valor_referencia_minimo DECIMAL(10,2) NULL,
    valor_referencia_maximo DECIMAL(10,2) NULL,
    CONSTRAINT fk_pe_tipo FOREIGN KEY (tipo_examen_id) REFERENCES tipos_examen_laboratorio(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ordenes_laboratorio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ol_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    CONSTRAINT fk_ol_medico FOREIGN KEY (medico_id) REFERENCES medicos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE muestras_laboratorio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_id INT NOT NULL,
    tipo_examen_id INT NOT NULL,
    codigo_barras VARCHAR(30) NOT NULL UNIQUE,
    tipo_muestra VARCHAR(50) NOT NULL,
    tomada_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ml_orden FOREIGN KEY (orden_id) REFERENCES ordenes_laboratorio(id) ON DELETE CASCADE,
    CONSTRAINT fk_ml_tipo FOREIGN KEY (tipo_examen_id) REFERENCES tipos_examen_laboratorio(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE resultados_laboratorio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    muestra_id INT NOT NULL,
    parametro_id INT NOT NULL,
    valor VARCHAR(50) NOT NULL,
    registrado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rl_muestra FOREIGN KEY (muestra_id) REFERENCES muestras_laboratorio(id) ON DELETE CASCADE,
    CONSTRAINT fk_rl_parametro FOREIGN KEY (parametro_id) REFERENCES parametros_examen(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE archivos_resultado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resultado_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    subido_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ar_resultado FOREIGN KEY (resultado_id) REFERENCES resultados_laboratorio(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: FARMACIA
-- =========================================================================

CREATE TABLE categorias_medicamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    presentacion VARCHAR(100) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_med_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_medicamento(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE lotes_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicamento_id INT NOT NULL,
    proveedor_id INT NOT NULL,
    numero_lote VARCHAR(50) NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    vencimiento DATE NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_li_medicamento FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id),
    CONSTRAINT fk_li_proveedor FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE recetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_receta_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    CONSTRAINT fk_receta_medico FOREIGN KEY (medico_id) REFERENCES medicos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE receta_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receta_id INT NOT NULL,
    medicamento_id INT NOT NULL,
    cantidad INT NOT NULL,
    indicaciones VARCHAR(255) NULL,
    CONSTRAINT fk_ri_receta FOREIGN KEY (receta_id) REFERENCES recetas(id) ON DELETE CASCADE,
    CONSTRAINT fk_ri_medicamento FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE dispensaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receta_item_id INT NOT NULL UNIQUE,
    farmaceutico_id INT NOT NULL,
    cantidad INT NOT NULL,
    dispensado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_disp_item FOREIGN KEY (receta_item_id) REFERENCES receta_items(id) ON DELETE CASCADE,
    CONSTRAINT fk_disp_personal FOREIGN KEY (farmaceutico_id) REFERENCES personal(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: RADIOLOGÍA
-- =========================================================================

CREATE TABLE tipos_estudio_radiologico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ordenes_radiologia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    tipo_estudio_id INT NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_or_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    CONSTRAINT fk_or_medico FOREIGN KEY (medico_id) REFERENCES medicos(id),
    CONSTRAINT fk_or_tipo FOREIGN KEY (tipo_estudio_id) REFERENCES tipos_estudio_radiologico(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE estudios_radiologicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_id INT NOT NULL UNIQUE,
    realizado_por INT NOT NULL,
    realizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observaciones VARCHAR(255) NULL,
    CONSTRAINT fk_er_orden FOREIGN KEY (orden_id) REFERENCES ordenes_radiologia(id) ON DELETE CASCADE,
    CONSTRAINT fk_er_personal FOREIGN KEY (realizado_por) REFERENCES personal(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE informes_radiologicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudio_id INT NOT NULL UNIQUE,
    radiologo_id INT NOT NULL,
    contenido TEXT NOT NULL,
    emitido_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ir_estudio FOREIGN KEY (estudio_id) REFERENCES estudios_radiologicos(id) ON DELETE CASCADE,
    CONSTRAINT fk_ir_personal FOREIGN KEY (radiologo_id) REFERENCES personal(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: FACTURACIÓN
-- =========================================================================

CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE servicios_facturables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    modulo_origen VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    impuestos DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fact_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE factura_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    servicio_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_fi_factura FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE,
    CONSTRAINT fk_fi_servicio FOREIGN KEY (servicio_id) REFERENCES servicios_facturables(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    metodo_pago_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    pagado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pago_factura FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE,
    CONSTRAINT fk_pago_metodo FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: EMERGENCIAS
-- =========================================================================

CREATE TABLE niveles_triage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    prioridad INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE casos_emergencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    nivel_triage_id INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'en_espera',
    llegada_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ce_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    CONSTRAINT fk_ce_nivel FOREIGN KEY (nivel_triage_id) REFERENCES niveles_triage(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE atenciones_emergencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caso_id INT NOT NULL,
    medico_id INT NOT NULL,
    notas TEXT NULL,
    atendido_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ae_caso FOREIGN KEY (caso_id) REFERENCES casos_emergencia(id) ON DELETE CASCADE,
    CONSTRAINT fk_ae_medico FOREIGN KEY (medico_id) REFERENCES medicos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: INVENTARIO GENERAL
-- =========================================================================

CREATE TABLE almacenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    ubicacion VARCHAR(150) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE articulos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    almacen_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    categoria VARCHAR(100) NULL,
    unidad_medida VARCHAR(30) NOT NULL,
    CONSTRAINT fk_ai_almacen FOREIGN KEY (almacen_id) REFERENCES almacenes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE existencias_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    articulo_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_ei_articulo FOREIGN KEY (articulo_id) REFERENCES articulos_inventario(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    existencia_id INT NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(255) NULL,
    registrado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mi_existencia FOREIGN KEY (existencia_id) REFERENCES existencias_inventario(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- MÓDULO: REPORTES
-- =========================================================================

CREATE TABLE definiciones_reporte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    modulo VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ejecuciones_reporte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporte_id INT NOT NULL,
    usuario_id INT NULL,
    ejecutado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    formato VARCHAR(10) NOT NULL,
    CONSTRAINT fk_er2_reporte FOREIGN KEY (reporte_id) REFERENCES definiciones_reporte(id) ON DELETE CASCADE,
    CONSTRAINT fk_er2_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
