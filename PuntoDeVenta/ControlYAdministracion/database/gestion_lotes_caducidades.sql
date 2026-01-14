-- =====================================================
-- MÓDULO DE GESTIÓN DE LOTES Y CADUCIDADES
-- =====================================================
-- Este módulo permite actualizar lotes y fechas de caducidad
-- con descuento automático mediante triggers

-- Tabla para registro de movimientos de lotes
CREATE TABLE IF NOT EXISTS `Gestion_Lotes_Movimientos` (
  `ID_Movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Lote_Anterior` varchar(100) DEFAULT NULL,
  `Lote_Nuevo` varchar(100) NOT NULL,
  `Fecha_Caducidad_Anterior` date DEFAULT NULL,
  `Fecha_Caducidad_Nueva` date NOT NULL,
  `Cantidad` int(11) NOT NULL DEFAULT 0,
  `Tipo_Movimiento` enum('actualizacion','ajuste','correccion') NOT NULL DEFAULT 'actualizacion',
  `Usuario_Modifico` varchar(250) NOT NULL,
  `Fecha_Modificacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Observaciones` text DEFAULT NULL,
  PRIMARY KEY (`ID_Movimiento`),
  KEY `idx_producto_sucursal` (`ID_Prod_POS`, `Fk_sucursal`),
  KEY `idx_cod_barra` (`Cod_Barra`),
  KEY `idx_fecha` (`Fecha_Modificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para historial de descuentos automáticos por ventas
CREATE TABLE IF NOT EXISTS `Lotes_Descuentos_Ventas` (
  `ID_Descuento` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Venta` int(11) DEFAULT NULL,
  `Folio_Ticket` varchar(100) DEFAULT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad_Descontada` int(11) NOT NULL DEFAULT 0,
  `Existencias_Antes` int(11) NOT NULL DEFAULT 0,
  `Existencias_Despues` int(11) NOT NULL DEFAULT 0,
  `Fecha_Descuento` timestamp NOT NULL DEFAULT current_timestamp(),
  `Usuario_Venta` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`ID_Descuento`),
  KEY `idx_venta` (`Folio_Ticket`),
  KEY `idx_producto_lote` (`ID_Prod_POS`, `Lote`, `Fk_sucursal`),
  KEY `idx_fecha_caducidad` (`Fecha_Caducidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TRIGGER: Descuento automático de lotes al vender
-- =====================================================
DELIMITER $$

-- Trigger que descuenta automáticamente del lote más próximo a vencer
DROP TRIGGER IF EXISTS `trg_descontar_lote_venta`$$
CREATE TRIGGER `trg_descontar_lote_venta` 
AFTER INSERT ON `Ventas_POS_Detalle` 
FOR EACH ROW 
BEGIN
    DECLARE v_cantidad_restante INT DEFAULT NEW.Cantidad;
    DECLARE v_cantidad_descontar INT;
    DECLARE v_lote_actual VARCHAR(100);
    DECLARE v_fecha_caducidad DATE;
    DECLARE v_existencias_lote INT;
    DECLARE v_existencias_antes INT;
    DECLARE v_folio_ticket VARCHAR(100);
    DECLARE v_usuario_venta VARCHAR(250);
    
    -- Obtener información de la venta
    SELECT Folio_Ticket, AgregadoPor INTO v_folio_ticket, v_usuario_venta
    FROM Ventas_POS
    WHERE ID_Venta = NEW.Fk_Venta
    LIMIT 1;
    
    -- Obtener código de barras del producto
    SELECT Cod_Barra, Fk_sucursal INTO @v_cod_barra, @v_fk_sucursal
    FROM Stock_POS
    WHERE ID_Prod_POS = NEW.ID_Prod_POS
    LIMIT 1;
    
    -- Descontar de lotes en orden de caducidad (más próximo primero)
    WHILE v_cantidad_restante > 0 DO
        -- Obtener el lote más próximo a vencer con existencias
        SELECT Lote, Fecha_Caducidad, Existencias
        INTO v_lote_actual, v_fecha_caducidad, v_existencias_lote
        FROM Historial_Lotes
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND Fk_sucursal = @v_fk_sucursal
          AND Existencias > 0
          AND Fecha_Caducidad >= CURDATE()
        ORDER BY Fecha_Caducidad ASC
        LIMIT 1;
        
        -- Si no hay lote disponible, salir del loop
        IF v_lote_actual IS NULL THEN
            LEAVE WHILE;
        END IF;
        
        -- Calcular cuánto descontar de este lote
        IF v_existencias_lote >= v_cantidad_restante THEN
            SET v_cantidad_descontar = v_cantidad_restante;
            SET v_cantidad_restante = 0;
        ELSE
            SET v_cantidad_descontar = v_existencias_lote;
            SET v_cantidad_restante = v_cantidad_restante - v_existencias_lote;
        END IF;
        
        -- Guardar existencias antes del descuento
        SET v_existencias_antes = v_existencias_lote;
        
        -- Actualizar existencias en Historial_Lotes
        UPDATE Historial_Lotes
        SET Existencias = Existencias - v_cantidad_descontar,
            Usuario_Modifico = COALESCE(v_usuario_venta, 'Sistema'),
            Fecha_Registro = NOW()
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND Fk_sucursal = @v_fk_sucursal
          AND Lote = v_lote_actual;
        
        -- Registrar el descuento en la tabla de auditoría
        INSERT INTO Lotes_Descuentos_Ventas (
            ID_Venta,
            Folio_Ticket,
            ID_Prod_POS,
            Cod_Barra,
            Fk_sucursal,
            Lote,
            Fecha_Caducidad,
            Cantidad_Descontada,
            Existencias_Antes,
            Existencias_Despues,
            Usuario_Venta
        ) VALUES (
            NEW.Fk_Venta,
            v_folio_ticket,
            NEW.ID_Prod_POS,
            @v_cod_barra,
            @v_fk_sucursal,
            v_lote_actual,
            v_fecha_caducidad,
            v_cantidad_descontar,
            v_existencias_antes,
            v_existencias_antes - v_cantidad_descontar,
            v_usuario_venta
        );
        
        -- Actualizar Stock_POS si el lote coincide
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R - v_cantidad_descontar
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND Fk_sucursal = @v_fk_sucursal
          AND Lote = v_lote_actual;
    END WHILE;
END$$

DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================
ALTER TABLE `Historial_Lotes` 
ADD INDEX IF NOT EXISTS `idx_caducidad_existencias` (`Fecha_Caducidad`, `Existencias`);
