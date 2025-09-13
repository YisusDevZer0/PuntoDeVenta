-- =====================================================
-- SCRIPT SQL CORREGIDO PARA SISTEMA DE RECORDATORIOS
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
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_recordatorio`),
  KEY `idx_fecha_programada` (`fecha_programada`),
  KEY `idx_estado` (`estado`),
  KEY `idx_prioridad` (`prioridad`),
  KEY `idx_usuario_creador` (`usuario_creador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`id_destinatario`),
  KEY `idx_recordatorio_id` (`recordatorio_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_estado_envio` (`estado_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Crear tabla de grupos
CREATE TABLE IF NOT EXISTS `recordatorios_grupos` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_grupo` varchar(100) NOT NULL,
  `descripcion` text,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_grupo`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Crear tabla de miembros de grupos
CREATE TABLE IF NOT EXISTS `recordatorios_grupos_miembros` (
  `id_miembro` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_agregado` timestamp DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_miembro`),
  KEY `idx_grupo_id` (`grupo_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  UNIQUE KEY `unique_grupo_usuario` (`grupo_id`, `usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Crear tabla de logs
CREATE TABLE IF NOT EXISTS `recordatorios_logs` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `recordatorio_id` int(11) NOT NULL,
  `tipo_envio` enum('whatsapp','notificacion','ambos') NOT NULL,
  `estado` enum('iniciado','completado','error') NOT NULL,
  `mensaje` text,
  `detalles_tecnico` text,
  `fecha_log` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `idx_recordatorio_id` (`recordatorio_id`),
  KEY `idx_fecha_log` (`fecha_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Crear tabla de configuración de WhatsApp
CREATE TABLE IF NOT EXISTS `recordatorios_config_whatsapp` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `api_url` varchar(500) NOT NULL,
  `api_token` varchar(255) NOT NULL,
  `numero_telefono` varchar(20) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Crear tabla de plantillas de mensajes
CREATE TABLE IF NOT EXISTS `recordatorios_plantillas` (
  `id_plantilla` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_plantilla` varchar(100) NOT NULL,
  `tipo_plantilla` enum('whatsapp','notificacion','ambos') NOT NULL,
  `contenido` text NOT NULL,
  `variables_disponibles` text,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_plantilla`),
  KEY `idx_tipo_plantilla` (`tipo_plantilla`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERTAR DATOS INICIALES
-- =====================================================

-- Insertar configuración por defecto de WhatsApp
INSERT INTO `recordatorios_config_whatsapp` (`api_url`, `api_token`, `numero_telefono`, `activo`) 
VALUES ('https://api.whatsapp.com/send', 'tu_token_aqui', '5211234567890', 1)
ON DUPLICATE KEY UPDATE `api_url` = VALUES(`api_url`);

-- Insertar plantillas por defecto
INSERT INTO `recordatorios_plantillas` (`nombre_plantilla`, `tipo_plantilla`, `contenido`, `variables_disponibles`) VALUES
('Recordatorio General', 'whatsapp', '🔔 *Recordatorio: {titulo}*\n\n{descripcion}\n\n📅 Fecha: {fecha}\n⏰ Hora: {hora}\n\n_Sistema Doctor Pez_', 'titulo,descripcion,fecha,hora'),
('Notificación Interna', 'notificacion', 'Recordatorio: {titulo}\n\n{descripcion}\n\nFecha: {fecha} {hora}', 'titulo,descripcion,fecha,hora'),
('Recordatorio Urgente', 'ambos', '🚨 *URGENTE* - {titulo}\n\n{descripcion}\n\n⏰ {fecha} {hora}\n\n_Requiere atención inmediata_', 'titulo,descripcion,fecha,hora')
ON DUPLICATE KEY UPDATE `contenido` = VALUES(`contenido`);

-- Crear grupo por defecto
INSERT INTO `recordatorios_grupos` (`nombre_grupo`, `descripcion`, `activo`) 
VALUES ('Todos los Usuarios', 'Grupo que incluye a todos los usuarios del sistema', 1)
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- =====================================================
-- CREAR VISTAS (CORREGIDAS)
-- =====================================================

-- Vista para recordatorios completos
CREATE OR REPLACE VIEW `v_recordatorios_completos` AS
SELECT 
    rs.id_recordatorio,
    rs.titulo,
    rs.descripcion,
    rs.fecha_programada,
    rs.prioridad,
    rs.estado,
    CASE 
        WHEN rs.estado = 'programado' THEN 'Programado'
        WHEN rs.estado = 'enviando' THEN 'Enviando'
        WHEN rs.estado = 'enviado' THEN 'Enviado'
        WHEN rs.estado = 'cancelado' THEN 'Cancelado'
        WHEN rs.estado = 'error' THEN 'Error'
        ELSE 'Desconocido'
    END as estado_descripcion,
    rs.tipo_envio,
    rs.destinatarios,
    rs.sucursal_id,
    s.Nombre_Sucursal as sucursal_nombre,
    rs.grupo_id,
    rg.nombre_grupo,
    rs.usuario_creador,
    u.Nombre_Apellidos as creador_nombre,
    rs.fecha_creacion,
    rs.fecha_actualizacion,
    rs.activo
FROM recordatorios_sistema rs
LEFT JOIN Sucursales s ON rs.sucursal_id = s.ID_Sucursal
LEFT JOIN recordatorios_grupos rg ON rs.grupo_id = rg.id_grupo
LEFT JOIN Usuarios_PV u ON rs.usuario_creador = u.Id_PvUser;

-- Vista para destinatarios completos (CORREGIDA)
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
    u.Correo_Electronico as usuario_email,
    u.Telefono as usuario_telefono,
    s.Nombre_Sucursal as sucursal_nombre
FROM recordatorios_destinatarios rd
LEFT JOIN Usuarios_PV u ON rd.usuario_id = u.Id_PvUser
LEFT JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal;

-- =====================================================
-- CREAR PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Procedimiento para limpiar logs antiguos
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_limpiar_logs_recordatorios`(IN dias_antiguedad INT)
BEGIN
    DELETE FROM recordatorios_logs 
    WHERE fecha_log < DATE_SUB(NOW(), INTERVAL dias_antiguedad DAY);
    
    SELECT ROW_COUNT() as registros_eliminados;
END //
DELIMITER ;

-- Procedimiento para obtener recordatorios pendientes
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_obtener_recordatorios_pendientes`()
BEGIN
    SELECT * FROM v_recordatorios_completos 
    WHERE estado = 'programado' 
    AND fecha_programada <= NOW() 
    AND activo = 1
    ORDER BY prioridad DESC, fecha_programada ASC;
END //
DELIMITER ;

-- Procedimiento para estadísticas
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_estadisticas_recordatorios`()
BEGIN
    SELECT 
        COUNT(*) as total_recordatorios,
        SUM(CASE WHEN estado = 'programado' THEN 1 ELSE 0 END) as programados,
        SUM(CASE WHEN estado = 'enviando' THEN 1 ELSE 0 END) as enviando,
        SUM(CASE WHEN estado = 'enviado' THEN 1 ELSE 0 END) as enviados,
        SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
        SUM(CASE WHEN estado = 'error' THEN 1 ELSE 0 END) as errores
    FROM recordatorios_sistema 
    WHERE activo = 1;
END //
DELIMITER ;

-- =====================================================
-- CREAR TRIGGERS
-- =====================================================

-- Trigger para actualizar fecha de modificación
DELIMITER //
CREATE TRIGGER IF NOT EXISTS `tr_recordatorios_sistema_update` 
BEFORE UPDATE ON `recordatorios_sistema`
FOR EACH ROW
BEGIN
    SET NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- Trigger para log de cambios de estado
DELIMITER //
CREATE TRIGGER IF NOT EXISTS `tr_recordatorios_log_estado` 
AFTER UPDATE ON `recordatorios_sistema`
FOR EACH ROW
BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO recordatorios_logs (recordatorio_id, tipo_envio, estado, mensaje)
        VALUES (NEW.id_recordatorio, NEW.tipo_envio, NEW.estado, 
                CONCAT('Estado cambiado de ', OLD.estado, ' a ', NEW.estado));
    END IF;
END //
DELIMITER ;

-- =====================================================
-- FINALIZAR SCRIPT
-- =====================================================

-- Mostrar mensaje de éxito
SELECT 'Sistema de Recordatorios instalado correctamente' as mensaje;
