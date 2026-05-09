-- =============================================================================
-- Repara "Duplicate entry '0' for key 'PRIMARY'" al vender con 2+ productos
-- aunque Ventas_POS esté vacía: el TRIGGER inserta en otras tablas.
--
-- Tablas: inventory_movements (id), Errores_POS_Ventas (ID_Error)
-- Ejecuta TODO el bloque en la misma base (phpMyAdmin → SQL).
-- =============================================================================

DELETE FROM inventory_movements WHERE id = 0;
DELETE FROM Errores_POS_Ventas WHERE ID_Error = 0;
DELETE FROM Ventas_POS WHERE Venta_POS_ID = 0;

ALTER TABLE inventory_movements
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE Errores_POS_Ventas
  MODIFY ID_Error int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE Ventas_POS
  MODIFY Venta_POS_ID int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

-- Contador siguiente = máximo actual + 1 (tablas pueden estar vacías → 1)

SET @n := (SELECT COALESCE(MAX(id), 0) + 1 FROM inventory_movements);
SET @sql := CONCAT('ALTER TABLE inventory_movements AUTO_INCREMENT = ', @n);
PREPARE s FROM @sql;
EXECUTE s;
DEALLOCATE PREPARE s;

SET @n := (SELECT COALESCE(MAX(ID_Error), 0) + 1 FROM Errores_POS_Ventas);
SET @sql := CONCAT('ALTER TABLE Errores_POS_Ventas AUTO_INCREMENT = ', @n);
PREPARE s FROM @sql;
EXECUTE s;
DEALLOCATE PREPARE s;

SET @n := (SELECT COALESCE(MAX(Venta_POS_ID), 0) + 1 FROM Ventas_POS);
SET @sql := CONCAT('ALTER TABLE Ventas_POS AUTO_INCREMENT = ', @n);
PREPARE s FROM @sql;
EXECUTE s;
DEALLOCATE PREPARE s;
