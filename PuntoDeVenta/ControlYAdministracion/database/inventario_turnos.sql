-- =====================================================
-- MÓDULO DE INVENTARIO POR TURNOS DIARIOS
-- =====================================================
-- Este módulo permite realizar inventarios por turnos diarios
-- con bloqueo de productos seleccionados por usuario

-- Tabla principal de turnos de inventario
CREATE TABLE IF NOT EXISTS `Inventario_Turnos` (
  `ID_Turno` int(11) NOT NULL AUTO_INCREMENT,
  `Folio_Turno` varchar(50) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Usuario_Inicio` varchar(250) NOT NULL,
  `Usuario_Actual` varchar(250) NOT NULL,
  `Fecha_Turno` date NOT NULL,
  `Hora_Inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `Hora_Pausa` timestamp NULL DEFAULT NULL,
  `Hora_Reanudacion` timestamp NULL DEFAULT NULL,
  `Hora_Finalizacion` timestamp NULL DEFAULT NULL,
  `Estado` enum('activo','pausado','finalizado','cancelado') NOT NULL DEFAULT 'activo',
  `Total_Productos` int(11) NOT NULL DEFAULT 0,
  `Productos_Completados` int(11) NOT NULL DEFAULT 0,
  `Observaciones` text DEFAULT NULL,
  PRIMARY KEY (`ID_Turno`),
  UNIQUE KEY `idx_folio` (`Folio_Turno`),
  KEY `idx_sucursal_fecha` (`Fk_sucursal`, `Fecha_Turno`),
  KEY `idx_usuario` (`Usuario_Actual`),
  KEY `idx_estado` (`Estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos en inventario por turno
CREATE TABLE IF NOT EXISTS `Inventario_Turnos_Productos` (
  `ID_Registro` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Turno` int(11) NOT NULL,
  `Folio_Turno` varchar(50) NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Nombre_Producto` varchar(500) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Existencias_Sistema` int(11) NOT NULL DEFAULT 0,
  `Existencias_Fisicas` int(11) DEFAULT NULL,
  `Diferencia` int(11) DEFAULT NULL,
  `Usuario_Selecciono` varchar(250) NOT NULL,
  `Fecha_Seleccion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Fecha_Conteo` timestamp NULL DEFAULT NULL,
  `Estado` enum('seleccionado','en_conteo','completado','liberado') NOT NULL DEFAULT 'seleccionado',
  `Observaciones` text DEFAULT NULL,
  PRIMARY KEY (`ID_Registro`),
  KEY `idx_turno` (`ID_Turno`, `Folio_Turno`),
  KEY `idx_producto` (`ID_Prod_POS`, `Cod_Barra`),
  KEY `idx_usuario_estado` (`Usuario_Selecciono`, `Estado`),
  KEY `idx_sucursal` (`Fk_sucursal`),
  CONSTRAINT `fk_turno_producto` FOREIGN KEY (`ID_Turno`) REFERENCES `Inventario_Turnos` (`ID_Turno`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de historial de pausas y reanudaciones
CREATE TABLE IF NOT EXISTS `Inventario_Turnos_Historial` (
  `ID_Historial` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Turno` int(11) NOT NULL,
  `Folio_Turno` varchar(50) NOT NULL,
  `Accion` enum('inicio','pausa','reanudacion','finalizacion','cancelacion') NOT NULL,
  `Usuario` varchar(250) NOT NULL,
  `Fecha_Accion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Observaciones` text DEFAULT NULL,
  `Datos_Estado` text DEFAULT NULL COMMENT 'JSON con el estado al momento de la acción',
  PRIMARY KEY (`ID_Historial`),
  KEY `idx_turno` (`ID_Turno`, `Folio_Turno`),
  KEY `idx_fecha` (`Fecha_Accion`),
  CONSTRAINT `fk_turno_historial` FOREIGN KEY (`ID_Turno`) REFERENCES `Inventario_Turnos` (`ID_Turno`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos bloqueados (no visibles para otros usuarios)
CREATE TABLE IF NOT EXISTS `Inventario_Productos_Bloqueados` (
  `ID_Bloqueo` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Turno` int(11) NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Usuario_Bloqueo` varchar(250) NOT NULL,
  `Fecha_Bloqueo` timestamp NOT NULL DEFAULT current_timestamp(),
  `Fecha_Liberacion` timestamp NULL DEFAULT NULL,
  `Estado` enum('bloqueado','liberado') NOT NULL DEFAULT 'bloqueado',
  PRIMARY KEY (`ID_Bloqueo`),
  UNIQUE KEY `idx_producto_turno` (`ID_Turno`, `ID_Prod_POS`, `Cod_Barra`),
  KEY `idx_usuario` (`Usuario_Bloqueo`),
  KEY `idx_sucursal` (`Fk_sucursal`),
  CONSTRAINT `fk_bloqueo_turno` FOREIGN KEY (`ID_Turno`) REFERENCES `Inventario_Turnos` (`ID_Turno`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TRIGGERS PARA GESTIÓN AUTOMÁTICA
-- =====================================================
DELIMITER $$

-- Trigger: Crear bloqueo automático al seleccionar producto
DROP TRIGGER IF EXISTS `trg_bloquear_producto_inventario`$$
CREATE TRIGGER `trg_bloquear_producto_inventario`
AFTER INSERT ON `Inventario_Turnos_Productos`
FOR EACH ROW
BEGIN
    -- Insertar bloqueo del producto
    INSERT INTO Inventario_Productos_Bloqueados (
        ID_Turno,
        ID_Prod_POS,
        Cod_Barra,
        Fk_sucursal,
        Usuario_Bloqueo
    ) VALUES (
        NEW.ID_Turno,
        NEW.ID_Prod_POS,
        NEW.Cod_Barra,
        NEW.Fk_sucursal,
        NEW.Usuario_Selecciono
    )
    ON DUPLICATE KEY UPDATE
        Usuario_Bloqueo = NEW.Usuario_Selecciono,
        Fecha_Bloqueo = NOW(),
        Estado = 'bloqueado',
        Fecha_Liberacion = NULL;
END$$

-- Trigger: Liberar bloqueo al completar o liberar producto
DROP TRIGGER IF EXISTS `trg_liberar_producto_inventario`$$
CREATE TRIGGER `trg_liberar_producto_inventario`
AFTER UPDATE ON `Inventario_Turnos_Productos`
FOR EACH ROW
BEGIN
    -- Si el producto se completa o libera, quitar el bloqueo
    IF (NEW.Estado = 'completado' OR NEW.Estado = 'liberado') AND OLD.Estado != NEW.Estado THEN
        UPDATE Inventario_Productos_Bloqueados
        SET Estado = 'liberado',
            Fecha_Liberacion = NOW()
        WHERE ID_Turno = NEW.ID_Turno
          AND ID_Prod_POS = NEW.ID_Prod_POS
          AND Cod_Barra = NEW.Cod_Barra
          AND Estado = 'bloqueado';
    END IF;
END$$

-- Trigger: Registrar historial de acciones en turnos
DROP TRIGGER IF EXISTS `trg_historial_turnos`$$
CREATE TRIGGER `trg_historial_turnos`
AFTER UPDATE ON `Inventario_Turnos`
FOR EACH ROW
BEGIN
    DECLARE v_accion VARCHAR(20);
    DECLARE v_datos_estado TEXT;
    
    -- Determinar la acción según el cambio de estado
    IF NEW.Estado = 'pausado' AND OLD.Estado = 'activo' THEN
        SET v_accion = 'pausa';
        SET v_datos_estado = JSON_OBJECT(
            'productos_completados', NEW.Productos_Completados,
            'total_productos', NEW.Total_Productos,
            'hora_pausa', NEW.Hora_Pausa
        );
    ELSEIF NEW.Estado = 'activo' AND OLD.Estado = 'pausado' THEN
        SET v_accion = 'reanudacion';
        SET v_datos_estado = JSON_OBJECT(
            'productos_completados', NEW.Productos_Completados,
            'total_productos', NEW.Total_Productos,
            'hora_reanudacion', NEW.Hora_Reanudacion
        );
    ELSEIF NEW.Estado = 'finalizado' AND OLD.Estado != 'finalizado' THEN
        SET v_accion = 'finalizacion';
        SET v_datos_estado = JSON_OBJECT(
            'productos_completados', NEW.Productos_Completados,
            'total_productos', NEW.Total_Productos,
            'hora_finalizacion', NEW.Hora_Finalizacion
        );
    ELSEIF NEW.Estado = 'cancelado' AND OLD.Estado != 'cancelado' THEN
        SET v_accion = 'cancelacion';
        SET v_datos_estado = JSON_OBJECT(
            'productos_completados', NEW.Productos_Completados,
            'total_productos', NEW.Total_Productos
        );
    END IF;
    
    -- Insertar en historial si hay una acción registrable
    IF v_accion IS NOT NULL THEN
        INSERT INTO Inventario_Turnos_Historial (
            ID_Turno,
            Folio_Turno,
            Accion,
            Usuario,
            Observaciones,
            Datos_Estado
        ) VALUES (
            NEW.ID_Turno,
            NEW.Folio_Turno,
            v_accion,
            NEW.Usuario_Actual,
            NEW.Observaciones,
            v_datos_estado
        );
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- PROCEDIMIENTO: Generar folio único de turno
-- =====================================================
DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_generar_folio_turno`$$
CREATE PROCEDURE `sp_generar_folio_turno`(
    IN p_sucursal_id INT,
    OUT p_folio VARCHAR(50)
)
BEGIN
    DECLARE v_fecha VARCHAR(8);
    DECLARE v_secuencial INT;
    DECLARE v_sucursal_cod VARCHAR(10);
    
    -- Obtener código de sucursal (primeras 3 letras o ID)
    SELECT COALESCE(SUBSTRING(Nombre_Sucursal, 1, 3), LPAD(ID_Sucursal, 3, '0'))
    INTO v_sucursal_cod
    FROM Sucursales
    WHERE ID_Sucursal = p_sucursal_id
    LIMIT 1;
    
    -- Formato: INV-SUC-YYYYMMDD-XXX
    SET v_fecha = DATE_FORMAT(CURDATE(), '%Y%m%d');
    
    -- Obtener siguiente secuencial del día
    SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(Folio_Turno, '-', -1) AS UNSIGNED)), 0) + 1
    INTO v_secuencial
    FROM Inventario_Turnos
    WHERE Fk_sucursal = p_sucursal_id
      AND DATE(Fecha_Turno) = CURDATE();
    
    -- Generar folio
    SET p_folio = CONCAT('INV-', UPPER(v_sucursal_cod), '-', v_fecha, '-', LPAD(v_secuencial, 3, '0'));
END$$
DELIMITER ;
