-- =====================================================
-- TRIGGER IngresosFarmacias + inventory_movements
-- =====================================================
-- Extiende actualizar_existencias para registrar cada
-- ingreso como movimiento de inventario (entrada).
--
-- PREREQUISITO: Ejecutar inventory_movements_create_table.sql
-- =====================================================

DROP TRIGGER IF EXISTS `actualizar_existencias`;

DELIMITER $$
CREATE TRIGGER `actualizar_existencias` AFTER INSERT ON `IngresosFarmacias` FOR EACH ROW
BEGIN
    DECLARE v_count INT DEFAULT 0;
    DECLARE v_lote_ok TINYINT DEFAULT 0;
    DECLARE v_fecha_ok TINYINT DEFAULT 0;
    DECLARE v_stock_antes INT DEFAULT 0;
    -- Fecha de ingreso al inventario
    DECLARE v_fecha_ingreso DATE DEFAULT COALESCE(NEW.FechaInventario, CURDATE());

    -- Stock antes del update (para inventory_movements)
    SELECT COALESCE(Existencias_R, 0) INTO v_stock_antes
    FROM Stock_POS
    WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_Sucursal
    LIMIT 1;

    -- 1) Actualizar Stock_POS
    UPDATE Stock_POS
    SET Existencias_R = Existencias_R + NEW.Contabilizado,
        Lote = NEW.Lote,
        Fecha_Caducidad = NEW.Fecha_Caducidad,
        Fecha_Ingreso = v_fecha_ingreso,
        ActualizadoPor = NEW.AgregadoPor
    WHERE Cod_Barra = NEW.Cod_Barra
      AND Fk_sucursal = NEW.Fk_Sucursal;

    -- 2) Registrar movimiento de inventario (entrada por compra/ingreso)
    INSERT INTO inventory_movements (
        Fk_sucursal, ID_Prod_POS, Cod_Barra, Nombre_Prod,
        movement_type, reference_type, reference_id,
        quantity, stock_before, stock_after, reason, AgregadoPor
    ) VALUES (
        NEW.Fk_Sucursal, NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Nombre_Prod,
        'entry', 'purchase', CONCAT(CAST(NEW.NumOrden AS CHAR), '-', NEW.Cod_Barra),
        NEW.Contabilizado, v_stock_antes, v_stock_antes + NEW.Contabilizado,
        'Ingreso de mercancía', NEW.AgregadoPor
    );

    -- 3) Historial_Lotes: escribir cuando haya lote no vacío
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
