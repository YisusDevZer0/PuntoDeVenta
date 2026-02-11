-- =====================================================
-- QUITAR trigger de Ingresos Farmacias (solo si dejas de usarlo)
-- =====================================================
-- Ahora el flujo recomendado es: PHP solo INSERT + trigger actualiza Stock_POS
-- e Historial_Lotes (trigger_ingresos_farmacias_fecha_ingreso.sql).
-- Si en algún momento quitaste el trigger y usabas solo PHP, ejecutabas este
-- script. Si vuelves a usar el trigger, ejecuta trigger_ingresos_farmacias_fecha_ingreso.sql.
-- =====================================================

DROP TRIGGER IF EXISTS `actualizar_existencias`;
