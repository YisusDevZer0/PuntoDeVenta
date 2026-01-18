-- Trigger para actualizar el valor total de la caja cuando se registra un pago de servicio
-- Suma el costo + comisión del servicio a la caja
DELIMITER $$

-- Trigger AFTER INSERT para sumar costo + comisión al valor total de la caja
DROP TRIGGER IF EXISTS tr_pago_servicio_insert;
CREATE TRIGGER tr_pago_servicio_insert 
AFTER INSERT ON PagosServicios
FOR EACH ROW
BEGIN
    DECLARE v_comision DECIMAL(10, 2) DEFAULT 0.00;
    DECLARE v_total_a_sumar DECIMAL(10, 2) DEFAULT 0.00;
    
    -- Obtener la comisión del servicio desde ListadoServicios usando el nombre del servicio
    SELECT IFNULL(Comision, 0.00) INTO v_comision
    FROM ListadoServicios
    WHERE Servicio = NEW.Servicio
    LIMIT 1;
    
    -- Calcular el total a sumar: costo + comisión
    SET v_total_a_sumar = IFNULL(NEW.costo, 0.00) + IFNULL(v_comision, 0.00);
    
    -- Solo actualizar si hay un valor a sumar mayor a 0 y existe la caja
    IF v_total_a_sumar > 0 AND NEW.Fk_Caja > 0 THEN
        UPDATE Cajas 
        SET Valor_Total_Caja = Valor_Total_Caja + v_total_a_sumar
        WHERE ID_Caja = NEW.Fk_Caja;
    END IF;
END$$

-- Trigger AFTER UPDATE para manejar cambios en el costo o servicio
DROP TRIGGER IF EXISTS tr_pago_servicio_update;
CREATE TRIGGER tr_pago_servicio_update 
AFTER UPDATE ON PagosServicios
FOR EACH ROW
BEGIN
    DECLARE v_comision_old DECIMAL(10, 2) DEFAULT 0.00;
    DECLARE v_comision_new DECIMAL(10, 2) DEFAULT 0.00;
    DECLARE v_total_old DECIMAL(10, 2) DEFAULT 0.00;
    DECLARE v_total_new DECIMAL(10, 2) DEFAULT 0.00;
    
    -- Solo procesar si cambió el costo o el servicio
    IF OLD.costo != NEW.costo OR OLD.Servicio != NEW.Servicio THEN
        -- Obtener comisión del servicio anterior
        SELECT IFNULL(Comision, 0.00) INTO v_comision_old
        FROM ListadoServicios
        WHERE Servicio = OLD.Servicio
        LIMIT 1;
        
        -- Obtener comisión del servicio nuevo
        SELECT IFNULL(Comision, 0.00) INTO v_comision_new
        FROM ListadoServicios
        WHERE Servicio = NEW.Servicio
        LIMIT 1;
        
        -- Calcular totales
        SET v_total_old = IFNULL(OLD.costo, 0.00) + IFNULL(v_comision_old, 0.00);
        SET v_total_new = IFNULL(NEW.costo, 0.00) + IFNULL(v_comision_new, 0.00);
        
        -- Restar el valor anterior si existe
        IF v_total_old > 0 AND OLD.Fk_Caja > 0 THEN
            UPDATE Cajas 
            SET Valor_Total_Caja = Valor_Total_Caja - v_total_old
            WHERE ID_Caja = OLD.Fk_Caja;
        END IF;
        
        -- Sumar el nuevo valor
        IF v_total_new > 0 AND NEW.Fk_Caja > 0 THEN
            UPDATE Cajas 
            SET Valor_Total_Caja = Valor_Total_Caja + v_total_new
            WHERE ID_Caja = NEW.Fk_Caja;
        END IF;
    END IF;
END$$

-- Trigger AFTER DELETE para restar el costo + comisión si se elimina un pago
DROP TRIGGER IF EXISTS tr_pago_servicio_delete;
CREATE TRIGGER tr_pago_servicio_delete 
AFTER DELETE ON PagosServicios
FOR EACH ROW
BEGIN
    DECLARE v_comision DECIMAL(10, 2) DEFAULT 0.00;
    DECLARE v_total_a_restar DECIMAL(10, 2) DEFAULT 0.00;
    
    -- Obtener la comisión del servicio
    SELECT IFNULL(Comision, 0.00) INTO v_comision
    FROM ListadoServicios
    WHERE Servicio = OLD.Servicio
    LIMIT 1;
    
    -- Calcular el total a restar: costo + comisión
    SET v_total_a_restar = IFNULL(OLD.costo, 0.00) + IFNULL(v_comision, 0.00);
    
    -- Restar del valor total de la caja si existe
    IF v_total_a_restar > 0 AND OLD.Fk_Caja > 0 THEN
        UPDATE Cajas 
        SET Valor_Total_Caja = Valor_Total_Caja - v_total_a_restar
        WHERE ID_Caja = OLD.Fk_Caja;
    END IF;
END$$

DELIMITER ;
