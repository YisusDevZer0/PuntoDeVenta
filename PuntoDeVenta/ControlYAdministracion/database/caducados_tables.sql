-- =====================================================
-- MÓDULO DE CONTROL DE CADUCADOS - DOCTOR PEZ
-- Sistema de gestión de lotes y fechas de caducidad
-- =====================================================

-- Tabla principal para registro de lotes y caducidad
CREATE TABLE IF NOT EXISTS `productos_lotes_caducidad` (
  `id_lote` int(11) NOT NULL AUTO_INCREMENT,
  `folio_stock` int(10) UNSIGNED ZEROFILL NOT NULL,
  `cod_barra` varchar(100) NOT NULL,
  `nombre_producto` varchar(250) NOT NULL,
  `lote` varchar(100) NOT NULL,
  `fecha_caducidad` date NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `cantidad_inicial` int(11) NOT NULL,
  `cantidad_actual` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `proveedor` varchar(255) DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL,
  `estado` enum('activo','agotado','vencido','retirado') DEFAULT 'activo',
  `usuario_registro` int(11) NOT NULL,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `observaciones` text,
  PRIMARY KEY (`id_lote`),
  KEY `idx_cod_barra` (`cod_barra`),
  KEY `idx_lote` (`lote`),
  KEY `idx_fecha_caducidad` (`fecha_caducidad`),
  KEY `idx_sucursal` (`sucursal_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_folio_stock` (`folio_stock`),
  CONSTRAINT `fk_lotes_stock` FOREIGN KEY (`folio_stock`) REFERENCES `Stock_POS` (`Folio_Prod_Stock`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para configuración de alertas por sucursal
CREATE TABLE IF NOT EXISTS `caducados_configuracion` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `sucursal_id` int(11) NOT NULL,
  `dias_alerta_3_meses` int(11) DEFAULT 90,
  `dias_alerta_6_meses` int(11) DEFAULT 180,
  `dias_alerta_9_meses` int(11) DEFAULT 270,
  `notificaciones_activas` tinyint(1) DEFAULT 1,
  `email_responsable` varchar(255) DEFAULT NULL,
  `telefono_whatsapp` varchar(20) DEFAULT NULL,
  `usuario_responsable` int(11) DEFAULT NULL,
  `fecha_configuracion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `unique_sucursal` (`sucursal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para historial de movimientos
CREATE TABLE IF NOT EXISTS `caducados_historial` (
  `id_historial` int(11) NOT NULL AUTO_INCREMENT,
  `id_lote` int(11) NOT NULL,
  `tipo_movimiento` enum('registro','actualizacion','transferencia','venta','ajuste','vencimiento') NOT NULL,
  `cantidad_anterior` int(11) DEFAULT NULL,
  `cantidad_nueva` int(11) DEFAULT NULL,
  `fecha_caducidad_anterior` date DEFAULT NULL,
  `fecha_caducidad_nueva` date DEFAULT NULL,
  `sucursal_origen` int(11) DEFAULT NULL,
  `sucursal_destino` int(11) DEFAULT NULL,
  `usuario_movimiento` int(11) NOT NULL,
  `fecha_movimiento` timestamp DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text,
  PRIMARY KEY (`id_historial`),
  KEY `idx_id_lote` (`id_lote`),
  KEY `idx_tipo_movimiento` (`tipo_movimiento`),
  KEY `idx_fecha_movimiento` (`fecha_movimiento`),
  CONSTRAINT `fk_historial_lote` FOREIGN KEY (`id_lote`) REFERENCES `productos_lotes_caducidad` (`id_lote`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para notificaciones de caducidad
CREATE TABLE IF NOT EXISTS `caducados_notificaciones` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_lote` int(11) NOT NULL,
  `tipo_alerta` enum('3_meses','6_meses','9_meses','vencido') NOT NULL,
  `fecha_programada` date NOT NULL,
  `estado` enum('pendiente','enviada','leida','cancelada') DEFAULT 'pendiente',
  `mensaje` text NOT NULL,
  `destinatario` varchar(255) NOT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notificacion`),
  KEY `idx_fecha_programada` (`fecha_programada`),
  KEY `idx_estado` (`estado`),
  KEY `idx_tipo_alerta` (`tipo_alerta`),
  CONSTRAINT `fk_notificaciones_lote` FOREIGN KEY (`id_lote`) REFERENCES `productos_lotes_caducidad` (`id_lote`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración por defecto para todas las sucursales
INSERT INTO `caducados_configuracion` (`sucursal_id`, `dias_alerta_3_meses`, `dias_alerta_6_meses`, `dias_alerta_9_meses`, `notificaciones_activas`)
SELECT DISTINCT `ID_Sucursal`, 90, 180, 270, 1 
FROM `Sucursales` 
WHERE `ID_Sucursal` NOT IN (SELECT `sucursal_id` FROM `caducados_configuracion`);

-- Crear índices adicionales para optimizar consultas
CREATE INDEX `idx_productos_caducidad_fecha` ON `productos_lotes_caducidad` (`fecha_caducidad`, `estado`);
CREATE INDEX `idx_productos_caducidad_sucursal_fecha` ON `productos_lotes_caducidad` (`sucursal_id`, `fecha_caducidad`);
CREATE INDEX `idx_notificaciones_fecha_estado` ON `caducados_notificaciones` (`fecha_programada`, `estado`);
