-- =====================================================
-- OPTIMIZACIONES ADICIONALES PARA MÁXIMO RENDIMIENTO
-- Base de datos: u858848268_doctorpez
-- =====================================================
-- Estas optimizaciones complementan los índices para
-- lograr el máximo rendimiento posible
-- =====================================================

USE `u858848268_doctorpez`;

-- =====================================================
-- 1. OPTIMIZAR CONFIGURACIÓN DE TABLAS INNODB
-- =====================================================
-- Estas configuraciones mejoran el rendimiento de InnoDB
-- Ejecutar como usuario con privilegios de administrador

-- Optimizar buffer pool (ajustar según RAM disponible)
-- SET GLOBAL innodb_buffer_pool_size = 1073741824; -- 1GB (ajustar según tu servidor)

-- Optimizar log files
-- SET GLOBAL innodb_log_file_size = 268435456; -- 256MB

-- Optimizar flush method
-- SET GLOBAL innodb_flush_method = 'O_DIRECT';

-- =====================================================
-- 2. OPTIMIZAR TABLAS ESPECÍFICAS (RECONSTRUIR)
-- =====================================================
-- Reconstruye las tablas y optimiza el espacio
-- Ejecutar periódicamente (mensual o cuando notes lentitud)

OPTIMIZE TABLE `Ventas_POS`;
OPTIMIZE TABLE `Stock_POS`;
OPTIMIZE TABLE `Productos_POS`;
OPTIMIZE TABLE `Cajas`;
OPTIMIZE TABLE `Traspasos_generados`;
OPTIMIZE TABLE `ConteosDiarios`;
OPTIMIZE TABLE `Inventario_Turnos`;
OPTIMIZE TABLE `Devoluciones`;
OPTIMIZE TABLE `GastosPOS`;
OPTIMIZE TABLE `pedidos`;
OPTIMIZE TABLE `encargos`;
OPTIMIZE TABLE `CEDIS`;
OPTIMIZE TABLE `Creditos_POS`;
OPTIMIZE TABLE `AbonosCreditosVentas`;
OPTIMIZE TABLE `Solicitudes_Ingresos`;
OPTIMIZE TABLE `Data_Pacientes`;
OPTIMIZE TABLE `Notificaciones`;
OPTIMIZE TABLE `chat_conversaciones`;
OPTIMIZE TABLE `chat_mensajes`;
OPTIMIZE TABLE `Tareas`;

-- =====================================================
-- 3. ACTUALIZAR ESTADÍSTICAS DE TABLAS
-- =====================================================
-- Las estadísticas ayudan al optimizador a elegir el mejor plan de ejecución
-- Ejecutar después de cambios grandes en los datos

ANALYZE TABLE `Ventas_POS`;
ANALYZE TABLE `Stock_POS`;
ANALYZE TABLE `Productos_POS`;
ANALYZE TABLE `Cajas`;
ANALYZE TABLE `Traspasos_generados`;
ANALYZE TABLE `ConteosDiarios`;
ANALYZE TABLE `Inventario_Turnos`;
ANALYZE TABLE `Devoluciones`;
ANALYZE TABLE `GastosPOS`;
ANALYZE TABLE `pedidos`;
ANALYZE TABLE `encargos`;
ANALYZE TABLE `CEDIS`;
ANALYZE TABLE `Creditos_POS`;
ANALYZE TABLE `AbonosCreditosVentas`;
ANALYZE TABLE `Solicitudes_Ingresos`;
ANALYZE TABLE `Data_Pacientes`;
ANALYZE TABLE `Notificaciones`;
ANALYZE TABLE `chat_conversaciones`;
ANALYZE TABLE `chat_mensajes`;
ANALYZE TABLE `Tareas`;
ANALYZE TABLE `asistencias`;
ANALYZE TABLE `productos_lotes_caducidad`;
ANALYZE TABLE `Historial_Lotes`;

