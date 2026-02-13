-- =====================================================
-- REVERT: Quitar inventory_movements de los triggers
-- =====================================================
-- Restaura los triggers a su versión ORIGINAL (sin INSERT
-- en inventory_movements). Usar si algo sale mal.
--
-- La tabla inventory_movements NO se elimina (queda con
-- datos históricos). Para borrarla: DROP TABLE inventory_movements;
-- =====================================================

-- ---- 1) VENTAS - RestarExistenciasDespuesInsert ----
DROP TRIGGER IF EXISTS `RestarExistenciasDespuesInsert`;

DELIMITER $$
CREATE TRIGGER `RestarExistenciasDespuesInsert` AFTER INSERT ON `Ventas_POS` FOR EACH ROW
BEGIN
    DECLARE v_stock INT DEFAULT NULL;
    DECLARE v_error VARCHAR(255);
    DECLARE v_a_restar_stock INT DEFAULT 0;
    DECLARE v_cantidad_restante INT DEFAULT 0;
    DECLARE v_id_historial INT;
    DECLARE v_lote VARCHAR(100);
    DECLARE v_fecha_caducidad DATE;
    DECLARE v_exist_lote INT;
    DECLARE v_a_descontar INT;
    DECLARE v_done INT DEFAULT 0;

    DECLARE cur_lotes CURSOR FOR
        SELECT hl.ID_Historial, hl.Lote, hl.Fecha_Caducidad, hl.Existencias
        FROM Historial_Lotes hl
        WHERE hl.ID_Prod_POS = NEW.ID_Prod_POS
          AND hl.Fk_sucursal = NEW.Fk_sucursal
          AND hl.Existencias > 0
          AND hl.Lote IS NOT NULL AND TRIM(hl.Lote) != ''
          AND LOWER(TRIM(hl.Lote)) NOT IN ('nan', 'null', 'n/a', 'na', 'sin lote')
          AND hl.Fecha_Caducidad IS NOT NULL
          AND hl.Fecha_Caducidad > '1900-01-01'
          AND hl.Fecha_Caducidad != '0000-00-00'
        ORDER BY
          CASE
            WHEN DATEDIFF(hl.Fecha_Caducidad, CURDATE()) < 0 THEN 0
            WHEN DATEDIFF(hl.Fecha_Caducidad, CURDATE()) <= 15 THEN 1
            ELSE 2
          END,
          hl.Fecha_Caducidad ASC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

    SELECT Existencias_R INTO v_stock
    FROM Stock_POS
    WHERE ID_Prod_POS = NEW.ID_Prod_POS
      AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
      AND Fk_sucursal = NEW.Fk_sucursal
    LIMIT 1;

    IF v_stock IS NULL THEN
        SET v_error = 'Producto no encontrado en inventario.';
        INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
        VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
    ELSE
        IF v_stock < NEW.Cantidad_Venta THEN
            SET v_error = 'No hay suficientes existencias.';
            INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
            VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
        END IF;

        SET v_a_restar_stock = LEAST(NEW.Cantidad_Venta, v_stock);
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R - v_a_restar_stock
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
          AND Fk_sucursal = NEW.Fk_sucursal;

        SET v_cantidad_restante = LEAST(NEW.Cantidad_Venta, (
            SELECT COALESCE(SUM(hl.Existencias), 0)
            FROM Historial_Lotes hl
            WHERE hl.ID_Prod_POS = NEW.ID_Prod_POS
              AND hl.Fk_sucursal = NEW.Fk_sucursal
              AND hl.Existencias > 0
              AND hl.Lote IS NOT NULL AND TRIM(hl.Lote) != ''
              AND LOWER(TRIM(hl.Lote)) NOT IN ('nan', 'null', 'n/a', 'na', 'sin lote')
              AND hl.Fecha_Caducidad IS NOT NULL
              AND hl.Fecha_Caducidad > '1900-01-01'
              AND hl.Fecha_Caducidad != '0000-00-00'
        ));
        SET v_done = 0;
        OPEN cur_lotes;

        read_lotes: LOOP
            FETCH cur_lotes INTO v_id_historial, v_lote, v_fecha_caducidad, v_exist_lote;
            IF v_done = 1 OR v_cantidad_restante <= 0 THEN
                LEAVE read_lotes;
            END IF;
            SET v_a_descontar = LEAST(v_cantidad_restante, v_exist_lote);
            UPDATE Historial_Lotes
            SET Existencias = Existencias - v_a_descontar,
                Usuario_Modifico = NEW.AgregadoPor,
                Fecha_Registro = NOW()
            WHERE ID_Historial = v_id_historial;
            SET v_cantidad_restante = v_cantidad_restante - v_a_descontar;
        END LOOP;

        CLOSE cur_lotes;
    END IF;
END$$
DELIMITER ;

-- ---- 2) INGRESOS - actualizar_existencias ----
DROP TRIGGER IF EXISTS `actualizar_existencias`;

