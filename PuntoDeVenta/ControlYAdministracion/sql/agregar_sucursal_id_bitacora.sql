-- Script para agregar el campo sucursal_id a la tabla Bitacora_Limpieza
-- Este script debe ejecutarse en la base de datos para habilitar la asignación de bitácoras a sucursales
-- VERSIÓN SIMPLIFICADA PARA HOSTING COMPARTIDO

-- Paso 1: Agregar el campo sucursal_id a la tabla Bitacora_Limpieza
ALTER TABLE `Bitacora_Limpieza` 
ADD COLUMN `sucursal_id` int(11) DEFAULT NULL AFTER `aux_res`;

-- Paso 2: Agregar campos de timestamp
ALTER TABLE `Bitacora_Limpieza` 
ADD COLUMN `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `firma_aux_res`;

ALTER TABLE `Bitacora_Limpieza` 
ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Paso 3: Agregar índice para mejorar el rendimiento (opcional)
ALTER TABLE `Bitacora_Limpieza` 
ADD INDEX `idx_sucursal_id` (`sucursal_id`);

-- Paso 4: Actualizar las bitácoras existentes para asignarlas a una sucursal por defecto
-- IMPORTANTE: Cambia el ID 1 por el ID de la sucursal que desees usar como predeterminada
-- Primero verifica qué sucursales tienes disponibles:
-- SELECT Id_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Estado = 1;

UPDATE `Bitacora_Limpieza` 
SET `sucursal_id` = 1 
WHERE `sucursal_id` IS NULL;

-- Paso 5: Verificar que los cambios se aplicaron correctamente
-- (Ejecuta estos comandos uno por uno si el anterior falla)
SHOW COLUMNS FROM `Bitacora_Limpieza`;

-- Verificar que las bitácoras tienen sucursal asignada
SELECT id_bitacora, area, sucursal_id FROM `Bitacora_Limpieza` LIMIT 5;
