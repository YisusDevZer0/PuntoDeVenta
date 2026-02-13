-- =====================================================
-- TRIGGERS TraspasosYNotasC + inventory_movements
-- =====================================================
-- Extiende after_insert_traspasosynotasc y suma_traspaso_a_la_sucursal
-- para registrar salida en origen y entrada en destino.
--
-- PREREQUISITO: Ejecutar inventory_movements_create_table.sql
-- =====================================================

-- ---- 1) Salida en sucursal origen ----
DROP TRIGGER IF EXISTS `after_insert_traspasosynotasc`;

DELIMITER $$
CREATE TRIGGER `after_insert_traspasosynotasc` AFTER INSERT ON `TraspasosYNotasC` FOR EACH ROW
BEGIN
    DECLARE v_existencias INT;

    SELECT Existencias_R INTO v_existencias
    FROM Stock_POS
    WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_sucursal;

    IF v_existencias IS NOT NULL THEN
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R - NEW.Cantidad,
            JustificacionAjuste = NEW.TipoDeMov
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_sucursal;

        -- Movimiento: salida por traspaso
        INSERT INTO inventory_movements (
            Fk_sucursal, Cod_Barra, Nombre_Prod,
            movement_type, reference_type, reference_id,
            quantity, stock_before, stock_after, reason, AgregadoPor
        ) VALUES (
            NEW.Fk_sucursal, NEW.Cod_Barra, NEW.Nombre_Prod,
            'exit', 'transfer', CAST(NEW.TraspaNotID AS CHAR),
            -NEW.Cantidad, v_existencias, v_existencias - NEW.Cantidad,
            'Traspaso enviado', NEW.AgregadoPor
        );
    ELSE
        INSERT INTO Stock_POS_Log (Cod_Barra, Fk_sucursal, Cantidad, TipoDeMov, Fecha, Mensaje)
        VALUES (NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad, NEW.TipoDeMov, NOW(), 'Intento fallido de actualizar stock: registro no encontrado');
    END IF;
END$$
DELIMITER ;

-- ---- 2) Entrada en sucursal destino ----
DROP TRIGGER IF EXISTS `suma_traspaso_a_la_sucursal`;

DELIMITER $$
CREATE TRIGGER `suma_traspaso_a_la_sucursal` AFTER INSERT ON `TraspasosYNotasC` FOR EACH ROW
BEGIN
    DECLARE v_stock_antes INT DEFAULT 0;

    IF EXISTS (
        SELECT 1 FROM Stock_POS
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_SucursalDestino
    ) THEN
        SELECT COALESCE(Existencias_R, 0) INTO v_stock_antes
        FROM Stock_POS
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_SucursalDestino
        LIMIT 1;

        UPDATE Stock_POS
        SET Existencias_R = Existencias_R + NEW.Cantidad,
            JustificacionAjuste = NEW.TipoDeMov
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_SucursalDestino;

        INSERT INTO inventory_movements (
            Fk_sucursal, Cod_Barra, Nombre_Prod,
            movement_type, reference_type, reference_id,
            quantity, stock_before, stock_after, reason, AgregadoPor
        ) VALUES (
            NEW.Fk_SucursalDestino, NEW.Cod_Barra, NEW.Nombre_Prod,
            'entry', 'transfer', CAST(NEW.TraspaNotID AS CHAR),
            NEW.Cantidad, v_stock_antes, v_stock_antes + NEW.Cantidad,
            'Traspaso recibido', NEW.AgregadoPor
        );
    ELSE
        INSERT INTO Stock_POS_Log (Cod_Barra, Fk_sucursal, Cantidad, TipoDeMov, Fecha, Mensaje)
        VALUES (NEW.Cod_Barra, NEW.Fk_SucursalDestino, NEW.Cantidad, NEW.TipoDeMov, NOW(), 'Intento fallido de actualizar stock: registro no encontrado');
    END IF;
END$$
DELIMITER ;
