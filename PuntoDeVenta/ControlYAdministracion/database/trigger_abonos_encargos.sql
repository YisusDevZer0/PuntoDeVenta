-- Trigger para actualizar el valor total de la caja cuando se registra un abono parcial
DELIMITER $$

-- Trigger AFTER INSERT para sumar el abono parcial al valor total de la caja
CREATE TRIGGER IF NOT EXISTS tr_abono_encargo_insert 
AFTER INSERT ON encargos
FOR EACH ROW
BEGIN
    -- Solo actualizar si hay un abono parcial mayor a 0
    IF NEW.abono_parcial > 0 THEN
        UPDATE Cajas 
        SET Valor_Total_Caja = Valor_Total_Caja + NEW.abono_parcial
        WHERE ID_Caja = NEW.Fk_Caja;
    END IF;
END$$

-- Trigger AFTER UPDATE para manejar cambios en el abono parcial
CREATE TRIGGER IF NOT EXISTS tr_abono_encargo_update 
AFTER UPDATE ON encargos
FOR EACH ROW
BEGIN
    -- Si el abono parcial cambió, ajustar el valor de la caja
    IF OLD.abono_parcial != NEW.abono_parcial THEN
        -- Restar el abono anterior
        IF OLD.abono_parcial > 0 THEN
            UPDATE Cajas 
            SET Valor_Total_Caja = Valor_Total_Caja - OLD.abono_parcial
            WHERE ID_Caja = OLD.Fk_Caja;
        END IF;
        
        -- Sumar el nuevo abono
        IF NEW.abono_parcial > 0 THEN
            UPDATE Cajas 
            SET Valor_Total_Caja = Valor_Total_Caja + NEW.abono_parcial
            WHERE ID_Caja = NEW.Fk_Caja;
        END IF;
    END IF;
END$$

-- Trigger AFTER DELETE para restar el abono si se elimina un encargo
CREATE TRIGGER IF NOT EXISTS tr_abono_encargo_delete 
AFTER DELETE ON encargos
FOR EACH ROW
BEGIN
    -- Restar el abono parcial del valor total de la caja
    IF OLD.abono_parcial > 0 THEN
        UPDATE Cajas 
        SET Valor_Total_Caja = Valor_Total_Caja - OLD.abono_parcial
        WHERE ID_Caja = OLD.Fk_Caja;
    END IF;
END$$

DELIMITER ; 