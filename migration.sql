-- Ejecuta una sola vez en phpMyAdmin sobre la base barberia_db existente.
USE barberia_db;

-- Los clientes siguen pudiendo reservar como invitados. Esta columna guarda
-- un hash solo cuando el cliente elige crear una cuenta.
ALTER TABLE clientes ADD COLUMN password_hash VARCHAR(255) NULL;

-- Folio necesario para consultar, reprogramar o cancelar una cita.
ALTER TABLE citas ADD COLUMN folio CHAR(10) NULL;
UPDATE citas SET folio = CONCAT('LEGACY', LPAD(id, 4, '0')) WHERE folio IS NULL;
ALTER TABLE citas MODIFY folio CHAR(10) NOT NULL;
ALTER TABLE citas ADD UNIQUE KEY uq_citas_folio (folio);

-- Catálogo de barberos. Las citas antiguas quedan sin asignación; las nuevas
-- requieren seleccionar un barbero desde la landing page.
CREATE TABLE barberos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  especialidad VARCHAR(160) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE citas ADD COLUMN barbero_id INT NULL;
ALTER TABLE citas ADD CONSTRAINT fk_citas_barbero FOREIGN KEY (barbero_id) REFERENCES barberos(id) ON DELETE RESTRICT;
CREATE INDEX idx_citas_barbero_fecha ON citas (barbero_id, fecha, estado);
