-- =====================================================
-- TRIGGER IngresosFarmacias: actualizar Stock_POS con Fecha_Ingreso
-- =====================================================
-- Para que trg_AfterStockUpdate sincronice correctamente con Historial_Lotes,
-- Stock_POS debe tener Fecha_Ingreso actualizada en cada ingreso.
-- Ejecutar en la misma BD donde están Stock_POS e IngresosFarmacias.
-- (ej. USE `u858848268_doctorpez`;)
--
-- ¿AFECTA A OTROS LUGARES?
-- NO. Este trigger SOLO se ejecuta cuando hay INSERT en IngresosFarmacias.
--
-- IMPORTANTE: RegistraIngresoMedicamentosFarmacia.php ahora actualiza Stock_POS
-- e Historial_Lotes desde PHP. Si tienes este trigger instalado, las existencias
-- se sumarían DOS VECES. Debes elegir UNA de las dos:
--   A) Usar solo PHP: NO ejecutes este trigger (o haz DROP TRIGGER actualizar_existencias).
--   B) Usar solo trigger: comenta o quita en PHP el bloque que actualiza Stock_POS/Historial_Lotes.
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
