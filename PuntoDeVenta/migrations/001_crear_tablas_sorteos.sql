-- =====================================================
-- Migración: Sistema de Sorteos + Relación Venta-Cliente
-- Fecha: 2026-05-08
-- Descripción: Crea tablas de sorteos, participaciones
--              y agrega Fk_Cliente a Ventas_POS
-- =====================================================

-- 1. Tabla de configuración de sorteos
CREATE TABLE IF NOT EXISTS `Sorteos` (
    `ID_Sorteo` int(11) NOT NULL AUTO_INCREMENT,
    `Nombre_Sorteo` varchar(200) NOT NULL,
    `Descripcion` varchar(500) DEFAULT NULL,
    `Fecha_Inicio` date NOT NULL,
    `Fecha_Fin` date NOT NULL,
    `Activo` tinyint(1) NOT NULL DEFAULT 1,
    `Aplica_Todas_Sucursales` tinyint(1) NOT NULL DEFAULT 1,
    `Prefijo_Folio` varchar(10) DEFAULT NULL COMMENT 'Prefijo personalizado para folios de rifa de este sorteo',
    `Folio_Inicio` int(11) NOT NULL DEFAULT 1 COMMENT 'Número inicial del folio para este sorteo',
    `CreadoPor` varchar(250) NOT NULL,
    `CreadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
    `ActualizadoPor` varchar(250) DEFAULT NULL,
    `ActualizadoEl` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
    PRIMARY KEY (`ID_Sorteo`),
    KEY `idx_sorteo_activo` (`Activo`, `Fecha_Inicio`, `Fecha_Fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- 2. Tabla de sucursales asociadas a un sorteo (cuando no aplica a todas)
CREATE TABLE IF NOT EXISTS `Sorteo_Sucursales` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `Fk_Sorteo` int(11) NOT NULL,
    `Fk_Sucursal` int(11) NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `uk_sorteo_sucursal` (`Fk_Sorteo`, `Fk_Sucursal`),
    KEY `idx_sorteo` (`Fk_Sorteo`),
    KEY `idx_sucursal` (`Fk_Sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- 3. Tabla de participaciones en sorteos (relación venta ↔ cliente ↔ sorteo)
CREATE TABLE IF NOT EXISTS `Sorteo_Participaciones` (
    `ID_Participacion` int(11) NOT NULL AUTO_INCREMENT,
    `Fk_Sorteo` int(11) NOT NULL,
    `Fk_Venta_Ticket` varchar(100) NOT NULL,
    `Fk_Cliente` int(12) UNSIGNED ZEROFILL DEFAULT NULL,
    `Nombre_Cliente` varchar(150) NOT NULL,
    `Telefono_Cliente` varchar(20) DEFAULT NULL,
    `FechaNacimiento_Cliente` date DEFAULT NULL,
    `FolioRifa` varchar(50) NOT NULL,
    `Fk_Sucursal` int(11) NOT NULL,
    `Participa` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=participa, 0=decidió no participar',
    `RegistradoPor` varchar(250) NOT NULL,
    `RegistradoEl` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`ID_Participacion`),
    KEY `idx_sorteo` (`Fk_Sorteo`),
    KEY `idx_cliente` (`Fk_Cliente`),
    KEY `idx_ticket` (`Fk_Venta_Ticket`),
    KEY `idx_folio_rifa` (`FolioRifa`),
    KEY `idx_sucursal` (`Fk_Sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- 4. Agregar columna Fk_Cliente a Ventas_POS (si no existe)
-- Se mantiene el campo Cliente (texto) por compatibilidad
SET @column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'Ventas_POS' 
    AND COLUMN_NAME = 'Fk_Cliente'
);

SET @sql = IF(@column_exists = 0,
    'ALTER TABLE `Ventas_POS` ADD COLUMN `Fk_Cliente` int(12) UNSIGNED ZEROFILL DEFAULT NULL AFTER `Cliente`',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Agregar columna Fk_Sorteo a Ventas_POS (para saber bajo qué sorteo se registró)
SET @column_exists2 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'Ventas_POS' 
    AND COLUMN_NAME = 'Fk_Sorteo'
);

SET @sql2 = IF(@column_exists2 = 0,
    'ALTER TABLE `Ventas_POS` ADD COLUMN `Fk_Sorteo` int(11) DEFAULT NULL AFTER `Fk_Cliente`',
    'SELECT 1'
);

PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;
