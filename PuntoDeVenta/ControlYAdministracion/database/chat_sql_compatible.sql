-- =============================================
-- SISTEMA DE CHAT - SCRIPT SQL COMPATIBLE
-- Versión: 2.0 - Compatible con DoctorPezActualizado.sql
-- =============================================

-- Configuración inicial
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- =============================================
-- ELIMINAR TABLAS EXISTENTES (SI EXISTEN)
-- =============================================

DROP TABLE IF EXISTS `chat_mensajes_eliminados`;
DROP TABLE IF EXISTS `chat_estados_usuario`;
DROP TABLE IF EXISTS `chat_reacciones`;
DROP TABLE IF EXISTS `chat_lecturas`;
DROP TABLE IF EXISTS `chat_mensajes`;
DROP TABLE IF EXISTS `chat_participantes`;
DROP TABLE IF EXISTS `chat_configuraciones`;
DROP TABLE IF EXISTS `chat_conversaciones`;

-- =============================================
-- CREAR TABLAS PRINCIPALES
-- =============================================

-- Tabla para conversaciones/grupos de chat
CREATE TABLE `chat_conversaciones` (
  `id_conversacion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_conversacion` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_conversacion` enum('individual','grupo','sucursal','general','canal') NOT NULL DEFAULT 'individual',
  `sucursal_id` int(11) DEFAULT NULL,
  `creado_por` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ultimo_mensaje` text DEFAULT NULL,
  `ultimo_mensaje_fecha` timestamp NULL DEFAULT NULL,
  `ultimo_mensaje_usuario_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `privado` tinyint(1) NOT NULL DEFAULT 0,
  `archivado` tinyint(1) NOT NULL DEFAULT 0,
  `configuracion` json DEFAULT NULL,
  PRIMARY KEY (`id_conversacion`),
  KEY `idx_tipo_sucursal` (`tipo_conversacion`, `sucursal_id`),
  KEY `idx_creado_por` (`creado_por`),
  KEY `idx_activo_archivado` (`activo`, `archivado`),
  KEY `idx_ultimo_mensaje_fecha` (`ultimo_mensaje_fecha`),
  KEY `idx_privado` (`privado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Conversaciones y grupos de chat';

-- Tabla para participantes de conversaciones
CREATE TABLE `chat_participantes` (
  `id_participante` int(11) NOT NULL AUTO_INCREMENT,
  `conversacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `rol` enum('admin','moderador','miembro') NOT NULL DEFAULT 'miembro',
  `fecha_union` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_salida` timestamp NULL DEFAULT NULL,
  `ultima_lectura` timestamp NULL DEFAULT NULL,
  `notificaciones` tinyint(1) NOT NULL DEFAULT 1,
  `silenciado` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `configuracion_participante` json DEFAULT NULL,
  PRIMARY KEY (`id_participante`),
  UNIQUE KEY `unique_conversacion_usuario_activo` (`conversacion_id`, `usuario_id`, `activo`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_conversacion` (`conversacion_id`),
  KEY `idx_activo` (`activo`),
  KEY `idx_rol` (`rol`),
  KEY `idx_ultima_lectura` (`ultima_lectura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Participantes de las conversaciones';

-- Tabla principal de mensajes
CREATE TABLE `chat_mensajes` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `conversacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `tipo_mensaje` enum('texto','imagen','video','audio','archivo','sistema','sticker','encuesta') NOT NULL DEFAULT 'texto',
  `archivo_url` varchar(500) DEFAULT NULL,
  `archivo_nombre` varchar(255) DEFAULT NULL,
  `archivo_tipo` varchar(100) DEFAULT NULL,
  `archivo_tamaño` bigint(20) DEFAULT NULL,
  `archivo_hash` varchar(64) DEFAULT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_edicion` timestamp NULL DEFAULT NULL,
  `fecha_eliminacion` timestamp NULL DEFAULT NULL,
  `editado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado_por` int(11) DEFAULT NULL,
  `mensaje_respuesta_id` int(11) DEFAULT NULL,
  `mensaje_original_id` int(11) DEFAULT NULL COMMENT 'Para mensajes reenviados',
  `metadatos` json DEFAULT NULL,
  `prioridad` enum('baja','normal','alta','urgente') NOT NULL DEFAULT 'normal',
  `destinatarios_especificos` json DEFAULT NULL COMMENT 'Para mensajes privados en grupos',
  PRIMARY KEY (`id_mensaje`),
  KEY `idx_conversacion` (`conversacion_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_fecha_envio` (`fecha_envio`),
  KEY `idx_tipo_mensaje` (`tipo_mensaje`),
  KEY `idx_eliminado` (`eliminado`),
  KEY `idx_mensaje_respuesta` (`mensaje_respuesta_id`),
  KEY `idx_prioridad` (`prioridad`),
  KEY `idx_archivo_hash` (`archivo_hash`),
  KEY `idx_conversacion_fecha_eliminado` (`conversacion_id`, `fecha_envio` DESC, `eliminado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Mensajes del chat';

-- Tabla para estados de lectura de mensajes
CREATE TABLE `chat_lecturas` (
  `id_lectura` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_lectura` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dispositivo` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_lectura`),
  UNIQUE KEY `unique_mensaje_usuario` (`mensaje_id`, `usuario_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_fecha_lectura` (`fecha_lectura`),
  KEY `idx_mensaje` (`mensaje_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estados de lectura de mensajes';

-- Tabla para reacciones a mensajes
CREATE TABLE `chat_reacciones` (
  `id_reaccion` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_reaccion` varchar(20) NOT NULL DEFAULT '👍',
  `fecha_reaccion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dispositivo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_reaccion`),
  UNIQUE KEY `unique_mensaje_usuario_reaccion` (`mensaje_id`, `usuario_id`, `tipo_reaccion`),
  KEY `idx_mensaje` (`mensaje_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_tipo_reaccion` (`tipo_reaccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Reacciones a mensajes';

-- Tabla para configuraciones de chat por usuario
CREATE TABLE `chat_configuraciones` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `notificaciones_sonido` tinyint(1) NOT NULL DEFAULT 1,
  `notificaciones_push` tinyint(1) NOT NULL DEFAULT 1,
  `notificaciones_email` tinyint(1) NOT NULL DEFAULT 0,
  `tema_oscuro` tinyint(1) NOT NULL DEFAULT 0,
  `mensajes_por_pagina` int(11) NOT NULL DEFAULT 50,
  `auto_borrar_mensajes` int(11) DEFAULT NULL COMMENT 'Días para auto-eliminar mensajes (NULL = nunca)',
  `idioma` varchar(5) NOT NULL DEFAULT 'es',
  `zona_horaria` varchar(50) NOT NULL DEFAULT 'America/Monterrey',
  `mostrar_online` tinyint(1) NOT NULL DEFAULT 1,
  `mostrar_ultima_vez` tinyint(1) NOT NULL DEFAULT 1,
  `auto_descargar_archivos` tinyint(1) NOT NULL DEFAULT 0,
  `tamaño_maximo_archivo` int(11) NOT NULL DEFAULT 10485760 COMMENT 'En bytes (10MB por defecto)',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `configuracion_avanzada` json DEFAULT NULL,
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `unique_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuraciones de chat por usuario';

-- Tabla para estados de usuario (online/offline)
CREATE TABLE `chat_estados_usuario` (
  `id_estado` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('online','offline','ausente','ocupado','invisible') NOT NULL DEFAULT 'offline',
  `ultima_actividad` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dispositivo` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_estado`),
  UNIQUE KEY `unique_usuario` (`usuario_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_ultima_actividad` (`ultima_actividad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estados de conexión de usuarios';

-- Tabla para mensajes eliminados (auditoría)
CREATE TABLE `chat_mensajes_eliminados` (
  `id_eliminacion` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje_id` int(11) NOT NULL,
  `usuario_elimino` int(11) NOT NULL,
  `fecha_eliminacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `motivo` varchar(255) DEFAULT NULL,
  `tipo_eliminacion` enum('usuario','admin','sistema','automatica') NOT NULL DEFAULT 'usuario',
  `contenido_original` text DEFAULT NULL,
  PRIMARY KEY (`id_eliminacion`),
  KEY `idx_mensaje_id` (`mensaje_id`),
  KEY `idx_usuario_elimino` (`usuario_elimino`),
  KEY `idx_fecha_eliminacion` (`fecha_eliminacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Auditoría de mensajes eliminados';

-- =============================================
-- AGREGAR CLAVES FORÁNEAS
-- =============================================

ALTER TABLE `chat_conversaciones`
  ADD CONSTRAINT `fk_chat_conversaciones_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `Sucursales` (`ID_Sucursal`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_conversaciones_usuario` FOREIGN KEY (`creado_por`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chat_participantes`
  ADD CONSTRAINT `fk_chat_participantes_conversacion` FOREIGN KEY (`conversacion_id`) REFERENCES `chat_conversaciones` (`id_conversacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_participantes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chat_mensajes`
  ADD CONSTRAINT `fk_chat_mensajes_conversacion` FOREIGN KEY (`conversacion_id`) REFERENCES `chat_conversaciones` (`id_conversacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_mensajes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_mensajes_respuesta` FOREIGN KEY (`mensaje_respuesta_id`) REFERENCES `chat_mensajes` (`id_mensaje`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_mensajes_eliminado_por` FOREIGN KEY (`eliminado_por`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `chat_lecturas`
  ADD CONSTRAINT `fk_chat_lecturas_mensaje` FOREIGN KEY (`mensaje_id`) REFERENCES `chat_mensajes` (`id_mensaje`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_lecturas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chat_reacciones`
  ADD CONSTRAINT `fk_chat_reacciones_mensaje` FOREIGN KEY (`mensaje_id`) REFERENCES `chat_mensajes` (`id_mensaje`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_reacciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chat_configuraciones`
  ADD CONSTRAINT `fk_chat_configuraciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chat_estados_usuario`
  ADD CONSTRAINT `fk_chat_estados_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chat_mensajes_eliminados`
  ADD CONSTRAINT `fk_chat_eliminados_mensaje` FOREIGN KEY (`mensaje_id`) REFERENCES `chat_mensajes` (`id_mensaje`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_eliminados_usuario` FOREIGN KEY (`usuario_elimino`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- CREAR ÍNDICES ADICIONALES
-- =============================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX `idx_conversacion_activa_ultimo` ON `chat_conversaciones` (`activo`, `archivado`, `ultimo_mensaje_fecha` DESC);
CREATE INDEX `idx_participante_activo_conversacion` ON `chat_participantes` (`activo`, `conversacion_id`, `usuario_id`);
CREATE INDEX `idx_mensaje_conversacion_fecha` ON `chat_mensajes` (`conversacion_id`, `fecha_envio` DESC, `eliminado`);
CREATE INDEX `idx_lectura_usuario_fecha` ON `chat_lecturas` (`usuario_id`, `fecha_lectura` DESC);
CREATE INDEX `idx_reaccion_mensaje_tipo` ON `chat_reacciones` (`mensaje_id`, `tipo_reaccion`);

-- Índices para búsquedas de texto
CREATE FULLTEXT INDEX `idx_mensaje_texto` ON `chat_mensajes` (`mensaje`);
CREATE FULLTEXT INDEX `idx_conversacion_nombre` ON `chat_conversaciones` (`nombre_conversacion`, `descripcion`);

-- =============================================
-- CREAR TRIGGERS
-- =============================================

-- Trigger para actualizar último mensaje en conversación
DELIMITER $$
CREATE TRIGGER `tr_chat_actualizar_ultimo_mensaje` 
AFTER INSERT ON `chat_mensajes`
FOR EACH ROW
BEGIN
    -- Solo actualizar si el mensaje no está eliminado
    IF NEW.eliminado = 0 THEN
        UPDATE chat_conversaciones 
        SET ultimo_mensaje = NEW.mensaje,
            ultimo_mensaje_fecha = NEW.fecha_envio,
            ultimo_mensaje_usuario_id = NEW.usuario_id,
            fecha_actualizacion = NOW()
        WHERE id_conversacion = NEW.conversacion_id;
    END IF;
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
    
    -- Crear estado de usuario si no existe
    INSERT IGNORE INTO chat_estados_usuario (usuario_id, estado) 
    VALUES (NEW.usuario_id, 'offline');
END$$
DELIMITER ;

-- Trigger para registrar eliminación de mensajes
DELIMITER $$
CREATE TRIGGER `tr_chat_registrar_eliminacion` 
BEFORE UPDATE ON `chat_mensajes`
FOR EACH ROW
BEGIN
    -- Si se marca como eliminado y antes no lo estaba
    IF NEW.eliminado = 1 AND OLD.eliminado = 0 THEN
        INSERT INTO chat_mensajes_eliminados 
        (mensaje_id, usuario_elimino, contenido_original, tipo_eliminacion)
        VALUES (NEW.id_mensaje, NEW.eliminado_por, OLD.mensaje, 'usuario');
        
        SET NEW.fecha_eliminacion = NOW();
    END IF;
END$$
DELIMITER ;

-- =============================================
-- CREAR PROCEDIMIENTOS ALMACENADOS
-- =============================================

-- Procedimiento para limpiar mensajes antiguos
DELIMITER $$
CREATE PROCEDURE `sp_chat_limpiar_mensajes_antiguos`(IN dias_antiguedad INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Mover mensajes eliminados a tabla de auditoría
    INSERT INTO chat_mensajes_eliminados 
    (mensaje_id, usuario_elimino, contenido_original, tipo_eliminacion)
    SELECT id_mensaje, usuario_id, mensaje, 'automatica'
    FROM chat_mensajes 
    WHERE fecha_envio < DATE_SUB(NOW(), INTERVAL dias_antiguedad DAY)
    AND eliminado = 0;
    
    -- Eliminar mensajes antiguos
    DELETE FROM chat_mensajes 
    WHERE fecha_envio < DATE_SUB(NOW(), INTERVAL dias_antiguedad DAY);
    
    COMMIT;
END$$
DELIMITER ;

-- Procedimiento para obtener estadísticas del chat
DELIMITER $$
CREATE PROCEDURE `sp_chat_estadisticas`(IN usuario_id INT)
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM chat_conversaciones WHERE creado_por = usuario_id) as conversaciones_creadas,
        (SELECT COUNT(*) FROM chat_participantes WHERE usuario_id = usuario_id AND activo = 1) as conversaciones_participando,
        (SELECT COUNT(*) FROM chat_mensajes WHERE usuario_id = usuario_id AND eliminado = 0) as mensajes_enviados,
        (SELECT COUNT(*) FROM chat_mensajes m 
         INNER JOIN chat_participantes p ON m.conversacion_id = p.conversacion_id 
         WHERE p.usuario_id = usuario_id AND m.fecha_envio > p.ultima_lectura AND m.usuario_id != usuario_id) as mensajes_no_leidos;
END$$
DELIMITER ;

-- =============================================
-- INSERTAR CONVERSACIONES INICIALES
-- =============================================

-- Crear conversación general del sistema
INSERT INTO `chat_conversaciones` (`id_conversacion`, `nombre_conversacion`, `descripcion`, `tipo_conversacion`, `creado_por`, `privado`) 
VALUES (1, 'Chat General', 'Conversación general para todos los usuarios del sistema', 'general', 1, 0);

-- Crear conversación de soporte técnico
INSERT INTO `chat_conversaciones` (`id_conversacion`, `nombre_conversacion`, `descripcion`, `tipo_conversacion`, `creado_por`, `privado`) 
VALUES (2, 'Soporte Técnico', 'Canal de soporte técnico para reportar problemas', 'canal', 1, 0);

-- Crear conversación de notificaciones del sistema
INSERT INTO `chat_conversaciones` (`id_conversacion`, `nombre_conversacion`, `descripcion`, `tipo_conversacion`, `creado_por`, `privado`) 
VALUES (3, 'Notificaciones del Sistema', 'Notificaciones automáticas del sistema', 'sistema', 1, 0);

-- =============================================
-- CREAR VISTAS OPTIMIZADAS
-- =============================================

-- Vista para conversaciones con información completa
CREATE VIEW `v_chat_conversaciones_info` AS
SELECT 
    c.id_conversacion,
    c.nombre_conversacion,
    c.descripcion,
    c.tipo_conversacion,
    c.sucursal_id,
    s.Nombre_Sucursal,
    c.creado_por,
    u.Nombre_Apellidos as creado_por_nombre,
    u.file_name as creado_por_avatar,
    c.fecha_creacion,
    c.fecha_actualizacion,
    c.ultimo_mensaje,
    c.ultimo_mensaje_fecha,
    c.ultimo_mensaje_usuario_id,
    um.Nombre_Apellidos as ultimo_mensaje_usuario_nombre,
    c.activo,
    c.privado,
    c.archivado,
    COUNT(p.id_participante) as total_participantes,
    COUNT(CASE WHEN p.activo = 1 THEN 1 END) as participantes_activos
FROM chat_conversaciones c
LEFT JOIN Sucursales s ON c.sucursal_id = s.ID_Sucursal
LEFT JOIN Usuarios_PV u ON c.creado_por = u.Id_PvUser
LEFT JOIN Usuarios_PV um ON c.ultimo_mensaje_usuario_id = um.Id_PvUser
LEFT JOIN chat_participantes p ON c.id_conversacion = p.conversacion_id
WHERE c.activo = 1
GROUP BY c.id_conversacion;

-- Vista para mensajes con información completa del usuario
CREATE VIEW `v_chat_mensajes_info` AS
SELECT 
    m.id_mensaje,
    m.conversacion_id,
    m.usuario_id,
    u.Nombre_Apellidos as usuario_nombre,
    u.file_name as usuario_avatar,
    t.TipoUsuario as usuario_tipo,
    m.mensaje,
    m.tipo_mensaje,
    m.archivo_url,
    m.archivo_nombre,
    m.archivo_tipo,
    m.archivo_tamaño,
    m.archivo_hash,
    m.fecha_envio,
    m.fecha_edicion,
    m.fecha_eliminacion,
    m.editado,
    m.eliminado,
    m.eliminado_por,
    eu.Nombre_Apellidos as eliminado_por_nombre,
    m.mensaje_respuesta_id,
    m.mensaje_original_id,
    m.metadatos,
    m.prioridad,
    m.destinatarios_especificos,
    -- Información de reacciones
    (SELECT COUNT(*) FROM chat_reacciones r WHERE r.mensaje_id = m.id_mensaje) as total_reacciones,
    -- Información de lecturas
    (SELECT COUNT(*) FROM chat_lecturas l WHERE l.mensaje_id = m.id_mensaje) as total_lecturas
FROM chat_mensajes m
LEFT JOIN Usuarios_PV u ON m.usuario_id = u.Id_PvUser
LEFT JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User
LEFT JOIN Usuarios_PV eu ON m.eliminado_por = eu.Id_PvUser
WHERE m.eliminado = 0;

-- Vista para participantes con información de usuario
CREATE VIEW `v_chat_participantes_info` AS
SELECT 
    p.id_participante,
    p.conversacion_id,
    p.usuario_id,
    u.Nombre_Apellidos as usuario_nombre,
    u.file_name as usuario_avatar,
    t.TipoUsuario as usuario_tipo,
    s.Nombre_Sucursal,
    p.rol,
    p.fecha_union,
    p.fecha_salida,
    p.ultima_lectura,
    p.notificaciones,
    p.silenciado,
    p.activo,
    p.configuracion_participante,
    -- Estado del usuario
    eu.estado as estado_usuario,
    eu.ultima_actividad,
    -- Mensajes no leídos
    (SELECT COUNT(*) FROM chat_mensajes m 
     WHERE m.conversacion_id = p.conversacion_id 
     AND m.fecha_envio > COALESCE(p.ultima_lectura, '1900-01-01')
     AND m.usuario_id != p.usuario_id
     AND m.eliminado = 0) as mensajes_no_leidos
FROM chat_participantes p
LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
LEFT JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User
LEFT JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal
LEFT JOIN chat_estados_usuario eu ON p.usuario_id = eu.usuario_id
WHERE p.activo = 1;

-- =============================================
-- INSERTAR CONFIGURACIONES PARA USUARIOS EXISTENTES
-- =============================================

-- Insertar configuraciones por defecto para usuarios existentes
INSERT IGNORE INTO chat_configuraciones (usuario_id)
SELECT Id_PvUser FROM Usuarios_PV WHERE Estatus = 'Activo';

-- Insertar estados de usuario para usuarios existentes
INSERT IGNORE INTO chat_estados_usuario (usuario_id, estado)
SELECT Id_PvUser, 'offline' FROM Usuarios_PV WHERE Estatus = 'Activo';

-- =============================================
-- FINALIZAR TRANSACCIÓN
-- =============================================

COMMIT;

-- Mostrar resumen de la instalación
SELECT 
    'Instalación del sistema de chat v2.0 completada exitosamente' as mensaje,
    NOW() as fecha_instalacion,
    (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name LIKE 'chat_%') as tablas_creadas,
    (SELECT COUNT(*) FROM information_schema.triggers WHERE trigger_schema = DATABASE() AND trigger_name LIKE 'tr_chat_%') as triggers_creados,
    (SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema = DATABASE() AND routine_name LIKE 'sp_chat_%') as procedimientos_creados,
    (SELECT COUNT(*) FROM information_schema.views WHERE table_schema = DATABASE() AND table_name LIKE 'v_chat_%') as vistas_creadas;
