-- =====================================================
-- AGREGAR CAMPO PARA CONTROL DE LOTES Y CADUCIDAD
-- =====================================================
-- Este script agrega un campo en Stock_POS para indicar
-- si el producto en esa sucursal requiere control de lotes y caducidad
-- 
-- NOTA: El control es por PRODUCTO-SUCURSAL, no solo por producto,
-- porque diferentes sucursales pueden manejar el mismo producto de manera diferente
-- (ej: farmacia requiere lotes, tienda general no)
--
-- IMPORTANTE: Si la columna ya existe, este script dará error.
-- En ese caso, ejecuta solo la parte del UPDATE más abajo.

-- Agregar campo Control_Lotes_Caducidad en Stock_POS
-- Si ya existe, ignora el error y continúa manualmente con el UPDATE
ALTER TABLE `Stock_POS` 
ADD COLUMN `Control_Lotes_Caducidad` TINYINT(1) DEFAULT 0 
COMMENT '1 = Requiere control de lotes y caducidad, 0 = No requiere' 
AFTER `JustificacionAjuste`;

-- Agregar índice para optimizar consultas
-- Si ya existe, ignora el error
ALTER TABLE `Stock_POS` 
ADD INDEX `idx_control_lotes` (`Control_Lotes_Caducidad`);

-- =====================================================
-- OPCIONAL: Actualizar productos existentes basado en lotes registrados
-- =====================================================
-- Marcar automáticamente stock que ya tiene lotes registrados
-- NOTA: Usamos tabla temporal para evitar conflicto con triggers

-- Crear tabla temporal con los productos que tienen lotes
CREATE TEMPORARY TABLE IF NOT EXISTS temp_productos_con_lotes AS
SELECT DISTINCT ID_Prod_POS, Fk_sucursal 
FROM Historial_Lotes 
WHERE Existencias > 0;

-- Actualizar Stock_POS usando la tabla temporal
-- Esto evita el conflicto con el trigger trg_AfterStockUpdate
UPDATE Stock_POS sp
INNER JOIN temp_productos_con_lotes t ON sp.ID_Prod_POS = t.ID_Prod_POS 
    AND sp.Fk_sucursal = t.Fk_sucursal
SET sp.Control_Lotes_Caducidad = 1
WHERE sp.Control_Lotes_Caducidad = 0 OR sp.Control_Lotes_Caducidad IS NULL;

-- Eliminar tabla temporal
DROP TEMPORARY TABLE IF EXISTS temp_productos_con_lotes;
