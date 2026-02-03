-- =====================================================
-- EJEMPLO COMPLETO: OPTIMIZAR TABLA Ventas_POS
-- Base de datos: u858848268_doctorpez
-- =====================================================

USE `u858848268_doctorpez`;

-- =====================================================
-- PASO 1: VER ESTADO ACTUAL DE LA TABLA
-- =====================================================
-- Ver tamaño, fragmentación y estadísticas actuales

SELECT 
    TABLE_NAME AS 'Tabla',
    TABLE_ROWS AS 'Filas_Aproximadas',
    ROUND(DATA_LENGTH / 1024 / 1024, 2) AS 'Tamaño_Datos_MB',
    ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS 'Tamaño_Indices_MB',
    ROUND(DATA_FREE / 1024 / 1024, 2) AS 'Espacio_Libre_MB',
    ROUND((DATA_FREE / NULLIF(DATA_LENGTH, 0)) * 100, 2) AS 'Porcentaje_Fragmentado',
    UPDATE_TIME AS 'Ultima_Actualizacion'
FROM 
    INFORMATION_SCHEMA.TABLES
WHERE 
    TABLE_SCHEMA = 'u858848268_doctorpez'
    AND TABLE_NAME = 'Ventas_POS';

-- =====================================================
-- PASO 2: VER ÍNDICES ACTUALES DE Ventas_POS
-- =====================================================

SHOW INDEX FROM Ventas_POS;

-- O con más detalle:
SELECT 
    INDEX_NAME AS 'Nombre_Indice',
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX SEPARATOR ', ') AS 'Columnas',
    INDEX_TYPE AS 'Tipo',
    CARDINALITY AS 'Cardinalidad',
    CASE 
        WHEN NON_UNIQUE = 0 THEN 'UNIQUE'
        ELSE 'INDEX'
    END AS 'Tipo_Clave'
FROM 
    INFORMATION_SCHEMA.STATISTICS
WHERE 
    TABLE_SCHEMA = 'u858848268_doctorpez'
    AND TABLE_NAME = 'Ventas_POS'
GROUP BY 
    INDEX_NAME, INDEX_TYPE, NON_UNIQUE, CARDINALITY
ORDER BY 
    INDEX_NAME;

-- =====================================================
-- PASO 3: VER ESTADÍSTICAS DE CONSULTAS (ANTES)
-- =====================================================
-- Analizar una consulta común para ver qué índices usa

EXPLAIN SELECT 
    Folio_Ticket,
    Nombre_Prod,
    Importe + Pagos_tarjeta AS Total_Venta,
    Fecha_venta
FROM Ventas_POS 
WHERE Fecha_venta = CURDATE() 
AND Fk_sucursal = 1
AND Estatus = 'Pagado'
LIMIT 100;

-- =====================================================
-- PASO 4: OPTIMIZAR LA TABLA Ventas_POS
-- =====================================================
-- ⚠️ IMPORTANTE: Esto bloquea la tabla durante la ejecución
-- Ejecutar en horas de bajo tráfico
-- Puede tardar varios minutos dependiendo del tamaño

OPTIMIZE TABLE `Ventas_POS`;

-- =====================================================
-- PASO 5: ACTUALIZAR ESTADÍSTICAS
-- =====================================================
-- Esto ayuda al optimizador a elegir el mejor plan de ejecución

ANALYZE TABLE `Ventas_POS`;

-- =====================================================
-- PASO 6: VER ESTADO DESPUÉS DE LA OPTIMIZACIÓN
-- =====================================================
-- Comparar con el Paso 1 para ver la mejora

SELECT 
    TABLE_NAME AS 'Tabla',
    TABLE_ROWS AS 'Filas_Aproximadas',
    ROUND(DATA_LENGTH / 1024 / 1024, 2) AS 'Tamaño_Datos_MB',
    ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS 'Tamaño_Indices_MB',
    ROUND(DATA_FREE / 1024 / 1024, 2) AS 'Espacio_Libre_MB',
    ROUND((DATA_FREE / NULLIF(DATA_LENGTH, 0)) * 100, 2) AS 'Porcentaje_Fragmentado',
    UPDATE_TIME AS 'Ultima_Actualizacion'
FROM 
    INFORMATION_SCHEMA.TABLES
WHERE 
    TABLE_SCHEMA = 'u858848268_doctorpez'
    AND TABLE_NAME = 'Ventas_POS';

-- =====================================================
-- PASO 7: VERIFICAR QUE LOS ÍNDICES SE USAN CORRECTAMENTE
-- =====================================================
-- Después de optimizar, verificar que las consultas usan índices

EXPLAIN SELECT 
    Folio_Ticket,
    Nombre_Prod,
    Importe + Pagos_tarjeta AS Total_Venta,
    Fecha_venta
FROM Ventas_POS 
WHERE Fecha_venta = CURDATE() 
AND Fk_sucursal = 1
AND Estatus = 'Pagado'
LIMIT 100;

-- Verificar que en la columna "key" aparezca un índice (ej: idx_ventas_fecha_sucursal)
-- Verificar que en "rows" sea un número bajo
-- Verificar que en "type" sea "ref" o "range", NO "ALL"

-- =====================================================
-- PASO 8: PROBAR RENDIMIENTO DE CONSULTAS COMUNES
-- =====================================================

-- Consulta 1: Ventas del día
SET @start_time = NOW(6);
SELECT 
    COUNT(*) AS Total_Ventas,
    SUM(Importe + Pagos_tarjeta) AS Total_Importe
FROM Ventas_POS 
WHERE Fecha_venta = CURDATE() 
AND Estatus = 'Pagado';
SELECT TIMESTAMPDIFF(MICROSECOND, @start_time, NOW(6)) / 1000 AS 'Tiempo_ms';

-- Consulta 2: Ventas por sucursal
SET @start_time = NOW(6);
SELECT 
    Fk_sucursal,
    COUNT(*) AS Total_Ventas,
    SUM(Importe + Pagos_tarjeta) AS Total_Importe
FROM Ventas_POS 
WHERE Fecha_venta BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()
AND Estatus = 'Pagado'
GROUP BY Fk_sucursal;
SELECT TIMESTAMPDIFF(MICROSECOND, @start_time, NOW(6)) / 1000 AS 'Tiempo_ms';

-- Consulta 3: Búsqueda por producto
SET @start_time = NOW(6);
SELECT 
    Nombre_Prod,
    SUM(Cantidad_Venta) AS Total_Vendido,
    SUM(Importe + Pagos_tarjeta) AS Total_Importe
FROM Ventas_POS 
WHERE ID_Prod_POS = 12345
AND Fecha_venta BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()
GROUP BY ID_Prod_POS, Nombre_Prod;
SELECT TIMESTAMPDIFF(MICROSECOND, @start_time, NOW(6)) / 1000 AS 'Tiempo_ms';

-- =====================================================
-- RESUMEN DE MEJORAS ESPERADAS
-- =====================================================
-- Después de OPTIMIZE TABLE y ANALYZE TABLE:
-- 
-- ✅ Espacio fragmentado liberado
-- ✅ Consultas más rápidas (5-20% más rápidas)
-- ✅ Índices funcionan mejor
-- ✅ Estadísticas actualizadas
-- ✅ Mejor uso de memoria
-- 
-- =====================================================