-- =====================================================
-- 4. VERIFICAR Y REPARAR TABLAS (si es necesario)
-- =====================================================
-- Solo ejecutar si hay problemas de integridad
-- CHECK TABLE `Ventas_POS`;
-- REPAIR TABLE `Ventas_POS`; -- Solo si CHECK encuentra errores

-- =====================================================
-- 5. CONFIGURAR QUERY CACHE (si está disponible)
-- =====================================================
-- Nota: Query Cache fue removido en MySQL 8.0+
-- Para MySQL 5.7 o MariaDB:
-- SET GLOBAL query_cache_size = 67108864; -- 64MB
-- SET GLOBAL query_cache_type = 1;

-- =====================================================
-- 6. OPTIMIZAR CONSULTAS FRECUENTES CON VISTAS MATERIALIZADAS
-- =====================================================
-- Crear vistas para consultas complejas que se repiten mucho
-- (Nota: MySQL no tiene vistas materializadas nativas, pero puedes usar tablas temporales)

-- Ejemplo: Vista para ventas del día (se puede cachear en aplicación)
-- CREATE OR REPLACE VIEW v_ventas_dia AS
-- SELECT 
--     DATE(Fecha_venta) AS fecha,
--     Fk_sucursal,
--     COUNT(*) AS total_ventas,
--     SUM(Importe + Pagos_tarjeta) AS total_importe
-- FROM Ventas_POS
-- WHERE Estatus = 'Pagado'
-- GROUP BY DATE(Fecha_venta), Fk_sucursal;

-- =====================================================
-- 7. LIMPIAR DATOS ANTIGUOS (si aplica)
-- =====================================================
-- Considera archivar o eliminar datos muy antiguos que ya no se usan
-- Ejemplo: Ventas de hace más de 2 años (ajustar según necesidades)

-- Ejemplo de archivo de datos antiguos:
-- CREATE TABLE Ventas_POS_Archivo LIKE Ventas_POS;
-- INSERT INTO Ventas_POS_Archivo 
-- SELECT * FROM Ventas_POS 
-- WHERE Fecha_venta < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);
-- DELETE FROM Ventas_POS WHERE Fecha_venta < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);

-- =====================================================
-- 8. CONFIGURAR PARTICIONADO (PARA TABLAS MUY GRANDES)
-- =====================================================
-- Particionar por fecha puede mejorar mucho el rendimiento
-- Solo para tablas con millones de registros

-- Ejemplo de particionado por fecha (ejecutar con cuidado):
-- ALTER TABLE Ventas_POS
-- PARTITION BY RANGE (YEAR(Fecha_venta)) (
--     PARTITION p2024 VALUES LESS THAN (2025),
--     PARTITION p2025 VALUES LESS THAN (2026),
--     PARTITION p2026 VALUES LESS THAN (2027),
--     PARTITION p_futuro VALUES LESS THAN MAXVALUE
-- );

-- =====================================================
-- 9. CONFIGURAR VARIABLES DE SESIÓN PARA CONSULTAS LENTAS
-- =====================================================
-- Estas configuraciones mejoran consultas complejas
-- Ejecutar al inicio de sesiones que hacen consultas pesadas

SET SESSION join_buffer_size = 262144; -- 256KB
SET SESSION sort_buffer_size = 262144; -- 256KB
SET SESSION read_buffer_size = 131072; -- 128KB
SET SESSION read_rnd_buffer_size = 262144; -- 256KB
SET SESSION tmp_table_size = 67108864; -- 64MB
SET SESSION max_heap_table_size = 67108864; -- 64MB

-- =====================================================
-- 10. VERIFICAR RENDIMIENTO DE CONSULTAS
-- =====================================================
-- Usar EXPLAIN para analizar consultas lentas

-- Ejemplo:
-- EXPLAIN SELECT * FROM Ventas_POS 
-- WHERE Fecha_venta = CURDATE() 
-- AND Fk_sucursal = 1;

-- Verificar que use índices (columna "key" debe mostrar el índice usado)

-- =====================================================
-- FIN DE OPTIMIZACIONES ADICIONALES
-- =====================================================
