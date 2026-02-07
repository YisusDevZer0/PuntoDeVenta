-- =====================================================
-- LIMPIAR REGISTROS VACÍOS EN HISTORIAL_LOTES
-- =====================================================
-- Elimina filas donde Lote es inválido (NaN, vacío, etc.)
-- o Fecha_Caducidad es 0000-00-00 / NULL.
-- Ejecutar después de aplicar trigger_stock_pos_sin_lotes_vacios.sql
-- para dejar la tabla consistente.

-- Ver cuántos registros se eliminarían (ejecutar primero como prueba)
-- SELECT COUNT(*) FROM Historial_Lotes
-- WHERE (Lote IS NULL OR TRIM(Lote) = '' OR LOWER(TRIM(Lote)) IN ('nan', 'null', 'n/a', 'na', 'sin lote'))
--    OR (Fecha_Caducidad IS NULL OR Fecha_Caducidad = '0000-00-00' OR Fecha_Caducidad < '1900-01-01');

-- Eliminar registros vacíos/inválidos
DELETE FROM Historial_Lotes
WHERE (Lote IS NULL OR TRIM(Lote) = '' OR LOWER(TRIM(Lote)) IN ('nan', 'null', 'n/a', 'na', 'sin lote'))
   OR (Fecha_Caducidad IS NULL OR Fecha_Caducidad = '0000-00-00' OR Fecha_Caducidad < '1900-01-01');
