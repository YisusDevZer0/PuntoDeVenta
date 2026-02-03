# Guía Completa de Optimización de Base de Datos

## 📊 Ver Todos los Índices

### Comando Principal (Ver TODOS los índices)

```sql
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
```

### Ver Índices de una Tabla Específica

```sql
-- Ver índices de Ventas_POS
SHOW INDEX FROM Ventas_POS;

-- Ver índices de Stock_POS
SHOW INDEX FROM Stock_POS;

-- Ver índices de Productos_POS
SHOW INDEX FROM Productos_POS;
```

### Ver Resumen de Índices por Tabla

```sql
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
```

### Ver Tamaño de los Índices

```sql
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
```

---

## 🚀 Optimizaciones Adicionales para Máxima Velocidad

### 1. **Optimizar Tablas (Reconstruir)**

Ejecuta periódicamente (mensual o cuando notes lentitud):

```sql
OPTIMIZE TABLE Ventas_POS;
OPTIMIZE TABLE Stock_POS;
OPTIMIZE TABLE Productos_POS;
-- ... etc
```

**Beneficios:**
- Reorganiza datos físicos
- Reduce fragmentación
- Mejora el rendimiento de lectura

### 2. **Actualizar Estadísticas**

Ejecuta después de cambios grandes en los datos:

```sql
ANALYZE TABLE Ventas_POS;
ANALYZE TABLE Stock_POS;
-- ... etc
```

**Beneficios:**
- Ayuda al optimizador a elegir el mejor plan de ejecución
- Mejora la selección de índices

### 3. **Configurar Variables de Sesión**

Para consultas complejas, ejecuta al inicio de la sesión:

```sql
SET SESSION join_buffer_size = 262144;
SET SESSION sort_buffer_size = 262144;
SET SESSION read_buffer_size = 131072;
SET SESSION tmp_table_size = 67108864;
SET SESSION max_heap_table_size = 67108864;
```

### 4. **Configurar Variables Globales del Servidor**

**⚠️ IMPORTANTE:** Requiere privilegios de administrador y reinicio del servidor MySQL.

Edita el archivo `my.cnf` o `my.ini`:

```ini
[mysqld]
# Buffer Pool (ajustar según RAM disponible)
# Recomendado: 70-80% de RAM disponible
innodb_buffer_pool_size = 1G

# Log File Size
innodb_log_file_size = 256M

# Flush Method
innodb_flush_method = O_DIRECT

# Query Cache (solo MySQL 5.7 o MariaDB)
query_cache_size = 64M
query_cache_type = 1

# Otros ajustes
innodb_flush_log_at_trx_commit = 2
innodb_file_per_table = 1
```

### 5. **Limpiar Datos Antiguos**

Considera archivar o eliminar datos muy antiguos:

```sql
-- Ejemplo: Archivar ventas de hace más de 2 años
CREATE TABLE Ventas_POS_Archivo LIKE Ventas_POS;
INSERT INTO Ventas_POS_Archivo 
SELECT * FROM Ventas_POS 
WHERE Fecha_venta < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);

DELETE FROM Ventas_POS 
WHERE Fecha_venta < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);
```

### 6. **Particionado de Tablas (Para Tablas Muy Grandes)**

Solo para tablas con millones de registros:

```sql
-- Particionar Ventas_POS por año
ALTER TABLE Ventas_POS
PARTITION BY RANGE (YEAR(Fecha_venta)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_futuro VALUES LESS THAN MAXVALUE
);
```

**Beneficios:**
- Consultas más rápidas (solo busca en particiones relevantes)
- Mantenimiento más fácil
- Eliminación de datos antiguos más rápida

### 7. **Usar EXPLAIN para Analizar Consultas Lentas**

```sql
EXPLAIN SELECT * FROM Ventas_POS 
WHERE Fecha_venta = CURDATE() 
AND Fk_sucursal = 1;
```

**Qué buscar:**
- `key`: Debe mostrar el índice usado
- `rows`: Debe ser bajo
- `type`: Debe ser `ref` o `range`, no `ALL`

