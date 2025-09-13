-- =============================================
-- SISTEMA DE CHAT - TABLAS DE BASE DE DATOS
-- =============================================

-- Tabla para conversaciones/grupos de chat
CREATE TABLE IF NOT EXISTS `chat_conversaciones` (
  `id_conversacion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_conversacion` varchar(255) DEFAULT NULL,
  `tipo_conversacion` enum('individual','grupo','sucursal','general') NOT NULL DEFAULT 'individual',
  `sucursal_id` int(11) DEFAULT NULL,
  `creado_por` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_mensaje` text DEFAULT NULL,
  `ultimo_mensaje_fecha` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_conversacion`),
  KEY `idx_tipo_sucursal` (`tipo_conversacion`, `sucursal_id`),
  KEY `idx_creado_por` (`creado_por`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para participantes de conversaciones
CREATE TABLE IF NOT EXISTS `chat_participantes` (
  `id_participante` int(11) NOT NULL AUTO_INCREMENT,
  `conversacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_union` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultima_lectura` timestamp NULL DEFAULT NULL,
  `notificaciones` tinyint(1) NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_participante`),
  UNIQUE KEY `unique_conversacion_usuario` (`conversacion_id`, `usuario_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_conversacion` (`conversacion_id`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla principal de mensajes
CREATE TABLE IF NOT EXISTS `chat_mensajes` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `conversacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `tipo_mensaje` enum('texto','imagen','archivo','sistema') NOT NULL DEFAULT 'texto',
  `archivo_url` varchar(500) DEFAULT NULL,
  `archivo_nombre` varchar(255) DEFAULT NULL,
  `archivo_tipo` varchar(100) DEFAULT NULL,
  `archivo_tamaño` int(11) DEFAULT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_edicion` timestamp NULL DEFAULT NULL,
  `editado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `mensaje_respuesta_id` int(11) DEFAULT NULL,
  `leido_por` json DEFAULT NULL,
  PRIMARY KEY (`id_mensaje`),
  KEY `idx_conversacion` (`conversacion_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_fecha_envio` (`fecha_envio`),
  KEY `idx_tipo_mensaje` (`tipo_mensaje`),
  KEY `idx_eliminado` (`eliminado`),
  KEY `idx_mensaje_respuesta` (`mensaje_respuesta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para estados de lectura de mensajes
CREATE TABLE IF NOT EXISTS `chat_lecturas` (
  `id_lectura` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_lectura` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_lectura`),
  UNIQUE KEY `unique_mensaje_usuario` (`mensaje_id`, `usuario_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_fecha_lectura` (`fecha_lectura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para reacciones a mensajes
CREATE TABLE IF NOT EXISTS `chat_reacciones` (
  `id_reaccion` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_reaccion` varchar(10) NOT NULL DEFAULT '👍',
  `fecha_reaccion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reaccion`),
  UNIQUE KEY `unique_mensaje_usuario_reaccion` (`mensaje_id`, `usuario_id`, `tipo_reaccion`),
  KEY `idx_mensaje` (`mensaje_id`),
  KEY `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para configuraciones de chat por usuario
CREATE TABLE IF NOT EXISTS `chat_configuraciones` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `notificaciones_sonido` tinyint(1) NOT NULL DEFAULT 1,
  `notificaciones_push` tinyint(1) NOT NULL DEFAULT 1,
  `tema_oscuro` tinyint(1) NOT NULL DEFAULT 0,
  `mensajes_por_pagina` int(11) NOT NULL DEFAULT 50,
  `auto_borrar_mensajes` int(11) DEFAULT NULL COMMENT 'Días para auto-eliminar mensajes (NULL = nunca)',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `unique_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =============================================

-- Índice compuesto para búsquedas de mensajes
CREATE INDEX `idx_conversacion_fecha_eliminado` ON `chat_mensajes` (`conversacion_id`, `fecha_envio` DESC, `eliminado`);

-- Índice para conversaciones activas
CREATE INDEX `idx_conversacion_activa` ON `chat_conversaciones` (`activo`, `tipo_conversacion`, `sucursal_id`);

-- Índice para participantes activos
CREATE INDEX `idx_participante_activo` ON `chat_participantes` (`activo`, `conversacion_id`);

-- =============================================
-- TRIGGERS PARA MANTENIMIENTO AUTOMÁTICO
-- =============================================

-- Trigger para actualizar último mensaje en conversación
DELIMITER $$
CREATE TRIGGER `tr_chat_actualizar_ultimo_mensaje` 
AFTER INSERT ON `chat_mensajes`
FOR EACH ROW
BEGIN
    UPDATE chat_conversaciones 
    SET ultimo_mensaje = NEW.mensaje,
        ultimo_mensaje_fecha = NEW.fecha_envio
    WHERE id_conversacion = NEW.conversacion_id;
END$$
DELIMITER ;

-- Trigger para crear configuración por defecto al insertar participante
DELIMITER $$
CREATE TRIGGER `tr_chat_crear_configuracion` 
AFTER INSERT ON `chat_participantes`
FOR EACH ROW
BEGIN
    INSERT IGNORE INTO chat_configuraciones (usuario_id) 
    VALUES (NEW.usuario_id);
END$$
DELIMITER ;

-- =============================================
-- CONVERSACIONES INICIALES DEL SISTEMA
-- =============================================

-- Crear conversación general del sistema
INSERT INTO `chat_conversaciones` (`nombre_conversacion`, `tipo_conversacion`, `creado_por`) 
VALUES ('Chat General', 'general', 1);

-- Crear conversación de soporte técnico
INSERT INTO `chat_conversaciones` (`nombre_conversacion`, `tipo_conversacion`, `creado_por`) 
VALUES ('Soporte Técnico', 'grupo', 1);

-- =============================================
-- VISTAS ÚTILES PARA CONSULTAS
-- =============================================

-- Vista para conversaciones con información de participantes
CREATE VIEW `v_chat_conversaciones_info` AS
SELECT 
    c.id_conversacion,
    c.nombre_conversacion,
    c.tipo_conversacion,
    c.sucursal_id,
    s.Nombre_Sucursal,
    c.creado_por,
    u.Nombre_Apellidos as creado_por_nombre,
    c.fecha_creacion,
    c.ultimo_mensaje,
    c.ultimo_mensaje_fecha,
    c.activo,
    COUNT(p.id_participante) as total_participantes
FROM chat_conversaciones c
LEFT JOIN Sucursales s ON c.sucursal_id = s.ID_Sucursal
LEFT JOIN Usuarios_PV u ON c.creado_por = u.Id_PvUser
LEFT JOIN chat_participantes p ON c.id_conversacion = p.conversacion_id AND p.activo = 1
WHERE c.activo = 1
GROUP BY c.id_conversacion;

-- Vista para mensajes con información del usuario
CREATE VIEW `v_chat_mensajes_info` AS
SELECT 
    m.id_mensaje,
    m.conversacion_id,
    m.usuario_id,
    u.Nombre_Apellidos as usuario_nombre,
    u.file_name as usuario_avatar,
    m.mensaje,
    m.tipo_mensaje,
    m.archivo_url,
    m.archivo_nombre,
    m.archivo_tipo,
    m.archivo_tamaño,
    m.fecha_envio,
    m.fecha_edicion,
    m.editado,
    m.eliminado,
    m.mensaje_respuesta_id,
    m.leido_por
FROM chat_mensajes m
LEFT JOIN Usuarios_PV u ON m.usuario_id = u.Id_PvUser
WHERE m.eliminado = 0;
