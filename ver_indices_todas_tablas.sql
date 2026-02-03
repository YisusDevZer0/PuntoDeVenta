-- =====================================================
-- COMANDOS PARA VER TODOS LOS ÍNDICES DE LA BASE DE DATOS
-- Base de datos: u858848268_doctorpez
-- =====================================================

USE `u858848268_doctorpez`;

-- =====================================================
-- OPCIÓN 1: Ver todos los índices de todas las tablas
-- =====================================================
SELECT 
    TABLE_NAME AS 'Tabla',
    INDEX_NAME AS 'Nombre_Indice',
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX SEPARATOR ', ') AS 'Columnas',
    INDEX_TYPE AS 'Tipo',
    CASE 
        WHEN NON_UNIQUE = 0 THEN 'UNIQUE'
        ELSE 'INDEX'
    END AS 'Tipo_Clave',
    CARDINALITY AS 'Cardinalidad'
FROM 
    INFORMATION_SCHEMA.STATISTICS
WHERE 
    TABLE_SCHEMA = 'u858848268_doctorpez'
    AND INDEX_NAME != 'PRIMARY'
GROUP BY 
    TABLE_NAME, INDEX_NAME, INDEX_TYPE, NON_UNIQUE, CARDINALITY
ORDER BY 
    TABLE_NAME, INDEX_NAME;

-- =====================================================
-- OPCIÓN 2: Ver índices por tabla específica
-- =====================================================
-- Ejemplo para Ventas_POS:
-- SHOW INDEX FROM Ventas_POS;

-- =====================================================
-- OPCIÓN 3: Ver resumen de índices por tabla
-- =====================================================
SELECT 
    TABLE_NAME AS 'Tabla',
    COUNT(DISTINCT INDEX_NAME) AS 'Total_Indices',
    SUM(CASE WHEN INDEX_TYPE = 'FULLTEXT' THEN 1 ELSE 0 END) AS 'Indices_Fulltext',
    SUM(CASE WHEN NON_UNIQUE = 0 THEN 1 ELSE 0 END) AS 'Indices_Unique',
    SUM(CASE WHEN NON_UNIQUE = 1 THEN 1 ELSE 0 END) AS 'Indices_Normales'
FROM 
    INFORMATION_SCHEMA.STATISTICS
WHERE 
    TABLE_SCHEMA = 'u858848268_doctorpez'
    AND INDEX_NAME != 'PRIMARY'
GROUP BY 
    TABLE_NAME
ORDER BY 
    Total_Indices DESC;

-- =====================================================
-- OPCIÓN 4: Ver índices de tablas principales (más importantes)
-- =====================================================
SELECT 
    TABLE_NAME AS 'Tabla',
    INDEX_NAME AS 'Indice',
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX SEPARATOR ', ') AS 'Columnas',
    INDEX_TYPE AS 'Tipo'
FROM 
    INFORMATION_SCHEMA.STATISTICS
WHERE 
    TABLE_SCHEMA = 'u858848268_doctorpez'
    AND TABLE_NAME IN (
        'Ventas_POS', 
        'Stock_POS', 
        'Productos_POS', 
        'Cajas',
        'Traspasos_generados',
        'ConteosDiarios',
        'Inventario_Turnos',
        'Devoluciones',
        'GastosPOS',
        'pedidos',
        'encargos',
        'CEDIS',
        'Creditos_POS',
        'AbonosCreditosVentas',
        'Solicitudes_Ingresos'
    )
    AND INDEX_NAME != 'PRIMARY'
GROUP BY 
    TABLE_NAME, INDEX_NAME, INDEX_TYPE
ORDER BY 
    TABLE_NAME, INDEX_NAME;

-- =====================================================
-- OPCIÓN 5: Ver tamaño de índices (útil para optimización)
-- =====================================================
SELECT 
    TABLE_NAME AS 'Tabla',
    INDEX_NAME AS 'Indice',
    ROUND(SUM(INDEX_LENGTH) / 1024 / 1024, 2) AS 'Tamaño_MB',
    COUNT(*) AS 'Columnas_en_Indice'
FROM 
    INFORMATION_SCHEMA.STATISTICS
WHERE 
    TABLE_SCHEMA = 'u858848268_doctorpez'
    AND INDEX_NAME != 'PRIMARY'
GROUP BY 
    TABLE_NAME, INDEX_NAME
ORDER BY 
    Tamaño_MB DESC
LIMIT 50;

-- =====================================================
-- OPCIÓN 6: Ver tablas sin índices (excepto PRIMARY)
-- =====================================================
SELECT 
    t.TABLE_NAME AS 'Tabla_Sin_Indices',
    t.TABLE_ROWS AS 'Filas_Aproximadas',
    ROUND(t.DATA_LENGTH / 1024 / 1024, 2) AS 'Tamaño_Datos_MB'
FROM 
    INFORMATION_SCHEMA.TABLES t
LEFT JOIN 
    INFORMATION_SCHEMA.STATISTICS s ON t.TABLE_SCHEMA = s.TABLE_SCHEMA 
    AND t.TABLE_NAME = s.TABLE_NAME 
    AND s.INDEX_NAME != 'PRIMARY'
WHERE 
    t.TABLE_SCHEMA = 'u858848268_doctorpez'
    AND t.TABLE_TYPE = 'BASE TABLE'
    AND s.INDEX_NAME IS NULL
ORDER BY 
    t.TABLE_ROWS DESC;