### 8. **Monitorear Consultas Lentas**

Habilita el log de consultas lentas:

```sql
-- Ver consultas que tardan más de 2 segundos
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
SET GLOBAL slow_query_log_file = '/var/log/mysql/slow.log';
```

### 9. **Optimizar Consultas en el Código PHP**

**Mejores Prácticas:**

```php
// ✅ CORRECTO - Usa índices
$sql = "SELECT * FROM Ventas_POS WHERE Fecha_venta = ? AND Fk_sucursal = ?";

// ❌ INCORRECTO - No puede usar índices eficientemente
$sql = "SELECT * FROM Ventas_POS WHERE DATE(Fecha_venta) = CURDATE()";

// ✅ CORRECTO - Limita resultados
$sql = "SELECT * FROM Ventas_POS WHERE ... LIMIT 100";

// ❌ INCORRECTO - Puede traer miles de registros
$sql = "SELECT * FROM Ventas_POS WHERE ...";

// ✅ CORRECTO - Solo columnas necesarias
$sql = "SELECT Folio_Ticket, Nombre_Prod, Importe FROM Ventas_POS WHERE ...";

// ❌ INCORRECTO - Trae todas las columnas innecesariamente
$sql = "SELECT * FROM Ventas_POS WHERE ...";
```

### 10. **Usar Caché en la Aplicación**

Para datos que no cambian frecuentemente:

```php
// Ejemplo con caché de productos
$cache_key = 'productos_lista_' . $sucursal_id;
$productos = apcu_fetch($cache_key);

if ($productos === false) {
    $sql = "SELECT * FROM Productos_POS WHERE ...";
    $productos = mysqli_query($conn, $sql)->fetch_all(MYSQLI_ASSOC);
    apcu_store($cache_key, $productos, 3600); // Cache por 1 hora
}
```

---

## 📋 Checklist de Optimización Completa

### ✅ Ya Completado
- [x] Crear índices en todas las tablas
- [x] Validar que los índices se crearon correctamente

### 🔄 Mantenimiento Periódico (Mensual)
- [ ] Ejecutar `OPTIMIZE TABLE` en tablas principales
- [ ] Ejecutar `ANALYZE TABLE` en tablas principales
- [ ] Revisar consultas lentas con `EXPLAIN`
- [ ] Limpiar datos antiguos si es necesario

### ⚙️ Configuración del Servidor (Una vez)
- [ ] Ajustar `innodb_buffer_pool_size` según RAM
- [ ] Configurar `innodb_log_file_size`
- [ ] Habilitar query cache (si aplica)
- [ ] Configurar log de consultas lentas

### 💻 Optimización en Código PHP
- [ ] Usar prepared statements (ya lo haces)
- [ ] Evitar funciones en WHERE (DATE(), YEAR(), etc.)
- [ ] Limitar resultados con LIMIT
- [ ] Seleccionar solo columnas necesarias
- [ ] Implementar caché para datos frecuentes

---

## 🎯 Resultados Esperados

Después de aplicar todas las optimizaciones:

- **Consultas simples:** 10-50x más rápidas
- **Consultas complejas:** 5-20x más rápidas
- **Reportes:** 3-10x más rápidos
- **Búsquedas:** 5-15x más rápidas
- **JOINs:** 3-8x más rápidos

---

## 📝 Archivos Creados

1. **`ver_indices_todas_tablas.sql`** - Comandos para ver todos los índices
2. **`optimizaciones_adicionales.sql`** - Optimizaciones complementarias
3. **`optimizacion_indices.sql`** - Script principal con validación

---

## 🚨 Importante

- Las optimizaciones de servidor (`my.cnf`) requieren reinicio de MySQL
- `OPTIMIZE TABLE` puede tardar mucho en tablas grandes - ejecutar en horas de bajo tráfico
- Hacer backup antes de cambios importantes
- Monitorear el rendimiento después de cada cambio
