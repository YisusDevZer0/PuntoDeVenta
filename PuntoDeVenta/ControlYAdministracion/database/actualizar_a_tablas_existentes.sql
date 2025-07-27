-- Script para actualizar el sistema de pedidos administrativos
-- para usar las tablas existentes en lugar de crear nuevas

-- Eliminar las tablas que creamos innecesariamente
DROP TABLE IF EXISTS `Pedidos_Administrativos`;
DROP TABLE IF EXISTS `Pedidos_Administrativos_Detalle`;

-- Verificar que las tablas existentes estén disponibles
-- Las tablas que usaremos son:
-- - pedidos (tabla principal)
-- - pedido_detalles (detalles de productos)
-- - pedido_historial (historial de cambios)
-- - proveedores_pedidos (proveedores)
-- - producto_proveedor (relación productos-proveedores)
-- - encargos (tabla existente de encargos)

-- Verificar que las tablas existan
SELECT 'Verificando tablas existentes...' as mensaje;

-- Verificar tabla pedidos
SELECT COUNT(*) as total_pedidos FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'pedidos';

-- Verificar tabla pedido_detalles
SELECT COUNT(*) as total_detalles FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'pedido_detalles';

-- Verificar tabla pedido_historial
SELECT COUNT(*) as total_historial FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'pedido_historial';

-- Verificar tabla encargos (existente)
SELECT COUNT(*) as total_encargos FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'encargos';

-- Si las tablas no existen, crearlas usando el esquema simple
-- (Esto solo se ejecutará si las tablas no existen)

-- Crear tabla pedidos si no existe
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
    INDEX idx_fecha_creacion (fecha_creacion),
    INDEX idx_tipo_origen (tipo_origen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla pedido_detalles si no existe
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

-- Crear tabla pedido_historial si no existe
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

-- Crear tabla proveedores_pedidos si no existe
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

-- Crear tabla producto_proveedor si no existe
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

-- Crear índices adicionales para optimización
CREATE INDEX IF NOT EXISTS idx_pedidos_fecha_estado ON pedidos(fecha_creacion, estado);
CREATE INDEX IF NOT EXISTS idx_pedidos_sucursal_estado ON pedidos(sucursal_id, estado);
CREATE INDEX IF NOT EXISTS idx_pedidos_tipo_origen ON pedidos(tipo_origen);
CREATE INDEX IF NOT EXISTS idx_detalles_estado ON pedido_detalles(estado);

-- Mensaje de confirmación
SELECT 'Sistema actualizado exitosamente. Ahora usa las tablas existentes incluyendo la tabla encargos correcta.' as resultado; 