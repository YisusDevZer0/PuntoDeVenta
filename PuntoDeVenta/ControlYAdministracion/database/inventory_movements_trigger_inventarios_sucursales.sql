-- =====================================================
-- TRIGGER InventariosSucursales + inventory_movements
-- =====================================================
-- Extiende trg_after_insert_inventarios_sucursales para
-- registrar ajustes por inventario de sucursales.
--
-- PREREQUISITO: Ejecutar inventory_movements_create_table.sql
-- =====================================================

DROP TRIGGER IF EXISTS `trg_after_insert_inventarios_sucursales`;

DELIMITER $$
CREATE TRIGGER `trg_after_insert_inventarios_sucursales` AFTER INSERT ON `InventariosSucursales` FOR EACH ROW
BEGIN
    DECLARE v_existencias INT;

    SELECT Existencias_R INTO v_existencias
    FROM Stock_POS
    WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_Sucursal;

    IF v_existencias IS NOT NULL THEN
        UPDATE Stock_POS
        SET Existencias_R = v_existencias + NEW.Contabilizado
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_Sucursal;

        INSERT INTO inventory_movements (
            Fk_sucursal, ID_Prod_POS, Cod_Barra, Nombre_Prod,
            movement_type, reference_type, reference_id,
            quantity, stock_before, stock_after, reason, AgregadoPor
        ) VALUES (
            NEW.Fk_Sucursal, NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Nombre_Prod,
            'entry', 'count', CONCAT('IS-', CAST(NEW.IdProdCedis AS CHAR)),
            NEW.Contabilizado, v_existencias, v_existencias + NEW.Contabilizado,
            'Inventario sucursal', NEW.AgregadoPor
        );
    ELSE
        INSERT INTO Stock_POS (Cod_Barra, Existencias_R, Fk_Sucursal)
        VALUES (NEW.Cod_Barra, NEW.Contabilizado, NEW.Fk_Sucursal);

        INSERT INTO inventory_movements (
            Fk_sucursal, ID_Prod_POS, Cod_Barra, Nombre_Prod,
            movement_type, reference_type, reference_id,
            quantity, stock_before, stock_after, reason, AgregadoPor
        ) VALUES (
            NEW.Fk_Sucursal, NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Nombre_Prod,
            'entry', 'count', CONCAT('IS-', CAST(NEW.IdProdCedis AS CHAR)),
            NEW.Contabilizado, 0, NEW.Contabilizado,
            'Inventario sucursal (nuevo)', NEW.AgregadoPor
        );
    END IF;
END$$
DELIMITER ;
