-- Migración: comisión vs costo en caja (PagosServicios) + auditoría de gastos (GastosPOS_Audita)
-- Ejecutar en la base de datos en uso (no reemplaza importar DoctorPezMantenimiento.sql completo).
--
-- RECONCILIACIÓN (operación / negocio): tras cambiar los triggers de PagosServicios, el
-- Valor_Total_Caja histórico puede estar inflado (se sumaba también el costo). Las cajas
-- abiertas o reportes pasados pueden requerir ajuste manual o script según arqueo físico.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- ---------------------------------------------------------------------------
-- Tabla de evidencia de gastos (si ya existe desde un dump reciente, omitir error)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `GastosPOS_Audita` (
  `ID_Audita_Gastos` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Gastos` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Concepto_Categoria` varchar(200) NOT NULL,
  `Importe_Total` decimal(50,2) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Fk_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Recibe` varchar(250) NOT NULL,
  `Empleado` varchar(250) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `FechaConcepto` date NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `Licencia` varchar(200) NOT NULL,
  `Valor_Total_Caja_antes` decimal(50,2) NOT NULL,
  `Valor_Total_Caja_despues` decimal(50,2) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_Audita_Gastos`),
  KEY `idx_gastos_audita_caja` (`Fk_Caja`),
  KEY `idx_gastos_audita_fecha` (`AgregadoEl`),
  KEY `idx_gastos_audita_id_gastos` (`ID_Gastos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$

DROP TRIGGER IF EXISTS `after_insert_GastosPOS`$$
CREATE TRIGGER `after_insert_GastosPOS` AFTER INSERT ON `GastosPOS` FOR EACH ROW BEGIN
    DECLARE v_antes DECIMAL(50, 2);
    DECLARE v_despues DECIMAL(50, 2);

    SELECT IFNULL(Valor_Total_Caja, 0) INTO v_antes
    FROM Cajas
    WHERE ID_Caja = NEW.Fk_Caja;

    UPDATE Cajas
    SET Valor_Total_Caja = Valor_Total_Caja - NEW.Importe_Total
    WHERE ID_Caja = NEW.Fk_Caja;

    SELECT IFNULL(Valor_Total_Caja, 0) INTO v_despues
    FROM Cajas
    WHERE ID_Caja = NEW.Fk_Caja;

    INSERT INTO GastosPOS_Audita (
        ID_Gastos,
        Concepto_Categoria,
        Importe_Total,
        Fk_sucursal,
        Fk_Caja,
        Recibe,
        Empleado,
        AgregadoPor,
        FechaConcepto,
        Sistema,
        Licencia,
        Valor_Total_Caja_antes,
        Valor_Total_Caja_despues
    ) VALUES (
        NEW.ID_Gastos,
        NEW.Concepto_Categoria,
        NEW.Importe_Total,
        NEW.Fk_sucursal,
        NEW.Fk_Caja,
        NEW.Recibe,
        NEW.Empleado,
        NEW.AgregadoPor,
        NEW.FechaConcepto,
        NEW.Sistema,
        NEW.Licencia,
        v_antes,
        v_despues
    );
END$$

DELIMITER ;

-- ---------------------------------------------------------------------------
-- PagosServicios: solo la comisión (ListadoServicios) afecta Cajas.Valor_Total_Caja
-- ---------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `tr_pago_servicio_delete`;
DROP TRIGGER IF EXISTS `tr_pago_servicio_insert`;
DROP TRIGGER IF EXISTS `tr_pago_servicio_update`;

DELIMITER $$
CREATE TRIGGER `tr_pago_servicio_delete` AFTER DELETE ON `PagosServicios` FOR EACH ROW BEGIN
            DECLARE v_comision DECIMAL(10, 2) DEFAULT 0.00;

            SELECT IFNULL(Comision, 0.00) INTO v_comision
            FROM ListadoServicios
            WHERE Servicio = OLD.Servicio
            LIMIT 1;

            IF IFNULL(v_comision, 0.00) > 0 AND OLD.Fk_Caja > 0 THEN
                UPDATE Cajas
                SET Valor_Total_Caja = Valor_Total_Caja - v_comision
                WHERE ID_Caja = OLD.Fk_Caja;
            END IF;
        END$$

CREATE TRIGGER `tr_pago_servicio_insert` AFTER INSERT ON `PagosServicios` FOR EACH ROW BEGIN
            DECLARE v_comision DECIMAL(10, 2) DEFAULT 0.00;

            SELECT IFNULL(Comision, 0.00) INTO v_comision
            FROM ListadoServicios
            WHERE Servicio = NEW.Servicio
            LIMIT 1;

            IF IFNULL(v_comision, 0.00) > 0 AND NEW.Fk_Caja > 0 THEN
                UPDATE Cajas
                SET Valor_Total_Caja = Valor_Total_Caja + v_comision
                WHERE ID_Caja = NEW.Fk_Caja;
            END IF;
        END$$

CREATE TRIGGER `tr_pago_servicio_update` AFTER UPDATE ON `PagosServicios` FOR EACH ROW BEGIN
            DECLARE v_comision_old DECIMAL(10, 2) DEFAULT 0.00;
            DECLARE v_comision_new DECIMAL(10, 2) DEFAULT 0.00;

            IF OLD.costo != NEW.costo OR OLD.Servicio != NEW.Servicio OR OLD.Fk_Caja != NEW.Fk_Caja THEN
                SELECT IFNULL(Comision, 0.00) INTO v_comision_old
                FROM ListadoServicios
                WHERE Servicio = OLD.Servicio
                LIMIT 1;

                SELECT IFNULL(Comision, 0.00) INTO v_comision_new
                FROM ListadoServicios
                WHERE Servicio = NEW.Servicio
                LIMIT 1;

                IF IFNULL(v_comision_old, 0.00) > 0 AND OLD.Fk_Caja > 0 THEN
                    UPDATE Cajas
                    SET Valor_Total_Caja = Valor_Total_Caja - v_comision_old
                    WHERE ID_Caja = OLD.Fk_Caja;
                END IF;

                IF IFNULL(v_comision_new, 0.00) > 0 AND NEW.Fk_Caja > 0 THEN
                    UPDATE Cajas
                    SET Valor_Total_Caja = Valor_Total_Caja + v_comision_new
                    WHERE ID_Caja = NEW.Fk_Caja;
                END IF;
            END IF;
        END$$

DELIMITER ;
