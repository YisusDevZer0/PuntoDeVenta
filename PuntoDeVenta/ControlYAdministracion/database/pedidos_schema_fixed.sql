-- Esquema corregido para MariaDB - Sistema de Gestión de Pedidos
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
    UNIQUE KEY unique_producto_proveedor (producto_id, proveedor_id),
    INDEX idx_producto (producto_id),
    INDEX idx_proveedor (proveedor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices adicionales para optimización
CREATE INDEX idx_pedidos_fecha_estado ON pedidos(fecha_creacion, estado);
CREATE INDEX idx_pedidos_sucursal_estado ON pedidos(sucursal_id, estado);
CREATE INDEX idx_detalles_estado ON pedido_detalles(estado);

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