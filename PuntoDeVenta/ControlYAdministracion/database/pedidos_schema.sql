-- Esquema completo para el sistema de gestión de pedidos
-- Tabla principal de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(50) UNIQUE NOT NULL,
    sucursal_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'en_proceso', 'completado', 'cancelado') DEFAULT 'pendiente',
    prioridad ENUM('baja', 'normal', 'alta', 'urgente') DEFAULT 'normal',
    tipo_origen ENUM('admin', 'farmacia', 'cedis', 'sucursal') DEFAULT 'admin',
    observaciones TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_aprobacion DATETIME NULL,
    fecha_completado DATETIME NULL,
    aprobado_por INT NULL,
    total_estimado DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (sucursal_id) REFERENCES Sucursales(ID_Sucursal),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(ID_Usuario),
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(ID_Usuario),
    INDEX idx_sucursal (sucursal_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalles de pedidos
CREATE TABLE IF NOT EXISTS pedido_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad_solicitada DECIMAL(10,2) NOT NULL,
    cantidad_aprobada DECIMAL(10,2) NULL,
    cantidad_recibida DECIMAL(10,2) DEFAULT 0.00,
    precio_unitario DECIMAL(10,2) NULL,
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    observaciones TEXT,
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'recibido') DEFAULT 'pendiente',
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES Stock_POS(ID_Prod_POS),
    INDEX idx_pedido (pedido_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de historial de cambios de estado
CREATE TABLE IF NOT EXISTS pedido_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50),
    comentario TEXT,
    fecha_cambio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(ID_Usuario),
    INDEX idx_pedido_fecha (pedido_id, fecha_cambio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de proveedores para pedidos
CREATE TABLE IF NOT EXISTS proveedores_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    contacto VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(255),
    direccion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de asignación de proveedores a productos
CREATE TABLE IF NOT EXISTS producto_proveedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    proveedor_id INT NOT NULL,
    codigo_proveedor VARCHAR(100),
    precio_proveedor DECIMAL(10,2),
    tiempo_entrega_dias INT DEFAULT 7,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (producto_id) REFERENCES Stock_POS(ID_Prod_POS),
    FOREIGN KEY (proveedor_id) REFERENCES proveedores_pedidos(id),
    UNIQUE KEY unique_producto_proveedor (producto_id, proveedor_id),
    INDEX idx_producto (producto_id),
    INDEX idx_proveedor (proveedor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Procedimiento para generar folio único
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS generar_folio_pedido(OUT folio_generado VARCHAR(50))
BEGIN
    DECLARE contador INT;
    DECLARE fecha_actual VARCHAR(8);
    
    SET fecha_actual = DATE_FORMAT(NOW(), '%Y%m%d');
    
    SELECT COALESCE(MAX(SUBSTRING(folio, 9)), 0) + 1 
    INTO contador 
    FROM pedidos 
    WHERE folio LIKE CONCAT('PED', fecha_actual, '%');
    
    SET folio_generado = CONCAT('PED', fecha_actual, LPAD(contador, 4, '0'));
END //
DELIMITER ;

-- Trigger para actualizar total del pedido
DELIMITER //
CREATE TRIGGER IF NOT EXISTS actualizar_total_pedido
AFTER INSERT ON pedido_detalles
FOR EACH ROW
BEGIN
    UPDATE pedidos 
    SET total_estimado = (
        SELECT SUM(subtotal) 
        FROM pedido_detalles 
        WHERE pedido_id = NEW.pedido_id
    )
    WHERE id = NEW.pedido_id;
END //
DELIMITER ;

-- Trigger para actualizar total cuando se modifica un detalle
DELIMITER //
CREATE TRIGGER IF NOT EXISTS actualizar_total_pedido_update
AFTER UPDATE ON pedido_detalles
FOR EACH ROW
BEGIN
    UPDATE pedidos 
    SET total_estimado = (
        SELECT SUM(subtotal) 
        FROM pedido_detalles 
        WHERE pedido_id = NEW.pedido_id
    )
    WHERE id = NEW.pedido_id;
END //
DELIMITER ;

-- Vista para pedidos con información completa
CREATE OR REPLACE VIEW vista_pedidos_completa AS
SELECT 
    p.id,
    p.folio,
    p.estado,
    p.prioridad,
    p.tipo_origen,
    p.observaciones,
    p.fecha_creacion,
    p.fecha_aprobacion,
    p.fecha_completado,
    p.total_estimado,
    s.ID_Sucursal,
    s.Nombre_Sucursal,
    u.Nombre AS usuario_nombre,
    u.Apellido AS usuario_apellido,
    aprobador.Nombre AS aprobador_nombre,
    aprobador.Apellido AS aprobador_apellido,
    COUNT(pd.id) AS total_productos,
    SUM(pd.cantidad_solicitada) AS total_cantidad_solicitada
FROM pedidos p
LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
LEFT JOIN usuarios u ON p.usuario_id = u.ID_Usuario
LEFT JOIN usuarios aprobador ON p.aprobado_por = aprobador.ID_Usuario
LEFT JOIN pedido_detalles pd ON p.id = pd.pedido_id
GROUP BY p.id;

-- Índices adicionales para optimización
CREATE INDEX idx_pedidos_fecha_estado ON pedidos(fecha_creacion, estado);
CREATE INDEX idx_pedidos_sucursal_estado ON pedidos(sucursal_id, estado);
CREATE INDEX idx_detalles_estado ON pedido_detalles(estado); 