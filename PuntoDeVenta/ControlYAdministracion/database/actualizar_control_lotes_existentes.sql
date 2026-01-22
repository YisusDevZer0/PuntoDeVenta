-- =====================================================
-- ACTUALIZAR CONTROL DE LOTES PARA PRODUCTOS EXISTENTES
-- =====================================================
-- Este script marca automáticamente los productos en Stock_POS
-- que ya tienen lotes registrados en Historial_Lotes
-- 
-- NOTA: Usa tabla temporal para evitar conflicto con triggers

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

-- Verificar resultados
SELECT 
    'Productos actualizados' AS resultado,
    COUNT(*) AS total
FROM Stock_POS 
WHERE Control_Lotes_Caducidad = 1;
