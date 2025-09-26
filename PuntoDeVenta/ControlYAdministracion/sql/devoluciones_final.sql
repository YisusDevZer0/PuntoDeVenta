-- SISTEMA DE DEVOLUCIONES - DOCTOR PEZ
-- Script SQL compatible con la estructura existente
-- Base de datos: u858848268_doctorpez

-- Tabla principal de devoluciones
CREATE TABLE IF NOT EXISTS `Devoluciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folio` varchar(50) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `estatus` enum('pendiente','procesada','cancelada') NOT NULL DEFAULT 'pendiente',
  `observaciones_generales` text,
  `total_productos` int(11) DEFAULT 0,
  `total_unidades` int(11) DEFAULT 0,
  `valor_total` decimal(15,2) DEFAULT 0.00,
  `fecha_procesada` timestamp NULL DEFAULT NULL,
  `usuario_procesa` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `idx_sucursal` (`sucursal_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_estatus` (`estatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalles de devoluciones
CREATE TABLE IF NOT EXISTS `Devoluciones_Detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `devolucion_id` int(11) NOT NULL,
  `producto_id` int(12) UNSIGNED ZEROFILL NOT NULL,
  `codigo_barras` varchar(100) NOT NULL,
  `nombre_producto` varchar(250) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `tipo_devolucion` varchar(50) NOT NULL,
  `observaciones` text,
  `lote` varchar(100) DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `precio_venta` decimal(15,2) DEFAULT 0.00,
  `precio_costo` decimal(15,2) DEFAULT 0.00,
  `valor_total` decimal(15,2) DEFAULT 0.00,
  `accion_tomada` enum('ajuste_inventario','traspaso','destruccion','reembolso','otro') DEFAULT 'ajuste_inventario',
  `observaciones_accion` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_devolucion` (`devolucion_id`),
  KEY `idx_producto` (`producto_id`),
  KEY `idx_codigo_barras` (`codigo_barras`),
  KEY `idx_tipo` (`tipo_devolucion`),
  CONSTRAINT `fk_devolucion_detalle` FOREIGN KEY (`devolucion_id`) REFERENCES `Devoluciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de tipos de devolución (configuración)
CREATE TABLE IF NOT EXISTS `Tipos_Devolucion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `color` varchar(20) DEFAULT '#6c757d',
  `requiere_autorizacion` tinyint(1) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar tipos de devolución por defecto
INSERT IGNORE INTO `Tipos_Devolucion` (`codigo`, `nombre`, `descripcion`, `color`, `requiere_autorizacion`) VALUES
('no_facturado', 'Producto no facturado', 'Producto que no fue facturado correctamente', '#007bff', 0),
('danado_recibir', 'Producto dañado al recibir', 'Producto que llegó dañado desde el proveedor', '#dc3545', 1),
('proximo_caducar', 'Próximo a caducar', 'Producto próximo a su fecha de caducidad', '#ffc107', 0),
('caducado', 'Producto caducado', 'Producto que ya caducó', '#6c757d', 1),
('danado_roto', 'Producto dañado/roto', 'Producto dañado o roto en almacén', '#dc3545', 1),
('solicitado_admin', 'Solicitado por administración', 'Devolución solicitada por administración', '#17a2b8', 1),
('error_etiquetado', 'Error en etiquetado', 'Error en etiquetado o información del producto', '#6f42c1', 0),
('defectuoso', 'Producto defectuoso', 'Producto con defectos de fabricación', '#dc3545', 1),
('sobrante', 'Sobrante de inventario', 'Exceso de inventario no utilizado', '#28a745', 0),
('otro', 'Otro motivo', 'Otro motivo no especificado', '#6c757d', 0);

-- Tabla de autorizaciones de devolución
CREATE TABLE IF NOT EXISTS `Devoluciones_Autorizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `devolucion_id` int(11) NOT NULL,
  `usuario_autoriza` int(11) NOT NULL,
  `fecha_autorizacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text,
  `estatus` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_devolucion` (`devolucion_id`),
  KEY `idx_usuario` (`usuario_autoriza`),
  CONSTRAINT `fk_devolucion_autorizacion` FOREIGN KEY (`devolucion_id`) REFERENCES `Devoluciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de acciones tomadas en devoluciones
CREATE TABLE IF NOT EXISTS `Devoluciones_Acciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `devolucion_id` int(11) NOT NULL,
  `detalle_id` int(11) NOT NULL,
  `tipo_accion` enum('ajuste_inventario','traspaso','destruccion','reembolso','otro') NOT NULL,
  `descripcion` text NOT NULL,
  `usuario_ejecuta` int(11) NOT NULL,
  `fecha_ejecucion` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text,
  `estatus` enum('pendiente','ejecutada','cancelada') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_devolucion` (`devolucion_id`),
  KEY `idx_detalle` (`detalle_id`),
  KEY `idx_usuario` (`usuario_ejecuta`),
  CONSTRAINT `fk_devolucion_accion` FOREIGN KEY (`devolucion_id`) REFERENCES `Devoluciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_accion` FOREIGN KEY (`detalle_id`) REFERENCES `Devoluciones_Detalle` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de reportes de devoluciones
CREATE TABLE IF NOT EXISTS `Devoluciones_Reportes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_reporte` varchar(50) NOT NULL,
  `parametros` json DEFAULT NULL,
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_genera` int(11) NOT NULL,
  `archivo_ruta` varchar(255) DEFAULT NULL,
  `estatus` enum('generando','completado','error') NOT NULL DEFAULT 'generando',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tipo` (`tipo_reporte`),
  KEY `idx_fecha` (`fecha_generacion`),
  KEY `idx_usuario` (`usuario_genera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices adicionales para optimización
CREATE INDEX IF NOT EXISTS idx_devoluciones_fecha_estatus ON Devoluciones(fecha, estatus);
CREATE INDEX IF NOT EXISTS idx_devoluciones_sucursal_fecha ON Devoluciones(sucursal_id, fecha);
CREATE INDEX IF NOT EXISTS idx_detalle_tipo_fecha ON Devoluciones_Detalle(tipo_devolucion, created_at);
CREATE INDEX IF NOT EXISTS idx_detalle_producto_cantidad ON Devoluciones_Detalle(producto_id, cantidad);

-- Triggers para actualizar totales automáticamente
DELIMITER $$

-- Trigger para INSERT
DROP TRIGGER IF EXISTS tr_devoluciones_detalle_insert$$
CREATE TRIGGER tr_devoluciones_detalle_insert
AFTER INSERT ON Devoluciones_Detalle
FOR EACH ROW
BEGIN
    UPDATE Devoluciones 
    SET total_productos = (
        SELECT COUNT(DISTINCT producto_id) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    ),
    total_unidades = (
        SELECT COALESCE(SUM(cantidad), 0) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    ),
    valor_total = (
        SELECT COALESCE(SUM(valor_total), 0.00) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    )
    WHERE id = NEW.devolucion_id;
END$$

-- Trigger para UPDATE
DROP TRIGGER IF EXISTS tr_devoluciones_detalle_update$$
CREATE TRIGGER tr_devoluciones_detalle_update
AFTER UPDATE ON Devoluciones_Detalle
FOR EACH ROW
BEGIN
    UPDATE Devoluciones 
    SET total_productos = (
        SELECT COUNT(DISTINCT producto_id) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    ),
    total_unidades = (
        SELECT COALESCE(SUM(cantidad), 0) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    ),
    valor_total = (
        SELECT COALESCE(SUM(valor_total), 0.00) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    )
    WHERE id = NEW.devolucion_id;
END$$

-- Trigger para DELETE
DROP TRIGGER IF EXISTS tr_devoluciones_detalle_delete$$
CREATE TRIGGER tr_devoluciones_detalle_delete
AFTER DELETE ON Devoluciones_Detalle
FOR EACH ROW
BEGIN
    UPDATE Devoluciones 
    SET total_productos = (
        SELECT COUNT(DISTINCT producto_id) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = OLD.devolucion_id
    ),
    total_unidades = (
        SELECT COALESCE(SUM(cantidad), 0) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = OLD.devolucion_id
    ),
    valor_total = (
        SELECT COALESCE(SUM(valor_total), 0.00) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = OLD.devolucion_id
    )
    WHERE id = OLD.devolucion_id;
END$$

DELIMITER ;

-- Vista para reportes de devoluciones - Compatible con estructura existente
DROP VIEW IF EXISTS v_devoluciones_completas;
CREATE VIEW v_devoluciones_completas AS
SELECT 
    d.id,
    d.folio,
    d.fecha,
    d.estatus,
    d.observaciones_generales,
    d.total_productos,
    d.total_unidades,
    d.valor_total,
    COALESCE(s.Nombre_Sucursal, CONCAT('Sucursal ID: ', d.sucursal_id)) as sucursal_nombre,
    COALESCE(u.Nombre_Apellidos, CONCAT('Usuario ID: ', d.usuario_id)) as usuario_nombre,
    COALESCE(tu.TipoUsuario, 'Usuario') as usuario_tipo
FROM Devoluciones d
LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser
LEFT JOIN Tipos_Usuarios tu ON u.Fk_Usuario = tu.ID_User;

-- Vista para detalles completos de devoluciones
DROP VIEW IF EXISTS v_devoluciones_detalle_completo;
CREATE VIEW v_devoluciones_detalle_completo AS
SELECT 
    dd.id,
    dd.devolucion_id,
    d.folio,
    dd.producto_id,
    dd.codigo_barras,
    dd.nombre_producto,
    dd.cantidad,
    dd.tipo_devolucion,
    COALESCE(td.nombre, dd.tipo_devolucion) as tipo_nombre,
    COALESCE(td.color, '#6c757d') as tipo_color,
    dd.observaciones,
    dd.lote,
    dd.fecha_caducidad,
    dd.precio_venta,
    dd.precio_costo,
    dd.valor_total,
    dd.accion_tomada,
    dd.observaciones_accion,
    dd.created_at,
    -- Información de la devolución
    d.fecha as fecha_devolucion,
    d.estatus as estatus_devolucion,
    -- Información de sucursal
    COALESCE(s.Nombre_Sucursal, CONCAT('Sucursal ID: ', d.sucursal_id)) as sucursal_nombre,
    -- Información de usuario
    COALESCE(u.Nombre_Apellidos, CONCAT('Usuario ID: ', d.usuario_id)) as usuario_nombre
FROM Devoluciones_Detalle dd
LEFT JOIN Devoluciones d ON dd.devolucion_id = d.id
LEFT JOIN Tipos_Devolucion td ON dd.tipo_devolucion = td.codigo
LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser;

-- Vista para estadísticas de devoluciones
DROP VIEW IF EXISTS v_estadisticas_devoluciones;
CREATE VIEW v_estadisticas_devoluciones AS
SELECT 
    DATE(d.fecha) as fecha,
    d.sucursal_id,
    s.Nombre_Sucursal,
    COUNT(*) as total_devoluciones,
    SUM(d.total_unidades) as total_unidades_devueltas,
    SUM(d.valor_total) as valor_total_devuelto,
    COUNT(CASE WHEN d.estatus = 'pendiente' THEN 1 END) as pendientes,
    COUNT(CASE WHEN d.estatus = 'procesada' THEN 1 END) as procesadas,
    COUNT(CASE WHEN d.estatus = 'cancelada' THEN 1 END) as canceladas
FROM Devoluciones d
LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
GROUP BY DATE(d.fecha), d.sucursal_id, s.Nombre_Sucursal
ORDER BY DATE(d.fecha) DESC;

-- Vista para productos más devueltos
DROP VIEW IF EXISTS v_productos_mas_devueltos;
CREATE VIEW v_productos_mas_devueltos AS
SELECT 
    dd.codigo_barras,
    dd.nombre_producto,
    COUNT(*) as total_devoluciones,
    SUM(dd.cantidad) as total_unidades_devueltas,
    SUM(dd.valor_total) as valor_total_devuelto,
    dd.tipo_devolucion,
    td.nombre as tipo_nombre,
    AVG(dd.cantidad) as promedio_cantidad_por_devolucion
FROM Devoluciones_Detalle dd
LEFT JOIN Tipos_Devolucion td ON dd.tipo_devolucion = td.codigo
LEFT JOIN Devoluciones d ON dd.devolucion_id = d.id
WHERE d.estatus = 'procesada'
GROUP BY dd.codigo_barras, dd.nombre_producto, dd.tipo_devolucion, td.nombre
ORDER BY total_devoluciones DESC, total_unidades_devueltas DESC;

-- Insertar datos de prueba (opcional)
-- INSERT IGNORE INTO Devoluciones (folio, sucursal_id, usuario_id, observaciones_generales) 
-- VALUES ('DEV-TEST-001', 1, 1, 'Devolución de prueba del sistema');

COMMIT;
