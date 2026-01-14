-- Script para modificar la tabla ListadoServicios
-- Agregar campos para Costo, Comisión y tipo de costo

-- Agregar campo Costo (precio base del servicio)
ALTER TABLE `ListadoServicios` 
ADD COLUMN `Costo` DECIMAL(10,2) DEFAULT 0.00 AFTER `Servicio`;

-- Agregar campo Comision (comisión asociada al servicio)
ALTER TABLE `ListadoServicios` 
ADD COLUMN `Comision` DECIMAL(10,2) DEFAULT 0.00 AFTER `Costo`;

-- Agregar campo CostoVariable para indicar si el costo es variable o fijo
-- 'S' = Sí (variable), 'N' = No (fijo)
ALTER TABLE `ListadoServicios` 
ADD COLUMN `CostoVariable` VARCHAR(1) DEFAULT 'N' AFTER `Comision`;

-- Agregar índice para mejorar las búsquedas por Servicio_ID
ALTER TABLE `ListadoServicios`
ADD INDEX `idx_servicio_id` (`Servicio_ID`);
