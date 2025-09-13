-- =============================================
-- MIGRACIÓN DEL SISTEMA DE CHAT A VERSIÓN 2.0
-- =============================================

-- Verificar si las tablas existen antes de migrar
SET @tabla_existe = (
    SELECT COUNT(*) 
    FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name = 'chat_conversaciones'
);

-- Solo ejecutar si las tablas existen
SET @sql = IF(@tabla_existe > 0, 
    '-- =============================================
-- MIGRACIÓN A VERSIÓN 2.0
-- =============================================

-- 1. Agregar nuevas columnas a chat_conversaciones
ALTER TABLE chat_conversaciones 
ADD COLUMN IF NOT EXISTS descripcion text DEFAULT NULL AFTER nombre_conversacion,
ADD COLUMN IF NOT EXISTS fecha_actualizacion timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER fecha_creacion,
ADD COLUMN IF NOT EXISTS ultimo_mensaje_usuario_id int(11) DEFAULT NULL AFTER ultimo_mensaje_fecha,
ADD COLUMN IF NOT EXISTS privado tinyint(1) NOT NULL DEFAULT 0 AFTER activo,
ADD COLUMN IF NOT EXISTS archivado tinyint(1) NOT NULL DEFAULT 0 AFTER privado,
ADD COLUMN IF NOT EXISTS configuracion json DEFAULT NULL AFTER archivado;

-- 2. Agregar nuevas columnas a chat_participantes
ALTER TABLE chat_participantes 
ADD COLUMN IF NOT EXISTS rol enum(''admin'',''moderador'',''miembro'') NOT NULL DEFAULT ''miembro'' AFTER usuario_id,
ADD COLUMN IF NOT EXISTS fecha_salida timestamp NULL DEFAULT NULL AFTER fecha_union,
ADD COLUMN IF NOT EXISTS silenciado tinyint(1) NOT NULL DEFAULT 0 AFTER notificaciones,
ADD COLUMN IF NOT EXISTS configuracion_participante json DEFAULT NULL AFTER activo;

-- 3. Agregar nuevas columnas a chat_mensajes
ALTER TABLE chat_mensajes 
ADD COLUMN IF NOT EXISTS archivo_hash varchar(64) DEFAULT NULL AFTER archivo_tamaño,
ADD COLUMN IF NOT EXISTS fecha_eliminacion timestamp NULL DEFAULT NULL AFTER fecha_edicion,
ADD COLUMN IF NOT EXISTS eliminado_por int(11) DEFAULT NULL AFTER eliminado,
ADD COLUMN IF NOT EXISTS mensaje_original_id int(11) DEFAULT NULL COMMENT ''Para mensajes reenviados'' AFTER mensaje_respuesta_id,
ADD COLUMN IF NOT EXISTS metadatos json DEFAULT NULL AFTER mensaje_original_id,
ADD COLUMN IF NOT EXISTS prioridad enum(''baja'',''normal'',''alta'',''urgente'') NOT NULL DEFAULT ''normal'' AFTER metadatos,
ADD COLUMN IF NOT EXISTS destinatarios_especificos json DEFAULT NULL COMMENT ''Para mensajes privados en grupos'' AFTER prioridad;

-- 4. Agregar nuevas columnas a chat_reacciones
ALTER TABLE chat_reacciones 
ADD COLUMN IF NOT EXISTS dispositivo varchar(50) DEFAULT NULL AFTER fecha_reaccion;

-- 5. Agregar nuevas columnas a chat_configuraciones
ALTER TABLE chat_configuraciones 
ADD COLUMN IF NOT EXISTS notificaciones_email tinyint(1) NOT NULL DEFAULT 0 AFTER notificaciones_push,
ADD COLUMN IF NOT EXISTS idioma varchar(5) NOT NULL DEFAULT ''es'' AFTER auto_borrar_mensajes,
ADD COLUMN IF NOT EXISTS zona_horaria varchar(50) NOT NULL DEFAULT ''America/Monterrey'' AFTER idioma,
ADD COLUMN IF NOT EXISTS mostrar_online tinyint(1) NOT NULL DEFAULT 1 AFTER zona_horaria,
ADD COLUMN IF NOT EXISTS mostrar_ultima_vez tinyint(1) NOT NULL DEFAULT 1 AFTER mostrar_online,
ADD COLUMN IF NOT EXISTS auto_descargar_archivos tinyint(1) NOT NULL DEFAULT 0 AFTER mostrar_ultima_vez,
ADD COLUMN IF NOT EXISTS tamaño_maximo_archivo int(11) NOT NULL DEFAULT 10485760 COMMENT ''En bytes (10MB por defecto)'' AFTER auto_descargar_archivos,
ADD COLUMN IF NOT EXISTS configuracion_avanzada json DEFAULT NULL AFTER fecha_actualizacion;

-- 6. Agregar nuevas columnas a chat_lecturas
ALTER TABLE chat_lecturas 
ADD COLUMN IF NOT EXISTS dispositivo varchar(50) DEFAULT NULL AFTER fecha_lectura,
ADD COLUMN IF NOT EXISTS ip_address varchar(45) DEFAULT NULL AFTER dispositivo;

-- 7. Crear nuevas tablas
CREATE TABLE IF NOT EXISTS chat_estados_usuario (
  id_estado int(11) NOT NULL AUTO_INCREMENT,
  usuario_id int(11) NOT NULL,
  estado enum(''online'',''offline'',''ausente'',''ocupado'',''invisible'') NOT NULL DEFAULT ''offline'',
  ultima_actividad timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  dispositivo varchar(50) DEFAULT NULL,
  ip_address varchar(45) DEFAULT NULL,
  user_agent text DEFAULT NULL,
  ubicacion varchar(255) DEFAULT NULL,
  PRIMARY KEY (id_estado),
  UNIQUE KEY unique_usuario (usuario_id),
  KEY idx_estado (estado),
  KEY idx_ultima_actividad (ultima_actividad),
  CONSTRAINT fk_chat_estados_usuario FOREIGN KEY (usuario_id) REFERENCES Usuarios_PV (Id_PvUser) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=''Estados de conexión de usuarios'';

CREATE TABLE IF NOT EXISTS chat_mensajes_eliminados (
  id_eliminacion int(11) NOT NULL AUTO_INCREMENT,
  mensaje_id int(11) NOT NULL,
  usuario_elimino int(11) NOT NULL,
  fecha_eliminacion timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  motivo varchar(255) DEFAULT NULL,
  tipo_eliminacion enum(''usuario'',''admin'',''sistema'',''automatica'') NOT NULL DEFAULT ''usuario'',
  contenido_original text DEFAULT NULL,
  PRIMARY KEY (id_eliminacion),
  KEY idx_mensaje_id (mensaje_id),
  KEY idx_usuario_elimino (usuario_elimino),
  KEY idx_fecha_eliminacion (fecha_eliminacion),
  CONSTRAINT fk_chat_eliminados_mensaje FOREIGN KEY (mensaje_id) REFERENCES chat_mensajes (id_mensaje) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_chat_eliminados_usuario FOREIGN KEY (usuario_elimino) REFERENCES Usuarios_PV (Id_PvUser) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=''Auditoría de mensajes eliminados'';

-- 8. Agregar nuevas claves foráneas
ALTER TABLE chat_conversaciones 
ADD CONSTRAINT fk_chat_conversaciones_sucursal FOREIGN KEY (sucursal_id) REFERENCES Sucursales (ID_Sucursal) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT fk_chat_conversaciones_usuario FOREIGN KEY (creado_por) REFERENCES Usuarios_PV (Id_PvUser) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE chat_participantes 
ADD CONSTRAINT fk_chat_participantes_conversacion FOREIGN KEY (conversacion_id) REFERENCES chat_conversaciones (id_conversacion) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_chat_participantes_usuario FOREIGN KEY (usuario_id) REFERENCES Usuarios_PV (Id_PvUser) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE chat_mensajes 
ADD CONSTRAINT fk_chat_mensajes_conversacion FOREIGN KEY (conversacion_id) REFERENCES chat_conversaciones (id_conversacion) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_chat_mensajes_usuario FOREIGN KEY (usuario_id) REFERENCES Usuarios_PV (Id_PvUser) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_chat_mensajes_respuesta FOREIGN KEY (mensaje_respuesta_id) REFERENCES chat_mensajes (id_mensaje) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT fk_chat_mensajes_eliminado_por FOREIGN KEY (eliminado_por) REFERENCES Usuarios_PV (Id_PvUser) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE chat_lecturas 
ADD CONSTRAINT fk_chat_lecturas_mensaje FOREIGN KEY (mensaje_id) REFERENCES chat_mensajes (id_mensaje) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_chat_lecturas_usuario FOREIGN KEY (usuario_id) REFERENCES Usuarios_PV (Id_PvUser) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE chat_reacciones 
ADD CONSTRAINT fk_chat_reacciones_mensaje FOREIGN KEY (mensaje_id) REFERENCES chat_mensajes (id_mensaje) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_chat_reacciones_usuario FOREIGN KEY (usuario_id) REFERENCES Usuarios_PV (Id_PvUser) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE chat_configuraciones 
ADD CONSTRAINT fk_chat_configuraciones_usuario FOREIGN KEY (usuario_id) REFERENCES Usuarios_PV (Id_PvUser) ON DELETE CASCADE ON UPDATE CASCADE;

-- 9. Crear nuevos índices
CREATE INDEX IF NOT EXISTS idx_conversacion_activa_ultimo ON chat_conversaciones (activo, archivado, ultimo_mensaje_fecha DESC);
CREATE INDEX IF NOT EXISTS idx_participante_activo_conversacion ON chat_participantes (activo, conversacion_id, usuario_id);
CREATE INDEX IF NOT EXISTS idx_mensaje_conversacion_fecha ON chat_mensajes (conversacion_id, fecha_envio DESC, eliminado);
CREATE INDEX IF NOT EXISTS idx_lectura_usuario_fecha ON chat_lecturas (usuario_id, fecha_lectura DESC);
CREATE INDEX IF NOT EXISTS idx_reaccion_mensaje_tipo ON chat_reacciones (mensaje_id, tipo_reaccion);
CREATE INDEX IF NOT EXISTS idx_archivo_hash ON chat_mensajes (archivo_hash);

-- 10. Crear índices de texto completo
CREATE FULLTEXT INDEX IF NOT EXISTS idx_mensaje_texto ON chat_mensajes (mensaje);
CREATE FULLTEXT INDEX IF NOT EXISTS idx_conversacion_nombre ON chat_conversaciones (nombre_conversacion, descripcion);

-- 11. Actualizar triggers existentes
DROP TRIGGER IF EXISTS tr_chat_actualizar_ultimo_mensaje;
DELIMITER $$
CREATE TRIGGER tr_chat_actualizar_ultimo_mensaje 
AFTER INSERT ON chat_mensajes
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

-- 12. Crear nuevos triggers
DROP TRIGGER IF EXISTS tr_chat_registrar_eliminacion;
DELIMITER $$
CREATE TRIGGER tr_chat_registrar_eliminacion 
BEFORE UPDATE ON chat_mensajes
FOR EACH ROW
BEGIN
    -- Si se marca como eliminado y antes no lo estaba
    IF NEW.eliminado = 1 AND OLD.eliminado = 0 THEN
        INSERT INTO chat_mensajes_eliminados 
        (mensaje_id, usuario_elimino, contenido_original, tipo_eliminacion)
        VALUES (NEW.id_mensaje, NEW.eliminado_por, OLD.mensaje, ''usuario'');
        
        SET NEW.fecha_eliminacion = NOW();
    END IF;
END$$
DELIMITER ;

-- 13. Crear procedimientos almacenados
DROP PROCEDURE IF EXISTS sp_chat_limpiar_mensajes_antiguos;
DELIMITER $$
CREATE PROCEDURE sp_chat_limpiar_mensajes_antiguos(IN dias_antiguedad INT)
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
    SELECT id_mensaje, usuario_id, mensaje, ''automatica''
    FROM chat_mensajes 
    WHERE fecha_envio < DATE_SUB(NOW(), INTERVAL dias_antiguedad DAY)
    AND eliminado = 0;
    
    -- Eliminar mensajes antiguos
    DELETE FROM chat_mensajes 
    WHERE fecha_envio < DATE_SUB(NOW(), INTERVAL dias_antiguedad DAY);
    
    COMMIT;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_chat_estadisticas;
DELIMITER $$
CREATE PROCEDURE sp_chat_estadisticas(IN usuario_id INT)
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

-- 14. Actualizar vistas existentes
DROP VIEW IF EXISTS v_chat_conversaciones_info;
CREATE VIEW v_chat_conversaciones_info AS
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

DROP VIEW IF EXISTS v_chat_mensajes_info;
CREATE VIEW v_chat_mensajes_info AS
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

-- 15. Crear nueva vista para participantes
CREATE VIEW v_chat_participantes_info AS
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
     AND m.fecha_envio > COALESCE(p.ultima_lectura, ''1900-01-01'')
     AND m.usuario_id != p.usuario_id
     AND m.eliminado = 0) as mensajes_no_leidos
FROM chat_participantes p
LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
LEFT JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User
LEFT JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal
LEFT JOIN chat_estados_usuario eu ON p.usuario_id = eu.usuario_id
WHERE p.activo = 1;

-- 16. Insertar datos iniciales para usuarios existentes
INSERT IGNORE INTO chat_estados_usuario (usuario_id, estado)
SELECT Id_PvUser, ''offline'' FROM Usuarios_PV WHERE Estatus = ''Activo'';

-- 17. Actualizar configuraciones existentes con valores por defecto
UPDATE chat_configuraciones 
SET 
    notificaciones_email = 0,
    idioma = ''es'',
    zona_horaria = ''America/Monterrey'',
    mostrar_online = 1,
    mostrar_ultima_vez = 1,
    auto_descargar_archivos = 0,
    tamaño_maximo_archivo = 10485760
WHERE notificaciones_email IS NULL;

SELECT ''Migración a versión 2.0 completada exitosamente'' as resultado;', 
    'SELECT ''No se encontraron tablas de chat para migrar'' as resultado;'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
