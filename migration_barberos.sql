-- Ejecuta una sola vez en phpMyAdmin después de la migración de folios.
USE barberia_db;

CREATE TABLE IF NOT EXISTS barberos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  especialidad VARCHAR(160) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE citas ADD COLUMN barbero_id INT NULL;
ALTER TABLE citas ADD CONSTRAINT fk_citas_barbero
  FOREIGN KEY (barbero_id) REFERENCES barberos(id) ON DELETE RESTRICT;
CREATE INDEX idx_citas_barbero_fecha ON citas (barbero_id, fecha, estado);
