-- =====================================================
-- MODIFICAR TRIGGER DE VENTAS PARA NO ACTUALIZAR STOCK
-- CUANDO EL PRODUCTO TIENE CONTROL DE LOTES ACTIVADO
-- =====================================================
-- Este script modifica el trigger RestarExistenciasDespuesInsert
-- para que NO actualice Stock_POS cuando el producto tiene
-- Control_Lotes_Caducidad = 1, porque en ese caso el descuento
-- se maneja desde la función descontarLotesVenta() que ya
-- actualiza Stock_POS después de descontar de Historial_Lotes

DROP TRIGGER IF EXISTS `RestarExistenciasDespuesInsert`;

DELIMITER $$
CREATE TRIGGER `RestarExistenciasDespuesInsert` AFTER INSERT ON `Ventas_POS` FOR EACH ROW 
BEGIN
    DECLARE v_existencias INT;
    DECLARE v_error VARCHAR(255);
    DECLARE tiene_control_lotes TINYINT DEFAULT 0;

    -- Verificar si el producto tiene control de lotes activado
    SELECT COALESCE(Control_Lotes_Caducidad, 0) INTO tiene_control_lotes
    FROM Stock_POS
    WHERE ID_Prod_POS = NEW.ID_Prod_POS
      AND Fk_sucursal = NEW.Fk_sucursal
    LIMIT 1;

    -- Si el producto tiene control de lotes activado, NO actualizar Stock_POS aquí
    -- El descuento se maneja desde descontarLotesVenta() que actualiza tanto
    -- Historial_Lotes como Stock_POS
    IF tiene_control_lotes = 0 THEN
        -- Solo procesar si NO tiene control de lotes activado
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
    END IF;
    -- Si tiene_control_lotes = 1, simplemente no hacer nada aquí
    -- El descuento se maneja desde descontarLotesVenta() en PHP

END$$
DELIMITER ;
