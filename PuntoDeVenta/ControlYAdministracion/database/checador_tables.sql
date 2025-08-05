-- Script para crear las tablas del sistema de checador
-- Ejecutar este script en la base de datos del proyecto

-- Tabla para almacenar las ubicaciones de trabajo de los usuarios
CREATE TABLE IF NOT EXISTS `ubicaciones_trabajo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `latitud` decimal(10,8) NOT NULL,
  `longitud` decimal(11,8) NOT NULL,
  `radio` int(11) NOT NULL DEFAULT 100 COMMENT 'Radio en metros',
  `direccion` text,
  `estado` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `fk_ubicaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para almacenar los registros de asistencia
CREATE TABLE IF NOT EXISTS `asistencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` varchar(50) NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `latitud` decimal(10,8) NOT NULL,
  `longitud` decimal(11,8) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_fecha_hora` (`fecha_hora`),
  KEY `idx_usuario_fecha` (`usuario_id`, `fecha_hora`),
  CONSTRAINT `fk_asistencias_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para almacenar configuraciones del checador
CREATE TABLE IF NOT EXISTS `configuracion_checador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` varchar(50) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_clave` (`usuario_id`, `clave`),
  CONSTRAINT `fk_config_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para almacenar logs de actividad del checador
CREATE TABLE IF NOT EXISTS `logs_checador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` varchar(50) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `detalles` text,
  `ip_address` varchar(45),
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_accion` (`accion`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuraciones por defecto
INSERT IGNORE INTO `configuracion_checador` (`usuario_id`, `clave`, `valor`) VALUES
(1, 'radio_por_defecto', '100'),
(1, 'tiempo_verificacion', '300'),
(1, 'notificaciones_activas', 'true');

-- Crear índices adicionales para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS `idx_asistencias_usuario_tipo_fecha` ON `asistencias` (`usuario_id`, `tipo`, `fecha_hora`);
CREATE INDEX IF NOT EXISTS `idx_ubicaciones_usuario_activas` ON `ubicaciones_trabajo` (`usuario_id`, `estado`) WHERE `estado` = 'active';

-- Crear vista para estadísticas de asistencia
CREATE OR REPLACE VIEW `v_estadisticas_asistencia` AS
SELECT 
    a.usuario_id,
    u.Nombre_Apellidos,
    DATE(a.fecha_hora) as fecha,
    COUNT(CASE WHEN a.tipo = 'entrada' THEN 1 END) as entradas,
    COUNT(CASE WHEN a.tipo = 'salida' THEN 1 END) as salidas,
    COUNT(*) as total_registros
FROM asistencias a
JOIN Usuarios_PV u ON a.usuario_id = u.Id_PvUser
GROUP BY a.usuario_id, DATE(a.fecha_hora)
ORDER BY fecha DESC;

-- Crear vista para resumen mensual
CREATE OR REPLACE VIEW `v_resumen_mensual` AS
SELECT 
    a.usuario_id,
    u.nombre,
    u.apellido,
    YEAR(a.fecha_hora) as año,
    MONTH(a.fecha_hora) as mes,
    COUNT(CASE WHEN a.tipo = 'entrada' THEN 1 END) as total_entradas,
    COUNT(CASE WHEN a.tipo = 'salida' THEN 1 END) as total_salidas,
    COUNT(*) as total_registros
FROM asistencias a
JOIN usuarios u ON a.usuario_id = u.id
GROUP BY a.usuario_id, YEAR(a.fecha_hora), MONTH(a.fecha_hora)
ORDER BY año DESC, mes DESC;

-- Procedimiento almacenado para limpiar logs antiguos
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `LimpiarLogsChecador`(IN dias INT)
BEGIN
    DELETE FROM logs_checador 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL dias DAY);
END //
DELIMITER ;

-- Trigger para registrar logs automáticamente
DELIMITER //
CREATE TRIGGER IF NOT EXISTS `tr_asistencias_log` 
AFTER INSERT ON `asistencias`
FOR EACH ROW
BEGIN
    INSERT INTO logs_checador (usuario_id, accion, detalles, ip_address, user_agent)
    VALUES (
        NEW.usuario_id, 
        CONCAT('registro_', NEW.tipo), 
        CONCAT('Registro de ', NEW.tipo, ' en ', NEW.latitud, ',', NEW.longitud),
        @ip_address,
        @user_agent
    );
END //
DELIMITER ;

-- Comentarios sobre las tablas
-- ubicaciones_trabajo: Almacena las ubicaciones configuradas por cada usuario
-- asistencias: Registra las entradas y salidas de los empleados
-- configuracion_checador: Configuraciones personalizadas por usuario
-- logs_checador: Registra todas las actividades del sistema para auditoría

-- Notas importantes:
-- 1. Las coordenadas se almacenan con precisión de 8 decimales
-- 2. El radio se almacena en metros
-- 3. Se incluyen índices para optimizar las consultas más frecuentes
-- 4. Se usan foreign keys para mantener integridad referencial
-- 5. Se incluyen vistas para facilitar reportes
-- 6. Se incluye un procedimiento para limpieza automática de logs 