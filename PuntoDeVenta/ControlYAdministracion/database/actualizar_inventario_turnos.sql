-- =====================================================
-- ACTUALIZACIÓN: Agregar columna Limite_Productos
-- =====================================================
-- Ejecutar este script si la tabla Inventario_Turnos ya existe
-- y necesita la columna Limite_Productos

-- Verificar si la columna existe antes de agregarla
SET @dbname = DATABASE();
SET @tablename = 'Inventario_Turnos';
SET @columnname = 'Limite_Productos';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT "La columna Limite_Productos ya existe" as Mensaje',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' INT(11) NOT NULL DEFAULT 50 AFTER Productos_Completados')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Si prefieres hacerlo de forma más simple, descomenta esta línea:
-- ALTER TABLE `Inventario_Turnos` ADD COLUMN `Limite_Productos` INT(11) NOT NULL DEFAULT 50 AFTER `Productos_Completados`;
