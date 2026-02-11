-- =====================================================
-- TRIGGER DE VENTAS: SIEMPRE DESCONTAR STOCK
-- =====================================================
-- El trigger SIEMPRE resta del Stock_POS (proceso normal).
-- Si el producto tiene registros en Historial_Lotes, el descuento
-- allí lo hace descontar_lotes_venta.php (solo resta en Historial_Lotes,
-- NO vuelve a tocar Stock_POS para no descontar dos veces).

DROP TRIGGER IF EXISTS `RestarExistenciasDespuesInsert`;

DELIMITER $$
CREATE TRIGGER `RestarExistenciasDespuesInsert` AFTER INSERT ON `Ventas_POS` FOR EACH ROW 
BEGIN
    DECLARE v_existencias INT;
    DECLARE v_error VARCHAR(255);

    -- Siempre buscar existencias y restar del stock (proceso normal)
    SELECT Existencias_R INTO v_existencias
    FROM Stock_POS
    WHERE ID_Prod_POS = NEW.ID_Prod_POS
      AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
      AND Fk_sucursal = NEW.Fk_sucursal
    LIMIT 1;

    IF v_existencias IS NULL THEN
        SET v_error = 'Producto no encontrado en inventario.';
        INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
        VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
    ELSEIF v_existencias < NEW.Cantidad_Venta THEN
        SET v_error = 'No hay suficientes existencias.';
        INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
        VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
    ELSE
        -- Restar siempre del inventario (stock normal)
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R - NEW.Cantidad_Venta
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
          AND Fk_sucursal = NEW.Fk_sucursal;
    END IF;
END$$
DELIMITER ;