DELIMITER $$
CREATE TRIGGER `actualizar_existencias` AFTER INSERT ON `IngresosFarmacias` FOR EACH ROW
BEGIN
    DECLARE v_count INT DEFAULT 0;
    DECLARE v_lote_ok TINYINT DEFAULT 0;
    DECLARE v_fecha_ok TINYINT DEFAULT 0;
    DECLARE v_fecha_ingreso DATE DEFAULT COALESCE(NEW.FechaInventario, CURDATE());

    UPDATE Stock_POS
    SET Existencias_R = Existencias_R + NEW.Contabilizado,
        Lote = NEW.Lote,
        Fecha_Caducidad = NEW.Fecha_Caducidad,
        Fecha_Ingreso = v_fecha_ingreso,
        ActualizadoPor = NEW.AgregadoPor
    WHERE Cod_Barra = NEW.Cod_Barra
      AND Fk_sucursal = NEW.Fk_Sucursal;

    SET v_lote_ok = (CHAR_LENGTH(TRIM(IFNULL(NEW.Lote, ''))) > 0);
    SET v_fecha_ok = (NEW.Fecha_Caducidad IS NOT NULL AND NEW.Fecha_Caducidad >= '1900-01-01');

    IF v_lote_ok THEN
        SELECT COUNT(*) INTO v_count
        FROM Historial_Lotes
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND Lote = NEW.Lote
          AND Fk_sucursal = NEW.Fk_Sucursal;

        IF v_count > 0 THEN
            UPDATE Historial_Lotes
            SET Existencias = Existencias + NEW.Contabilizado,
                Fecha_Caducidad = IF(v_fecha_ok, NEW.Fecha_Caducidad, Fecha_Caducidad),
                Fecha_Ingreso = v_fecha_ingreso,
                Usuario_Modifico = NEW.AgregadoPor
            WHERE ID_Prod_POS = NEW.ID_Prod_POS
              AND Lote = NEW.Lote
              AND Fk_sucursal = NEW.Fk_Sucursal;
        ELSE
            INSERT INTO Historial_Lotes (ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico)
            VALUES (
                NEW.ID_Prod_POS,
                NEW.Fk_Sucursal,
                NEW.Lote,
                IF(v_fecha_ok, NEW.Fecha_Caducidad, v_fecha_ingreso),
                v_fecha_ingreso,
                NEW.Contabilizado,
                NEW.AgregadoPor
            );
        END IF;
    END IF;
END$$
DELIMITER ;

-- ---- 3) CONTEOS - trg_actualizar_stock_pos ----
DROP TRIGGER IF EXISTS `trg_actualizar_stock_pos`;

DELIMITER $$
CREATE TRIGGER `trg_actualizar_stock_pos` AFTER INSERT ON `InventariosStocks_Conteos` FOR EACH ROW
BEGIN
    DECLARE error_msg VARCHAR(255);

    IF NEW.Cod_Barra IS NOT NULL AND NEW.Cod_Barra != '' THEN
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
        END IF;
    ELSE
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
        END IF;
    END IF;
END$$
DELIMITER ;

-- ---- 4) TRASPASOS - after_insert_traspasosynotasc ----
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
    ELSE
        INSERT INTO Stock_POS_Log (Cod_Barra, Fk_sucursal, Cantidad, TipoDeMov, Fecha, Mensaje)
        VALUES (NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad, NEW.TipoDeMov, NOW(), 'Intento fallido de actualizar stock: registro no encontrado');
    END IF;
END$$
DELIMITER ;

-- ---- 5) TRASPASOS - suma_traspaso_a_la_sucursal ----
DROP TRIGGER IF EXISTS `suma_traspaso_a_la_sucursal`;

DELIMITER $$
CREATE TRIGGER `suma_traspaso_a_la_sucursal` AFTER INSERT ON `TraspasosYNotasC` FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM Stock_POS
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_SucursalDestino
    ) THEN
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R + NEW.Cantidad,
            JustificacionAjuste = NEW.TipoDeMov
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_SucursalDestino;
    ELSE
        INSERT INTO Stock_POS_Log (Cod_Barra, Fk_sucursal, Cantidad, TipoDeMov, Fecha, Mensaje)
        VALUES (NEW.Cod_Barra, NEW.Fk_SucursalDestino, NEW.Cantidad, NEW.TipoDeMov, NOW(), 'Intento fallido de actualizar stock: registro no encontrado');
    END IF;
END$$
DELIMITER ;

-- ---- 6) TRASPASOS RECEPCIONADOS - after_insert_traspasos_recepcionados ----
DROP TRIGGER IF EXISTS `after_insert_traspasos_recepcionados`;

DELIMITER $$
CREATE TRIGGER `after_insert_traspasos_recepcionados` AFTER INSERT ON `Traspasos_Recepcionados` FOR EACH ROW
BEGIN
    UPDATE Stock_POS
    SET Existencias_R = Existencias_R + NEW.Cantidad_Enviada
    WHERE Stock_POS.Cod_Barra = NEW.Cod_Barra
    AND Stock_POS.Fk_sucursal = NEW.Fk_SucDestino;
END$$
DELIMITER ;

-- ---- 7) INVENTARIOS SUCURSALES - trg_after_insert_inventarios_sucursales ----
DROP TRIGGER IF EXISTS `trg_after_insert_inventarios_sucursales`;

DELIMITER $$
CREATE TRIGGER `trg_after_insert_inventarios_sucursales` AFTER INSERT ON `InventariosSucursales` FOR EACH ROW
BEGIN
    DECLARE v_existencias INT;

    SELECT Existencias_R
    INTO v_existencias
    FROM Stock_POS
    WHERE Cod_Barra = NEW.Cod_Barra AND Fk_Sucursal = NEW.Fk_Sucursal;

    IF v_existencias IS NOT NULL THEN
        UPDATE Stock_POS
        SET Existencias_R = v_existencias + NEW.Contabilizado
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_Sucursal = NEW.Fk_Sucursal;
    ELSE
        INSERT INTO Stock_POS (Cod_Barra, Existencias_R, Fk_Sucursal)
        VALUES (NEW.Cod_Barra, NEW.Contabilizado, NEW.Fk_Sucursal);
    END IF;
END$$
DELIMITER ;
