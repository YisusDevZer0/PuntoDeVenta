-- =====================================================
-- TABLA inventory_movements
-- =====================================================
-- Registra todos los movimientos de inventario para auditoría,
-- reportes de rotación, y compatibilidad con el sistema de movimientos.
--
-- Ejecutar PRIMERO antes de los triggers.
-- Base de datos: u858848268_doctorpez (o la que uses)
-- =====================================================

CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Fk_sucursal` int(12) NOT NULL COMMENT 'Sucursal donde ocurre el movimiento',
  `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL DEFAULT NULL,
  `Cod_Barra` varchar(100) DEFAULT NULL,
  `Nombre_Prod` varchar(250) DEFAULT NULL,
  `movement_type` varchar(20) NOT NULL COMMENT 'entry, exit, adjustment, count_correction',
  `reference_type` varchar(30) DEFAULT NULL COMMENT 'sale, purchase, transfer, count, manual',
  `reference_id` varchar(100) DEFAULT NULL COMMENT 'ID de venta, traspaso, conteo, etc.',
  `quantity` int(11) NOT NULL COMMENT 'Positivo=entrada, Negativo=salida',
  `stock_before` int(11) DEFAULT NULL,
  `stock_after` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `AgregadoPor` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_inv_mov_sucursal` (`Fk_sucursal`),
  KEY `idx_inv_mov_created` (`created_at`),
  KEY `idx_inv_mov_reference` (`reference_type`,`reference_id`),
  KEY `idx_inv_mov_cod_barra` (`Cod_Barra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
