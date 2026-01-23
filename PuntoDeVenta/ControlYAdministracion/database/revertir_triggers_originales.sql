-- =====================================================
-- REVERTIR TRIGGERS A SU FUNCIONAMIENTO ORIGINAL
-- =====================================================
-- Este script revierte los triggers a su funcionamiento original
-- donde se descuenta el stock normalmente al vender

-- Revertir trigger de Ventas_POS
DROP TRIGGER IF EXISTS `RestarExistenciasDespuesInsert`;

DELIMITER $$
CREATE TRIGGER `RestarExistenciasDespuesInsert` AFTER INSERT ON `Ventas_POS` FOR EACH ROW 
BEGIN
    DECLARE v_existencias INT;
    DECLARE v_error VARCHAR(255);

    -- Buscar las existencias del producto en Stock_POS
    SELECT Existencias_R INTO v_existencias
    FROM Stock_POS
    WHERE ID_Prod_POS = NEW.ID_Prod_POS
      AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
      AND Fk_sucursal = NEW.Fk_sucursal
    LIMIT 1;

    -- Verificar si el producto existe en el inventario
    IF v_existencias IS NULL THEN
        SET v_error = 'Producto no encontrado en inventario.';
        INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
        VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
    ELSEIF v_existencias < NEW.Cantidad_Venta THEN
        -- Verificar si hay suficiente stock
        SET v_error = 'No hay suficientes existencias.';
        INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
        VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
    ELSE
        -- Restar la cantidad vendida del inventario
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R - NEW.Cantidad_Venta
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
          AND Fk_sucursal = NEW.Fk_sucursal;
    END IF;
END$$
DELIMITER ;

-- Revertir trigger de Stock_POS
DROP TRIGGER IF EXISTS `trg_AfterStockUpdate`;

DELIMITER $$
CREATE TRIGGER `trg_AfterStockUpdate` AFTER UPDATE ON `Stock_POS` FOR EACH ROW 
BEGIN
    DECLARE existe_lote INT;

    -- Verificar si el producto, lote y sucursal ya existen en Historial_Lotes
    -- Solo si hay un lote válido (no NULL y no vacío)
    IF NEW.Lote IS NOT NULL AND NEW.Lote != '' THEN
        SELECT COUNT(*) INTO existe_lote 
        FROM Historial_Lotes 
        WHERE ID_Prod_POS = NEW.ID_Prod_POS 
          AND Lote = NEW.Lote
          AND Fk_sucursal = NEW.Fk_sucursal;

        IF existe_lote = 0 THEN
            -- Insertar nuevo lote en el historial
            INSERT INTO Historial_Lotes (
                ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico
            ) VALUES (
                NEW.ID_Prod_POS, NEW.Fk_sucursal, NEW.Lote, NEW.Fecha_Caducidad, NEW.Fecha_Ingreso, NEW.Existencias_R, COALESCE(NEW.ActualizadoPor, NEW.AgregadoPor)
            );
        ELSE
            -- Si el lote ya existe en la misma sucursal, actualizar existencias
            UPDATE Historial_Lotes
            SET Existencias = NEW.Existencias_R,
                Fecha_Ingreso = NEW.Fecha_Ingreso,
                Usuario_Modifico = COALESCE(NEW.ActualizadoPor, NEW.AgregadoPor)
            WHERE ID_Prod_POS = NEW.ID_Prod_POS 
              AND Lote = NEW.Lote
              AND Fk_sucursal = NEW.Fk_sucursal;
        END IF;
    END IF;
END$$
DELIMITER ;
