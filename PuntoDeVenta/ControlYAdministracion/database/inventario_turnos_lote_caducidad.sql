-- =====================================================
-- Inventario por turnos: Lote y Fecha de caducidad
-- =====================================================
-- Agrega columnas opcionales a Inventario_Turnos_Productos
-- para registrar lote y fecha de caducidad al hacer el conteo.

SET @dbname = DATABASE();
SET @tbl = 'Inventario_Turnos_Productos';

-- Lote (opcional)
SET @col = 'Lote';
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tbl AND COLUMN_NAME = @col) > 0,
  'SELECT "Columna Lote ya existe" AS Mensaje',
  CONCAT('ALTER TABLE ', @tbl, ' ADD COLUMN ', @col, ' VARCHAR(100) NULL DEFAULT NULL AFTER Observaciones')
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Fecha_Caducidad (opcional)
SET @col = 'Fecha_Caducidad';
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tbl AND COLUMN_NAME = @col) > 0,
  'SELECT "Columna Fecha_Caducidad ya existe" AS Mensaje',
  CONCAT('ALTER TABLE ', @tbl, ' ADD COLUMN ', @col, ' DATE NULL DEFAULT NULL AFTER Lote')
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
