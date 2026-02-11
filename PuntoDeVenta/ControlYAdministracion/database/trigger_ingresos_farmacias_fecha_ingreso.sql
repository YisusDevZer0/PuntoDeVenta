-- =====================================================
-- TRIGGER IngresosFarmacias: Stock_POS + Historial_Lotes (todo en BD, rollback si falla)
-- =====================================================
-- Ventaja: todo atómico. Si falla el UPDATE Stock_POS o Historial_Lotes,
-- el INSERT en IngresosFarmacias se revierte (rollback automático).
-- PHP solo debe hacer INSERT en IngresosFarmacias dentro de una transacción;
-- no debe actualizar Stock_POS ni Historial_Lotes.
--
-- Ejecutar en la misma BD donde están Stock_POS, IngresosFarmacias e Historial_Lotes.
-- (ej. USE `u858848268_doctorpez`;)
-- =====================================================

DROP TRIGGER IF EXISTS `actualizar_existencias`;

DELIMITER $$
CREATE TRIGGER `actualizar_existencias` AFTER INSERT ON `IngresosFarmacias` FOR EACH ROW
BEGIN
    DECLARE v_count INT DEFAULT 0;
    DECLARE v_fecha_ingreso DATE DEFAULT COALESCE(NEW.FechaInventario, CURDATE());

    -- 1) Actualizar Stock_POS (si falla, todo el INSERT se revierte)
    UPDATE Stock_POS
    SET Existencias_R = Existencias_R + NEW.Contabilizado,
        Lote = NEW.Lote,
        Fecha_Caducidad = NEW.Fecha_Caducidad,
        Fecha_Ingreso = v_fecha_ingreso,
        ActualizadoPor = NEW.AgregadoPor
    WHERE Cod_Barra = NEW.Cod_Barra
      AND Fk_sucursal = NEW.Fk_Sucursal;

    -- 2) Historial_Lotes cuando lote no es S/L/vacío y fecha es válida
    -- Condición permisiva: solo excluir lotes placeholder y fechas inválidas
    IF CHAR_LENGTH(IFNULL(TRIM(NEW.Lote), '')) > 0
       AND LOWER(IFNULL(TRIM(NEW.Lote), '')) NOT IN ('s/l', 'nan', 'null', 'n/a', 'na', 'sin lote')
       AND IFNULL(NEW.Fecha_Caducidad, '0000-00-00') > '1900-01-01'
    THEN
        SELECT COUNT(*) INTO v_count
        FROM Historial_Lotes
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND Lote = NEW.Lote
          AND Fk_sucursal = NEW.Fk_Sucursal;

        IF v_count > 0 THEN
            UPDATE Historial_Lotes
            SET Existencias = Existencias + NEW.Contabilizado,
                Fecha_Ingreso = v_fecha_ingreso,
                Usuario_Modifico = NEW.AgregadoPor
            WHERE ID_Prod_POS = NEW.ID_Prod_POS
              AND Lote = NEW.Lote
              AND Fk_sucursal = NEW.Fk_Sucursal;
        ELSE
            INSERT INTO Historial_Lotes (ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico)
            VALUES (NEW.ID_Prod_POS, NEW.Fk_Sucursal, NEW.Lote, NEW.Fecha_Caducidad, v_fecha_ingreso, NEW.Contabilizado, NEW.AgregadoPor);
        END IF;
    END IF;
END$$
DELIMITER ;
