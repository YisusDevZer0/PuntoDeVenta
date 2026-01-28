-- =====================================================
-- VISTA: Productos que permiten registrar lote (Farmacias)
-- =====================================================
-- Productos con stock en sucursal donde aún hay unidades
-- SIN cubrir por lote/caducidad (Existencias_R > SUM(Historial_Lotes)).
-- Solo mientras exista "stock sin cubrir" podrán usar Registrar Lote.

DROP VIEW IF EXISTS v_productos_farmacia_permiten_lote;

CREATE VIEW v_productos_farmacia_permiten_lote AS
SELECT 
    sp.ID_Prod_POS,
    sp.Cod_Barra,
    sp.Nombre_Prod,
    sp.Fk_sucursal,
    sp.Existencias_R AS existencia_total,
    COALESCE(SUM(hl.Existencias), 0) AS en_lotes,
    (COALESCE(sp.Existencias_R, 0) - COALESCE(SUM(hl.Existencias), 0)) AS sin_cubrir
FROM Stock_POS sp
LEFT JOIN Historial_Lotes hl 
    ON hl.ID_Prod_POS = sp.ID_Prod_POS 
    AND hl.Fk_sucursal = sp.Fk_sucursal 
    AND hl.Existencias > 0
WHERE COALESCE(sp.Existencias_R, 0) > 0
GROUP BY sp.ID_Prod_POS, sp.Fk_sucursal, sp.Cod_Barra, sp.Nombre_Prod, sp.Existencias_R
HAVING (COALESCE(sp.Existencias_R, 0) - COALESCE(SUM(hl.Existencias), 0)) > 0;
