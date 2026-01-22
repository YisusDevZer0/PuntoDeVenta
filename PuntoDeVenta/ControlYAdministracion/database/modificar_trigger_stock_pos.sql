-- =====================================================
-- MODIFICAR TRIGGER PARA EVITAR CREAR LOTES DUPLICADOS
-- =====================================================
-- Este script modifica el trigger trg_AfterStockUpdate
-- para que NO cree nuevas filas en Historial_Lotes cuando:
-- 1. El producto tiene Control_Lotes_Caducidad = 1 (control desde Historial_Lotes)
-- 2. El campo Lote está NULL o vacío
-- 
-- Esto evita que el trigger interfiera con el sistema
-- de descuento automático FEFO

DROP TRIGGER IF EXISTS `trg_AfterStockUpdate`;

DELIMITER $$
CREATE TRIGGER `trg_AfterStockUpdate` AFTER UPDATE ON `Stock_POS` FOR EACH ROW 
BEGIN
    DECLARE existe_lote INT;
    DECLARE tiene_control_lotes TINYINT DEFAULT 0;

    -- Verificar si el producto tiene control de lotes activado
    -- Si tiene control activado, el manejo se hace desde Historial_Lotes
    -- y NO debemos crear/actualizar desde aquí
    SELECT COALESCE(Control_Lotes_Caducidad, 0) INTO tiene_control_lotes
    FROM Stock_POS
    WHERE ID_Prod_POS = NEW.ID_Prod_POS 
      AND Fk_sucursal = NEW.Fk_sucursal
    LIMIT 1;

    -- Si el producto tiene control de lotes activado, NO hacer nada
    -- El descuento se maneja desde la función descontarLotesVenta()
    IF tiene_control_lotes = 0 THEN
        -- Solo procesar si NO tiene control de lotes activado
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
    END IF;
    -- Si tiene_control_lotes = 1, simplemente no hacer nada (el control se hace desde Historial_Lotes)

END$$
DELIMITER ;
