-- =====================================================
-- TRIGGER InventariosStocks_Conteos + inventory_movements
-- =====================================================
-- Extiende trg_actualizar_stock_pos para registrar cada
-- corrección por conteo como movimiento.
--
-- PREREQUISITO: Ejecutar inventory_movements_create_table.sql
-- =====================================================

DROP TRIGGER IF EXISTS `trg_actualizar_stock_pos`;

DELIMITER $$
CREATE TRIGGER `trg_actualizar_stock_pos` AFTER INSERT ON `InventariosStocks_Conteos` FOR EACH ROW
BEGIN
    DECLARE error_msg VARCHAR(255);
    DECLARE v_stock_antes INT DEFAULT 0;

    -- Actualización de stock, campos Anaquel, Repisa, UltimoInventarioPor y FechaUltimoInventario
    IF NEW.Cod_Barra IS NOT NULL AND NEW.Cod_Barra != '' THEN
        -- Stock antes (para inventory_movements)
        SELECT COALESCE(Existencias_R, 0) INTO v_stock_antes
        FROM Stock_POS
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_sucursal
        LIMIT 1;

        UPDATE Stock_POS
        SET Existencias_R = IFNULL(Existencias_R, 0) + NEW.Diferencia,
            Anaquel = CASE WHEN NEW.Anaquel IS NOT NULL AND NEW.Anaquel != '' AND NEW.Anaquel != Anaquel THEN NEW.Anaquel ELSE Anaquel END,
            Repisa = CASE WHEN NEW.Repisa IS NOT NULL AND NEW.Repisa != '' AND NEW.Repisa != Repisa THEN NEW.Repisa ELSE Repisa END,
            UltimoInventarioPor = NEW.AgregadoPor,
            FechaUltimoInventario = NEW.FechaInventario
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_sucursal;

        IF ROW_COUNT() = 0 THEN
            SET error_msg = CONCAT('No se encontraron filas afectadas para Cod_Barra: ', NEW.Cod_Barra, ' y Fk_sucursal: ', NEW.Fk_sucursal);
            INSERT INTO registro_errores_Actualizacionanaqueles (mensaje_error) VALUES (error_msg);
        ELSEIF NEW.Diferencia != 0 THEN
            INSERT INTO inventory_movements (
                Fk_sucursal, Folio_Prod_Stock, ID_Prod_POS, Cod_Barra, Nombre_Prod,
                movement_type, reference_type, reference_id,
                quantity, stock_before, stock_after, reason, AgregadoPor
            ) VALUES (
                NEW.Fk_sucursal, NEW.Folio_Prod_Stock, NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Nombre_Prod,
                'count_correction', 'count', CAST(NEW.Folio_Prod_Stock AS CHAR),
                NEW.Diferencia, v_stock_antes, v_stock_antes + NEW.Diferencia,
                'Corrección por conteo físico', NEW.AgregadoPor
            );
        END IF;
    ELSE
        -- Actualizar usando ID_Prod_POS
        SELECT COALESCE(Existencias_R, 0) INTO v_stock_antes
        FROM Stock_POS
        WHERE ID_Prod_POS = NEW.ID_Prod_POS AND Fk_sucursal = NEW.Fk_sucursal
        LIMIT 1;

        UPDATE Stock_POS
        SET Existencias_R = IFNULL(Existencias_R, 0) + NEW.Diferencia,
            Anaquel = CASE WHEN NEW.Anaquel IS NOT NULL AND NEW.Anaquel != '' AND NEW.Anaquel != Anaquel THEN NEW.Anaquel ELSE Anaquel END,
            Repisa = CASE WHEN NEW.Repisa IS NOT NULL AND NEW.Repisa != '' AND NEW.Repisa != Repisa THEN NEW.Repisa ELSE Repisa END,
            UltimoInventarioPor = NEW.AgregadoPor,
            FechaUltimoInventario = NEW.FechaInventario
        WHERE ID_Prod_POS = NEW.ID_Prod_POS AND Fk_sucursal = NEW.Fk_sucursal;

        IF ROW_COUNT() = 0 THEN
            SET error_msg = CONCAT('No se encontraron filas afectadas para ID_Prod_POS: ', NEW.ID_Prod_POS, ' y Fk_sucursal: ', NEW.Fk_sucursal);
            INSERT INTO registro_errores_Actualizacionanaqueles (mensaje_error) VALUES (error_msg);
        ELSEIF NEW.Diferencia != 0 THEN
            INSERT INTO inventory_movements (
                Fk_sucursal, Folio_Prod_Stock, ID_Prod_POS, Cod_Barra, Nombre_Prod,
                movement_type, reference_type, reference_id,
                quantity, stock_before, stock_after, reason, AgregadoPor
            ) VALUES (
                NEW.Fk_sucursal, NEW.Folio_Prod_Stock, NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Nombre_Prod,
                'count_correction', 'count', CAST(NEW.Folio_Prod_Stock AS CHAR),
                NEW.Diferencia, v_stock_antes, v_stock_antes + NEW.Diferencia,
                'Corrección por conteo físico', NEW.AgregadoPor
            );
        END IF;
    END IF;
END$$
DELIMITER ;
