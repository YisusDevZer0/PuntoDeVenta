-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT,
    tipo ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion DATETIME NOT NULL,
    fecha_lectura DATETIME,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar columna de suscripción push a la tabla usuarios si no existe
ALTER TABLE usuarios
ADD COLUMN IF NOT EXISTS push_subscription TEXT NULL AFTER email;

-- Índices para mejorar el rendimiento
CREATE INDEX idx_notificaciones_usuario ON notificaciones(usuario_id);
CREATE INDEX idx_notificaciones_fecha ON notificaciones(fecha_creacion);
CREATE INDEX idx_notificaciones_leida ON notificaciones(leida);

-- Procedimiento almacenado para marcar notificación como leída
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS marcar_notificacion_leida(IN notificacion_id INT)
BEGIN
    UPDATE notificaciones 
    SET leida = TRUE, 
        fecha_lectura = NOW() 
    WHERE id = notificacion_id;
END //
DELIMITER ;

-- Procedimiento almacenado para obtener notificaciones no leídas
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS obtener_notificaciones_no_leidas(IN usuario_id INT)
BEGIN
    SELECT id, titulo, mensaje, tipo, fecha_creacion
    FROM notificaciones
    WHERE usuario_id = usuario_id 
    AND leida = FALSE
    ORDER BY fecha_creacion DESC;
END //
DELIMITER ; 