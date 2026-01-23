-- =====================================================
-- ACTIVAR CONTROL DE LOTES PARA PRODUCTOS EN UNA SUCURSAL
-- =====================================================
-- Este script activa el control de lotes para productos
-- que ya tienen lotes registrados en una sucursal específica

-- IMPORTANTE: Cambia el número de sucursal según necesites
SET @sucursal_id = 4;

-- Activar control de lotes para productos que tienen lotes en esta sucursal
-- Usa tabla temporal para evitar conflicto con triggers
CREATE TEMPORARY TABLE IF NOT EXISTS temp_productos_con_lotes_sucursal AS
SELECT DISTINCT ID_Prod_POS, Fk_sucursal 
FROM Historial_Lotes 
WHERE Fk_sucursal = @sucursal_id
  AND Existencias > 0;

-- Actualizar Stock_POS usando la tabla temporal
UPDATE Stock_POS sp
INNER JOIN temp_productos_con_lotes_sucursal t ON sp.ID_Prod_POS = t.ID_Prod_POS 
    AND sp.Fk_sucursal = t.Fk_sucursal
SET sp.Control_Lotes_Caducidad = 1
WHERE sp.Fk_sucursal = @sucursal_id
  AND (sp.Control_Lotes_Caducidad = 0 OR sp.Control_Lotes_Caducidad IS NULL);

-- Eliminar tabla temporal
DROP TEMPORARY TABLE IF EXISTS temp_productos_con_lotes_sucursal;

-- Verificar resultados
SELECT 
    'Productos actualizados en sucursal' AS resultado,
    @sucursal_id AS sucursal,
    COUNT(*) AS total_productos_con_control_activado
FROM Stock_POS 
WHERE Fk_sucursal = @sucursal_id
  AND Control_Lotes_Caducidad = 1;

-- Mostrar productos actualizados
SELECT 
    sp.ID_Prod_POS,
    sp.Cod_Barra,
    sp.Nombre_Prod,
    sp.Fk_sucursal,
    s.Nombre_Sucursal,
    sp.Control_Lotes_Caducidad,
    (SELECT COUNT(*) FROM Historial_Lotes hl 
     WHERE hl.ID_Prod_POS = sp.ID_Prod_POS 
       AND hl.Fk_sucursal = sp.Fk_sucursal 
       AND hl.Existencias > 0) as Total_Lotes_Registrados
FROM Stock_POS sp
INNER JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
WHERE sp.Fk_sucursal = @sucursal_id
  AND sp.Control_Lotes_Caducidad = 1
ORDER BY sp.Cod_Barra
LIMIT 50;
