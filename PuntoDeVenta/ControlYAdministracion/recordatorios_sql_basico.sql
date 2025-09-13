-- =====================================================
-- SCRIPT SQL BÁSICO PARA SISTEMA DE RECORDATORIOS
-- Doctor Pez - Sistema de Punto de Venta
-- =====================================================

-- 1. Crear tabla principal de recordatorios
CREATE TABLE IF NOT EXISTS `recordatorios_sistema` (
  `id_recordatorio` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `fecha_programada` datetime NOT NULL,
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `estado` enum('programado','enviando','enviado','cancelado','error') DEFAULT 'programado',
  `tipo_envio` enum('whatsapp','notificacion','ambos') DEFAULT 'ambos',
  `mensaje_whatsapp` text,
  `mensaje_notificacion` text,
  `destinatarios` enum('todos','sucursal','grupo','individual') DEFAULT 'todos',
  `sucursal_id` int(11) DEFAULT NULL,
  `grupo_id` int(11) DEFAULT NULL,
  `usuario_creador` int(11) NOT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_recordatorio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Crear tabla de destinatarios
CREATE TABLE IF NOT EXISTS `recordatorios_destinatarios` (
  `id_destinatario` int(11) NOT NULL AUTO_INCREMENT,
  `recordatorio_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `telefono_whatsapp` varchar(20) DEFAULT NULL,
  `estado_envio` enum('pendiente','enviando','enviado','error') DEFAULT 'pendiente',
  `fecha_envio` datetime DEFAULT NULL,
  `error_envio` text DEFAULT NULL,
  `tipo_envio` enum('whatsapp','notificacion','ambos') DEFAULT 'ambos',
  PRIMARY KEY (`id_destinatario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Crear tabla de grupos
CREATE TABLE IF NOT EXISTS `recordatorios_grupos` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_grupo` varchar(100) NOT NULL,
  `descripcion` text,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Crear tabla de miembros de grupos
CREATE TABLE IF NOT EXISTS `recordatorios_grupos_miembros` (
  `id_miembro` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_agregado` timestamp DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_miembro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Crear tabla de logs
CREATE TABLE IF NOT EXISTS `recordatorios_logs` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `recordatorio_id` int(11) NOT NULL,
  `tipo_envio` enum('whatsapp','notificacion','ambos') NOT NULL,
  `estado` enum('iniciado','completado','error') NOT NULL,
  `mensaje` text,
  `detalles_tecnico` text,
  `fecha_log` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSERTAR SOLO DATOS BÁSICOS
-- =====================================================

-- Crear grupo por defecto
INSERT IGNORE INTO `recordatorios_grupos` (`nombre_grupo`, `descripcion`, `activo`) 
VALUES ('Todos los Usuarios', 'Grupo que incluye a todos los usuarios del sistema', 1);

-- =====================================================
-- FINALIZAR SCRIPT
-- =====================================================

-- Mostrar mensaje de éxito
SELECT 'Sistema de Recordatorios instalado correctamente' as mensaje;
