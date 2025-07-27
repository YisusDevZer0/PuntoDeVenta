-- Tabla principal de pedidos administrativos
CREATE TABLE IF NOT EXISTS `Pedidos_Administrativos` (
  `ID_Pedido` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha_Creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fecha_Modificacion` datetime NULL,
  `Solicitante` varchar(255) NOT NULL,
  `Total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Observaciones` text NULL,
  `Prioridad` enum('baja','media','alta','urgente') NOT NULL DEFAULT 'media',
  `Estado` enum('pendiente','aprobado','completado','cancelado') NOT NULL DEFAULT 'pendiente',
  `Sucursal` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Pedido`),
  KEY `idx_fecha_creacion` (`Fecha_Creacion`),
  KEY `idx_estado` (`Estado`),
  KEY `idx_sucursal` (`Sucursal`),
  KEY `idx_solicitante` (`Solicitante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalles de pedidos administrativos
CREATE TABLE IF NOT EXISTS `Pedidos_Administrativos_Detalle` (
  `ID_Detalle` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Pedido` int(11) NOT NULL,
  `ID_Producto` int(11) NULL,
  `Cantidad` int(11) NOT NULL DEFAULT 1,
  `Precio_Unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Es_Encargo` tinyint(1) NOT NULL DEFAULT 0,
  `ID_Encargo` int(11) NULL,
  PRIMARY KEY (`ID_Detalle`),
  KEY `idx_pedido` (`ID_Pedido`),
  KEY `idx_producto` (`ID_Producto`),
  KEY `idx_encargo` (`ID_Encargo`),
  CONSTRAINT `fk_pedido_detalle` FOREIGN KEY (`ID_Pedido`) REFERENCES `Pedidos_Administrativos` (`ID_Pedido`) ON DELETE CASCADE,
  CONSTRAINT `fk_producto_detalle` FOREIGN KEY (`ID_Producto`) REFERENCES `Productos_POS` (`ID_Prod_POS`) ON DELETE SET NULL,
  CONSTRAINT `fk_encargo_detalle` FOREIGN KEY (`ID_Encargo`) REFERENCES `Encargos` (`ID_Encargo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de encargos (si no existe)
CREATE TABLE IF NOT EXISTS `Encargos` (
  `ID_Encargo` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(500) NOT NULL,
  `Cliente` varchar(255) NOT NULL,
  `Fecha_Solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Fecha_Entrega_Estimada` date NULL,
  `Observaciones` text NULL,
  `Precio_Estimado` decimal(10,2) NULL DEFAULT 0.00,
  `Estado` enum('pendiente','en_proceso','completado','cancelado') NOT NULL DEFAULT 'pendiente',
  `Sucursal` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Encargo`),
  KEY `idx_fecha_solicitud` (`Fecha_Solicitud`),
  KEY `idx_estado` (`Estado`),
  KEY `idx_sucursal` (`Sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunos datos de ejemplo para encargos
INSERT INTO `Encargos` (`Descripcion`, `Cliente`, `Fecha_Solicitud`, `Observaciones`, `Precio_Estimado`, `Estado`, `Sucursal`) VALUES
('Medicamento especial para diabetes', 'Juan Pérez', NOW(), 'Medicamento específico que no tenemos en inventario', 150.00, 'pendiente', 'SUC001'),
('Producto de belleza específico', 'María García', NOW(), 'Producto de marca específica solicitado por cliente', 89.50, 'pendiente', 'SUC001'),
('Suplemento vitamínico especial', 'Carlos López', NOW(), 'Suplemento con composición específica', 200.00, 'pendiente', 'SUC001');

-- Crear índices adicionales para mejor rendimiento
CREATE INDEX IF NOT EXISTS `idx_pedidos_fecha_estado` ON `Pedidos_Administrativos` (`Fecha_Creacion`, `Estado`);
CREATE INDEX IF NOT EXISTS `idx_pedidos_sucursal_estado` ON `Pedidos_Administrativos` (`Sucursal`, `Estado`);
CREATE INDEX IF NOT EXISTS `idx_encargos_fecha_estado` ON `Encargos` (`Fecha_Solicitud`, `Estado`);

-- Comentarios sobre las tablas
ALTER TABLE `Pedidos_Administrativos` COMMENT = 'Tabla principal para gestionar pedidos administrativos';
ALTER TABLE `Pedidos_Administrativos_Detalle` COMMENT = 'Detalles de productos en pedidos administrativos';
ALTER TABLE `Encargos` COMMENT = 'Tabla para gestionar encargos especiales de clientes'; 