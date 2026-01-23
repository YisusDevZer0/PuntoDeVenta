-- =====================================================
-- VERIFICAR ESTADO DE CONTROL DE LOTES PARA UN PRODUCTO
-- =====================================================
-- Este script te permite verificar si un producto tiene
-- el control de lotes activado y si tiene lotes registrados

-- Reemplaza '7503008344785' con el código de barras del producto que quieres verificar
SET @codigo_barras = '7503008344785';
SET @sucursal_id = 4; -- IMPORTANTE: Cambia por la sucursal que quieres verificar (el usuario usa sucursal 4)

-- Verificar estado en Stock_POS
SELECT 
    sp.ID_Prod_POS,
    sp.Cod_Barra,
    sp.Nombre_Prod,
    sp.Fk_sucursal,
    s.Nombre_Sucursal,
    sp.Control_Lotes_Caducidad,
    sp.Existencias_R,
    sp.Lote as Lote_Stock_POS,
    sp.Fecha_Caducidad as Fecha_Caducidad_Stock_POS,
    CASE 
        WHEN sp.Control_Lotes_Caducidad = 1 THEN 'ACTIVADO - Se descontará automáticamente'
        WHEN sp.Control_Lotes_Caducidad = 0 THEN 'DESACTIVADO - No se descontará automáticamente'
        WHEN sp.Control_Lotes_Caducidad IS NULL THEN 'NO CONFIGURADO - Verificar si tiene lotes'
        ELSE 'ESTADO DESCONOCIDO'
    END as Estado_Control
FROM Stock_POS sp
INNER JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
WHERE sp.Cod_Barra = @codigo_barras
  AND sp.Fk_sucursal = @sucursal_id;

-- Verificar lotes en Historial_Lotes
SELECT 
    hl.ID_Historial,
    hl.Lote,
    hl.Fecha_Caducidad,
    hl.Existencias,
    DATEDIFF(hl.Fecha_Caducidad, CURDATE()) as Dias_restantes,
    hl.Fecha_Ingreso,
    hl.Usuario_Modifico,
    CASE 
        WHEN DATEDIFF(hl.Fecha_Caducidad, CURDATE()) < 0 THEN 'VENCIDO'
        WHEN DATEDIFF(hl.Fecha_Caducidad, CURDATE()) <= 15 THEN 'PRÓXIMO A VENCER'
        ELSE 'VIGENTE'
    END as Estado_Caducidad
FROM Historial_Lotes hl
INNER JOIN Stock_POS sp ON hl.ID_Prod_POS = sp.ID_Prod_POS AND hl.Fk_sucursal = sp.Fk_sucursal
WHERE sp.Cod_Barra = @codigo_barras
  AND hl.Fk_sucursal = @sucursal_id
  AND hl.Existencias > 0
ORDER BY hl.Fecha_Caducidad ASC;

-- Resumen
SELECT 
    'RESUMEN' as Tipo,
    COUNT(*) as Total_Lotes,
    SUM(hl.Existencias) as Total_Existencias,
    MIN(hl.Fecha_Caducidad) as Fecha_Caducidad_Mas_Proxima
FROM Historial_Lotes hl
INNER JOIN Stock_POS sp ON hl.ID_Prod_POS = sp.ID_Prod_POS AND hl.Fk_sucursal = sp.Fk_sucursal
WHERE sp.Cod_Barra = @codigo_barras
  AND hl.Fk_sucursal = @sucursal_id
  AND hl.Existencias > 0;
