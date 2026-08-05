-- =========================================================================
-- Migración incremental: tabla de recuperación de contraseña
-- (no estaba en el schema.sql inicial de la Fase 1; se agrega en la Fase 12
-- para cubrir el requisito de seguridad "recuperación de contraseña").
-- =========================================================================

CREATE TABLE IF NOT EXISTS tokens_recuperacion_password (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expira_en DATETIME NOT NULL,
    usado_en DATETIME NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_trp_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
