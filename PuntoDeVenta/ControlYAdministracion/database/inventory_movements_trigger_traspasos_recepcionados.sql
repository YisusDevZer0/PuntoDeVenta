-- =====================================================
-- TRIGGER Traspasos_Recepcionados + inventory_movements
-- =====================================================
-- Extiende after_insert_traspasos_recepcionados para
-- registrar entrada al recibir traspaso (flujo Traspasos_generados).
--
-- PREREQUISITO: Ejecutar inventory_movements_create_table.sql
-- =====================================================

DROP TRIGGER IF EXISTS `after_insert_traspasos_recepcionados`;

DELIMITER $$
CREATE TRIGGER `after_insert_traspasos_recepcionados` AFTER INSERT ON `Traspasos_Recepcionados` FOR EACH ROW
BEGIN
    DECLARE v_stock_antes INT DEFAULT 0;

    SELECT COALESCE(Existencias_R, 0) INTO v_stock_antes
    FROM Stock_POS
    WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_SucDestino
    LIMIT 1;

    UPDATE Stock_POS
    SET Existencias_R = Existencias_R + NEW.Cantidad_Enviada
    WHERE Stock_POS.Cod_Barra = NEW.Cod_Barra
    AND Stock_POS.Fk_sucursal = NEW.Fk_SucDestino;

    INSERT INTO inventory_movements (
        Fk_sucursal, Cod_Barra, Nombre_Prod,
        movement_type, reference_type, reference_id,
        quantity, stock_before, stock_after, reason, AgregadoPor
    ) VALUES (
        NEW.Fk_SucDestino, NEW.Cod_Barra, NEW.Nombre_Prod,
        'entry', 'transfer', CONCAT('TR-', CAST(NEW.ID_Traspaso_Generado AS CHAR)),
        NEW.Cantidad_Enviada, v_stock_antes, v_stock_antes + NEW.Cantidad_Enviada,
        'Traspaso recepcionado', NEW.AgregadoPor
    );
END$$
DELIMITER ;
