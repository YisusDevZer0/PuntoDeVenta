-- =====================================================
-- MÓDULO DE GESTIÓN DE LOTES Y CADUCIDADES
-- =====================================================
-- Este módulo permite actualizar lotes y fechas de caducidad
-- con descuento automático mediante triggers

-- Tabla para registro de movimientos de lotes
CREATE TABLE IF NOT EXISTS `Gestion_Lotes_Movimientos` (
  `ID_Movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Lote_Anterior` varchar(100) DEFAULT NULL,
  `Lote_Nuevo` varchar(100) NOT NULL,
  `Fecha_Caducidad_Anterior` date DEFAULT NULL,
  `Fecha_Caducidad_Nueva` date NOT NULL,
  `Cantidad` int(11) NOT NULL DEFAULT 0,
  `Tipo_Movimiento` enum('actualizacion','ajuste','correccion') NOT NULL DEFAULT 'actualizacion',
  `Usuario_Modifico` varchar(250) NOT NULL,
  `Fecha_Modificacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Observaciones` text DEFAULT NULL,
  PRIMARY KEY (`ID_Movimiento`),
  KEY `idx_producto_sucursal` (`ID_Prod_POS`, `Fk_sucursal`),
  KEY `idx_cod_barra` (`Cod_Barra`),
  KEY `idx_fecha` (`Fecha_Modificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para historial de descuentos de lotes por ventas
-- (Se puede usar para registrar descuentos manuales desde la aplicación)
CREATE TABLE IF NOT EXISTS `Lotes_Descuentos_Ventas` (
  `ID_Descuento` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Venta` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `Folio_Ticket` varchar(100) DEFAULT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad_Descontada` int(11) NOT NULL DEFAULT 0,
  `Existencias_Antes` int(11) NOT NULL DEFAULT 0,
  `Existencias_Despues` int(11) NOT NULL DEFAULT 0,
  `Fecha_Descuento` timestamp NOT NULL DEFAULT current_timestamp(),
  `Usuario_Venta` varchar(250) DEFAULT NULL,
  `Tipo_Descuento` enum('manual','automatico') DEFAULT 'manual',
  PRIMARY KEY (`ID_Descuento`),
  KEY `idx_venta` (`Folio_Ticket`),
  KEY `idx_producto_lote` (`ID_Prod_POS`, `Lote`, `Fk_sucursal`),
  KEY `idx_fecha_caducidad` (`Fecha_Caducidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- NOTA: TRIGGER DESHABILITADO TEMPORALMENTE
-- =====================================================
-- El trigger de descuento automático ha sido deshabilitado
-- para evitar posibles problemas en el sistema de ventas.
-- 
-- El descuento de lotes se puede implementar desde la aplicación
-- cuando sea necesario, utilizando las tablas de auditoría:
-- - Lotes_Descuentos_Ventas: Para registrar descuentos manuales
-- - Gestion_Lotes_Movimientos: Para registrar cambios en lotes
--
-- Cuando se requiera implementar el descuento automático,
-- se puede crear un procedimiento almacenado o función PHP
-- que se ejecute después de registrar las ventas.

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================
ALTER TABLE `Historial_Lotes` 
ADD INDEX IF NOT EXISTS `idx_caducidad_existencias` (`Fecha_Caducidad`, `Existencias`);
