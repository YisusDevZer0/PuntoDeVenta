-- =====================================================
-- Inventario por turnos: Lote y Fecha de caducidad
-- =====================================================
-- Ejecuta este script en tu base de datos (phpMyAdmin o MySQL)
-- para que el modal "Registrar conteo físico" guarde Lote y
-- Fecha de caducidad. Sin este script, el conteo funciona pero
-- esos campos no se guardan.
--
-- Agrega columnas opcionales a Inventario_Turnos_Productos.

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
