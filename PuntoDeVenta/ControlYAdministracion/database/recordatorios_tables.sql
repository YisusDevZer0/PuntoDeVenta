-- =====================================================
-- SISTEMA DE RECORDATORIOS - DOCTOR PEZ
-- =====================================================
-- Script para crear las tablas necesarias para el sistema
-- de recordatorios con envío por WhatsApp y notificaciones

-- Tabla principal de recordatorios
CREATE TABLE IF NOT EXISTS `recordatorios_sistema` (
  `id_recordatorio` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `mensaje_whatsapp` text,
  `mensaje_notificacion` text,
  `fecha_programada` datetime NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `prioridad` enum('baja','media','alta','urgente') NOT NULL DEFAULT 'media',
  `estado` enum('programado','enviando','enviado','cancelado','error') NOT NULL DEFAULT 'programado',
  `tipo_envio` set('whatsapp','notificacion','ambos') NOT NULL DEFAULT 'ambos',
  `destinatarios` enum('todos','sucursal','grupo','individual') NOT NULL DEFAULT 'todos',
  `sucursal_id` int(11) DEFAULT NULL,
  `grupo_id` int(11) DEFAULT NULL,
  `usuario_creador` int(11) NOT NULL,
  `usuario_modificador` int(11) DEFAULT NULL,
  `intentos_envio` int(11) NOT NULL DEFAULT 0,
  `max_intentos` int(11) NOT NULL DEFAULT 3,
  `fecha_ultimo_intento` timestamp NULL DEFAULT NULL,
  `error_ultimo_intento` text,
  `configuracion_adicional` json DEFAULT NULL,
  PRIMARY KEY (`id_recordatorio`),
  KEY `idx_fecha_programada` (`fecha_programada`),
  KEY `idx_estado` (`estado`),
  KEY `idx_prioridad` (`prioridad`),
  KEY `idx_destinatarios` (`destinatarios`),
  KEY `idx_sucursal` (`sucursal_id`),
  KEY `idx_usuario_creador` (`usuario_creador`),
  CONSTRAINT `fk_recordatorios_usuario_creador` FOREIGN KEY (`usuario_creador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recordatorios_usuario_modificador` FOREIGN KEY (`usuario_modificador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla principal de recordatorios del sistema';

-- Tabla de destinatarios específicos para recordatorios
CREATE TABLE IF NOT EXISTS `recordatorios_destinatarios` (
  `id_destinatario` int(11) NOT NULL AUTO_INCREMENT,
  `recordatorio_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `telefono_whatsapp` varchar(20) DEFAULT NULL,
  `estado_envio` enum('pendiente','enviado','error','cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha_envio` timestamp NULL DEFAULT NULL,
  `error_envio` text,
  `tipo_envio` enum('whatsapp','notificacion','ambos') NOT NULL DEFAULT 'ambos',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_destinatario`),
  KEY `idx_recordatorio` (`recordatorio_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_estado_envio` (`estado_envio`),
  CONSTRAINT `fk_recordatorios_destinatarios_recordatorio` FOREIGN KEY (`recordatorio_id`) REFERENCES `recordatorios_sistema` (`id_recordatorio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recordatorios_destinatarios_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Destinatarios específicos para recordatorios';

-- Tabla de grupos de destinatarios
CREATE TABLE IF NOT EXISTS `recordatorios_grupos` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_grupo` varchar(100) NOT NULL,
  `descripcion` text,
  `sucursal_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usuario_creador` int(11) NOT NULL,
  PRIMARY KEY (`id_grupo`),
  KEY `idx_sucursal` (`sucursal_id`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `fk_recordatorios_grupos_usuario_creador` FOREIGN KEY (`usuario_creador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Grupos de destinatarios para recordatorios';

-- Tabla de miembros de grupos
CREATE TABLE IF NOT EXISTS `recordatorios_grupos_miembros` (
  `id_miembro` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_miembro`),
  UNIQUE KEY `unique_grupo_usuario` (`grupo_id`,`usuario_id`),
  KEY `idx_grupo` (`grupo_id`),
  KEY `idx_usuario` (`usuario_id`),
  CONSTRAINT `fk_recordatorios_grupos_miembros_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `recordatorios_grupos` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recordatorios_grupos_miembros_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Miembros de grupos de recordatorios';

-- Tabla de logs de envío
CREATE TABLE IF NOT EXISTS `recordatorios_logs` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `recordatorio_id` int(11) NOT NULL,
  `destinatario_id` int(11) DEFAULT NULL,
  `tipo_envio` enum('whatsapp','notificacion','ambos') NOT NULL,
  `estado` enum('iniciado','exitoso','error','cancelado') NOT NULL,
  `mensaje` text,
  `detalles_tecnico` json DEFAULT NULL,
  `fecha_log` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `idx_recordatorio` (`recordatorio_id`),
  KEY `idx_destinatario` (`destinatario_id`),
  KEY `idx_fecha` (`fecha_log`),
  CONSTRAINT `fk_recordatorios_logs_recordatorio` FOREIGN KEY (`recordatorio_id`) REFERENCES `recordatorios_sistema` (`id_recordatorio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recordatorios_logs_destinatario` FOREIGN KEY (`destinatario_id`) REFERENCES `recordatorios_destinatarios` (`id_destinatario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs de envío de recordatorios';

-- Tabla de configuración de WhatsApp
CREATE TABLE IF NOT EXISTS `recordatorios_config_whatsapp` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `api_url` varchar(500) NOT NULL,
  `api_token` text NOT NULL,
  `numero_telefono` varchar(20) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usuario_configurador` int(11) NOT NULL,
  PRIMARY KEY (`id_config`),
  CONSTRAINT `fk_recordatorios_config_whatsapp_usuario` FOREIGN KEY (`usuario_configurador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración de WhatsApp para recordatorios';

-- Tabla de plantillas de mensajes
CREATE TABLE IF NOT EXISTS `recordatorios_plantillas` (
  `id_plantilla` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('whatsapp','notificacion','ambos') NOT NULL DEFAULT 'ambos',
  `plantilla_whatsapp` text,
  `plantilla_notificacion` text,
  `variables_disponibles` json DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usuario_creador` int(11) NOT NULL,
  PRIMARY KEY (`id_plantilla`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `fk_recordatorios_plantillas_usuario_creador` FOREIGN KEY (`usuario_creador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Plantillas de mensajes para recordatorios';

-- Insertar configuración por defecto de WhatsApp
INSERT INTO `recordatorios_config_whatsapp` (`api_url`, `api_token`, `numero_telefono`, `usuario_configurador`) 
VALUES ('https://api.whatsapp.com/send', 'tu_token_aqui', '1234567890', 1)
ON DUPLICATE KEY UPDATE `api_url` = VALUES(`api_url`);

-- Insertar plantillas por defecto
INSERT INTO `recordatorios_plantillas` (`nombre`, `tipo`, `plantilla_whatsapp`, `plantilla_notificacion`, `variables_disponibles`, `usuario_creador`) VALUES
('Recordatorio General', 'ambos', 
'🔔 *Recordatorio del Sistema*\n\n*Título:* {titulo}\n*Descripción:* {descripcion}\n*Fecha:* {fecha}\n*Prioridad:* {prioridad}\n\n_Sistema Doctor Pez_', 
'🔔 Recordatorio: {titulo}\n\n{descripcion}\n\nFecha: {fecha} | Prioridad: {prioridad}', 
'["titulo", "descripcion", "fecha", "prioridad"]', 1),
('Recordatorio Urgente', 'ambos', 
'🚨 *URGENTE - Recordatorio del Sistema*\n\n*Título:* {titulo}\n*Descripción:* {descripcion}\n*Fecha:* {fecha}\n\n⚠️ *ATENCIÓN INMEDIATA REQUERIDA* ⚠️\n\n_Sistema Doctor Pez_', 
'🚨 URGENTE: {titulo}\n\n{descripcion}\n\n⚠️ ATENCIÓN INMEDIATA REQUERIDA', 
'["titulo", "descripcion", "fecha"]', 1),
('Recordatorio de Reunión', 'ambos', 
'📅 *Recordatorio de Reunión*\n\n*Título:* {titulo}\n*Hora:* {hora}\n*Lugar:* {lugar}\n*Descripción:* {descripcion}\n\n_Sistema Doctor Pez_', 
'📅 Reunión: {titulo}\n\nHora: {hora}\nLugar: {lugar}\n\n{descripcion}', 
'["titulo", "hora", "lugar", "descripcion"]', 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- Crear vista para recordatorios con información completa
CREATE OR REPLACE VIEW `v_recordatorios_completos` AS
SELECT 
    r.id_recordatorio,
    r.titulo,
    r.descripcion,
    r.mensaje_whatsapp,
    r.mensaje_notificacion,
    r.fecha_programada,
    r.fecha_creacion,
    r.prioridad,
    r.estado,
    r.tipo_envio,
    r.destinatarios,
    r.sucursal_id,
    r.grupo_id,
    r.intentos_envio,
    r.max_intentos,
    r.fecha_ultimo_intento,
    r.error_ultimo_intento,
    u_creador.Nombre_Apellidos as creador_nombre,
    u_modificador.Nombre_Apellidos as modificador_nombre,
    s.Nombre_Sucursal as sucursal_nombre,
    g.nombre_grupo as grupo_nombre,
    CASE 
        WHEN r.estado = 'programado' AND r.fecha_programada > NOW() THEN 'Pendiente'
        WHEN r.estado = 'programado' AND r.fecha_programada <= NOW() THEN 'Listo para enviar'
        WHEN r.estado = 'enviando' THEN 'Enviando'
        WHEN r.estado = 'enviado' THEN 'Enviado'
        WHEN r.estado = 'cancelado' THEN 'Cancelado'
        WHEN r.estado = 'error' THEN 'Error'
        ELSE 'Desconocido'
    END as estado_descripcion
FROM recordatorios_sistema r
LEFT JOIN Usuarios_PV u_creador ON r.usuario_creador = u_creador.Id_PvUser
LEFT JOIN Usuarios_PV u_modificador ON r.usuario_modificador = u_modificador.Id_PvUser
LEFT JOIN Sucursales s ON r.sucursal_id = s.ID_Sucursal
LEFT JOIN recordatorios_grupos g ON r.grupo_id = g.id_grupo;

-- Crear vista para destinatarios con información completa
CREATE OR REPLACE VIEW `v_recordatorios_destinatarios_completos` AS
SELECT 
    rd.id_destinatario,
    rd.recordatorio_id,
    rd.usuario_id,
    rd.telefono_whatsapp,
    rd.estado_envio,
    rd.fecha_envio,
    rd.error_envio,
    rd.tipo_envio,
    u.Nombre_Apellidos as usuario_nombre,
    u.Email as usuario_email,
    u.Telefono as usuario_telefono,
    s.Nombre_Sucursal as sucursal_nombre
FROM recordatorios_destinatarios rd
LEFT JOIN Usuarios_PV u ON rd.usuario_id = u.Id_PvUser
LEFT JOIN Sucursales s ON u.ID_Sucursal = s.ID_Sucursal;

-- Crear índices adicionales para optimización
CREATE INDEX `idx_recordatorios_fecha_estado` ON `recordatorios_sistema` (`fecha_programada`, `estado`);
CREATE INDEX `idx_recordatorios_prioridad_estado` ON `recordatorios_sistema` (`prioridad`, `estado`);
CREATE INDEX `idx_destinatarios_estado_envio` ON `recordatorios_destinatarios` (`estado_envio`, `fecha_envio`);

-- Crear trigger para actualizar fecha de modificación
DELIMITER $$
CREATE TRIGGER `tr_recordatorios_sistema_update` 
BEFORE UPDATE ON `recordatorios_sistema`
FOR EACH ROW
BEGIN
    SET NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- Crear trigger para log automático de cambios de estado
DELIMITER $$
CREATE TRIGGER `tr_recordatorios_log_estado` 
AFTER UPDATE ON `recordatorios_sistema`
FOR EACH ROW
BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO recordatorios_logs (recordatorio_id, tipo_envio, estado, mensaje, detalles_tecnico)
        VALUES (NEW.id_recordatorio, 'ambos', NEW.estado, 
                CONCAT('Estado cambiado de ', OLD.estado, ' a ', NEW.estado),
                JSON_OBJECT('estado_anterior', OLD.estado, 'estado_nuevo', NEW.estado, 'fecha_cambio', NOW()));
    END IF;
END$$
DELIMITER ;

-- Crear procedimiento para limpiar logs antiguos (más de 30 días)
DELIMITER $$
CREATE PROCEDURE `sp_limpiar_logs_recordatorios`()
BEGIN
    DELETE FROM recordatorios_logs 
    WHERE fecha_log < DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    SELECT ROW_COUNT() as registros_eliminados;
END$$
DELIMITER ;

-- Crear procedimiento para obtener recordatorios pendientes
DELIMITER $$
CREATE PROCEDURE `sp_obtener_recordatorios_pendientes`()
BEGIN
    SELECT * FROM v_recordatorios_completos 
    WHERE estado = 'programado' 
    AND fecha_programada <= NOW()
    ORDER BY prioridad DESC, fecha_programada ASC;
END$$
DELIMITER ;

-- Crear procedimiento para obtener estadísticas de recordatorios
DELIMITER $$
CREATE PROCEDURE `sp_estadisticas_recordatorios`(IN fecha_inicio DATE, IN fecha_fin DATE)
BEGIN
    SELECT 
        COUNT(*) as total_recordatorios,
        SUM(CASE WHEN estado = 'enviado' THEN 1 ELSE 0 END) as enviados,
        SUM(CASE WHEN estado = 'error' THEN 1 ELSE 0 END) as errores,
        SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
        SUM(CASE WHEN estado = 'programado' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN prioridad = 'urgente' THEN 1 ELSE 0 END) as urgentes,
        SUM(CASE WHEN prioridad = 'alta' THEN 1 ELSE 0 END) as alta_prioridad,
        SUM(CASE WHEN prioridad = 'media' THEN 1 ELSE 0 END) as media_prioridad,
        SUM(CASE WHEN prioridad = 'baja' THEN 1 ELSE 0 END) as baja_prioridad
    FROM recordatorios_sistema 
    WHERE DATE(fecha_creacion) BETWEEN fecha_inicio AND fecha_fin;
END$$
DELIMITER ;

-- Comentarios finales
-- =====================================================
-- Este script crea un sistema completo de recordatorios que incluye:
-- 1. Gestión de recordatorios con múltiples tipos de envío
-- 2. Sistema de destinatarios (todos, sucursal, grupo, individual)
-- 3. Integración con WhatsApp y notificaciones internas
-- 4. Sistema de logs y auditoría
-- 5. Plantillas de mensajes personalizables
-- 6. Vistas optimizadas para consultas frecuentes
-- 7. Triggers automáticos para mantenimiento
-- 8. Procedimientos almacenados para operaciones comunes
-- =====================================================
