-- =====================================================
-- SCRIPT SQL MÍNIMO PARA SISTEMA DE RECORDATORIOS
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

-- 6. Crear tabla de plantillas de mensajes
CREATE TABLE IF NOT EXISTS `recordatorios_plantillas` (
  `id_plantilla` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_plantilla` varchar(100) NOT NULL,
  `tipo_plantilla` enum('whatsapp','notificacion','ambos') NOT NULL,
  `contenido` text NOT NULL,
  `variables_disponibles` text,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_plantilla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSERTAR DATOS INICIALES (SIN la tabla problemática)
-- =====================================================

-- Insertar plantillas por defecto
INSERT IGNORE INTO `recordatorios_plantillas` (`nombre_plantilla`, `tipo_plantilla`, `contenido`, `variables_disponibles`) VALUES
('Recordatorio General', 'whatsapp', '🔔 *Recordatorio: {titulo}*\n\n{descripcion}\n\n📅 Fecha: {fecha}\n⏰ Hora: {hora}\n\n_Sistema Doctor Pez_', 'titulo,descripcion,fecha,hora'),
('Notificación Interna', 'notificacion', 'Recordatorio: {titulo}\n\n{descripcion}\n\nFecha: {fecha} {hora}', 'titulo,descripcion,fecha,hora'),
('Recordatorio Urgente', 'ambos', '🚨 *URGENTE* - {titulo}\n\n{descripcion}\n\n⏰ {fecha} {hora}\n\n_Requiere atención inmediata_', 'titulo,descripcion,fecha,hora');

-- Crear grupo por defecto
INSERT IGNORE INTO `recordatorios_grupos` (`nombre_grupo`, `descripcion`, `activo`) 
VALUES ('Todos los Usuarios', 'Grupo que incluye a todos los usuarios del sistema', 1);

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
-- FINALIZAR SCRIPT
-- =====================================================

-- Mostrar mensaje de éxito
SELECT 'Sistema de Recordatorios instalado correctamente' as mensaje;
