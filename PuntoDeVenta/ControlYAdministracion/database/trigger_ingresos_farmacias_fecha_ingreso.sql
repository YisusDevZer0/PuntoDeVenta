-- =====================================================
-- TRIGGER IngresosFarmacias: actualizar Stock_POS con Fecha_Ingreso
-- =====================================================
-- Para que trg_AfterStockUpdate sincronice correctamente con Historial_Lotes,
-- Stock_POS debe tener Fecha_Ingreso actualizada en cada ingreso.
-- Ejecutar en la misma BD donde están Stock_POS e IngresosFarmacias.
-- (ej. USE `u858848268_doctorpez`;)
-- =====================================================

DROP TRIGGER IF EXISTS `actualizar_existencias`;

DELIMITER $$
CREATE TRIGGER `actualizar_existencias` AFTER INSERT ON `IngresosFarmacias` FOR EACH ROW
BEGIN
    UPDATE Stock_POS
    SET Existencias_R = Existencias_R + NEW.Contabilizado,
        Lote = NEW.Lote,
        Fecha_Caducidad = NEW.Fecha_Caducidad,
        Fecha_Ingreso = COALESCE(NEW.FechaInventario, CURDATE()),
        ActualizadoPor = NEW.AgregadoPor
    WHERE Cod_Barra = NEW.Cod_Barra
      AND Fk_sucursal = NEW.Fk_Sucursal;
END$$
DELIMITER ;
