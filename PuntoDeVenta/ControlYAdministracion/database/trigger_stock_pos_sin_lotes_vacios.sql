-- =====================================================
-- TRIGGERS STOCK_POS: NO CREAR REGISTROS VACÍOS EN HISTORIAL_LOTES
-- =====================================================
-- Evita que se inserten/actualicen filas en Historial_Lotes cuando:
-- - Lote es NULL, vacío, o inválido ('NaN', 'nan', 'null', etc.)
-- - Fecha_Caducidad es NULL o fecha inválida (0000-00-00)
--
-- Regla: solo escribir en Historial_Lotes cuando exista un registro
-- previo válido (lote + fecha de caducidad real). Si no hay lote/caducidad
-- válidos, no crear contenido vacío ni descontar desde Historial_Lotes.
--
-- IMPORTANTE: Selecciona tu base de datos antes de ejecutar (o elige la BD en phpMyAdmin).
USE `u858848268_doctorpez`;

-- --------------- trg_AfterStockInsert ---------------
DROP TRIGGER IF EXISTS `trg_AfterStockInsert`;

DELIMITER $$
CREATE TRIGGER `trg_AfterStockInsert` AFTER INSERT ON `Stock_POS` FOR EACH ROW
BEGIN
    DECLARE existe_lote INT;
    DECLARE lote_valido TINYINT DEFAULT 0;
    DECLARE fecha_valida TINYINT DEFAULT 0;

    -- Solo procesar si Lote es válido (no NULL, no vacío, no 'NaN' ni similares)
    SET lote_valido = (NEW.Lote IS NOT NULL
        AND TRIM(NEW.Lote) != ''
        AND LOWER(TRIM(NEW.Lote)) NOT IN ('nan', 'null', 'n/a', 'na', 'sin lote'));

    -- Solo procesar si Fecha_Caducidad es válida (no NULL ni fecha placeholder)
    SET fecha_valida = (NEW.Fecha_Caducidad IS NOT NULL
        AND NEW.Fecha_Caducidad > '1900-01-01'
        AND NEW.Fecha_Caducidad != '0000-00-00');

    IF lote_valido = 1 AND fecha_valida = 1 THEN
        SELECT COUNT(*) INTO existe_lote
        FROM Historial_Lotes
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND Lote = NEW.Lote
          AND Fk_sucursal = NEW.Fk_sucursal;

        IF existe_lote = 0 THEN
            INSERT INTO Historial_Lotes (
                ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico
            ) VALUES (
                NEW.ID_Prod_POS, NEW.Fk_sucursal, NEW.Lote, NEW.Fecha_Caducidad, NEW.Fecha_Ingreso, NEW.Existencias_R, NEW.AgregadoPor
            );
        ELSE
            UPDATE Historial_Lotes
            SET Existencias = NEW.Existencias_R,
                Fecha_Ingreso = NEW.Fecha_Ingreso,
                Usuario_Modifico = NEW.AgregadoPor
            WHERE ID_Prod_POS = NEW.ID_Prod_POS
              AND Lote = NEW.Lote
              AND Fk_sucursal = NEW.Fk_sucursal;
        END IF;
    END IF;
END$$
DELIMITER ;

-- --------------- trg_AfterStockUpdate ---------------
DROP TRIGGER IF EXISTS `trg_AfterStockUpdate`;

DELIMITER $$
CREATE TRIGGER `trg_AfterStockUpdate` AFTER UPDATE ON `Stock_POS` FOR EACH ROW
BEGIN
    DECLARE existe_lote INT;
    DECLARE tiene_control_lotes TINYINT DEFAULT 0;
    DECLARE lote_valido TINYINT DEFAULT 0;
    DECLARE fecha_valida TINYINT DEFAULT 0;

    -- Si el producto tiene control de lotes activado, el manejo se hace desde Historial_Lotes (descontar_lotes_venta)
    SELECT COALESCE(Control_Lotes_Caducidad, 0) INTO tiene_control_lotes
    FROM Stock_POS
    WHERE ID_Prod_POS = NEW.ID_Prod_POS
      AND Fk_sucursal = NEW.Fk_sucursal
    LIMIT 1;

    -- Solo procesar si NO tiene control de lotes (cuando tiene control, se maneja desde PHP)
    IF tiene_control_lotes = 0 THEN
        -- Solo escribir en Historial_Lotes si Lote y Fecha_Caducidad son válidos
        SET lote_valido = (NEW.Lote IS NOT NULL
            AND TRIM(NEW.Lote) != ''
            AND LOWER(TRIM(NEW.Lote)) NOT IN ('nan', 'null', 'n/a', 'na', 'sin lote'));

        SET fecha_valida = (NEW.Fecha_Caducidad IS NOT NULL
            AND NEW.Fecha_Caducidad > '1900-01-01'
            AND NEW.Fecha_Caducidad != '0000-00-00');

        IF lote_valido = 1 AND fecha_valida = 1 THEN
            SELECT COUNT(*) INTO existe_lote
            FROM Historial_Lotes
            WHERE ID_Prod_POS = NEW.ID_Prod_POS
              AND Lote = NEW.Lote
              AND Fk_sucursal = NEW.Fk_sucursal;

            IF existe_lote = 0 THEN
                INSERT INTO Historial_Lotes (
                    ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico
                ) VALUES (
                    NEW.ID_Prod_POS, NEW.Fk_sucursal, NEW.Lote, NEW.Fecha_Caducidad, NEW.Fecha_Ingreso, NEW.Existencias_R, COALESCE(NEW.ActualizadoPor, NEW.AgregadoPor)
                );
            ELSE
                UPDATE Historial_Lotes
                SET Existencias = NEW.Existencias_R,
                    Fecha_Ingreso = NEW.Fecha_Ingreso,
                    Usuario_Modifico = COALESCE(NEW.ActualizadoPor, NEW.AgregadoPor)
                WHERE ID_Prod_POS = NEW.ID_Prod_POS
                  AND Lote = NEW.Lote
                  AND Fk_sucursal = NEW.Fk_sucursal;
            END IF;
        END IF;
    END IF;
END$$
DELIMITER ;
