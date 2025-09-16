# Instrucciones para Habilitar Asignación de Bitácoras a Sucursales

## ⚠️ IMPORTANTE: Actualización de Base de Datos Requerida

Para que las bitácoras se puedan asignar correctamente a sucursales específicas, es necesario actualizar la estructura de la base de datos.

## 🔧 Pasos para Actualizar la Base de Datos

### Opción 1: Script Automático (Recomendado)
1. Accede a: `http://tu-dominio/PuntoDeVenta/ControlYAdministracion/actualizar_bitacora_sucursal.php`
2. El script verificará y actualizará automáticamente la base de datos
3. Verás un reporte detallado de los cambios realizados

### Opción 2: Script SQL Manual
1. Ejecuta el archivo SQL: `PuntoDeVenta/ControlYAdministracion/sql/agregar_sucursal_id_bitacora.sql`
2. En tu panel de administración de MySQL (phpMyAdmin, MySQL Workbench, etc.)
3. Importa y ejecuta el script

## 📋 Cambios que se Realizarán

### Campos Agregados a la Tabla `Bitacora_Limpieza`:
- `sucursal_id` (int) - ID de la sucursal asignada
- `created_at` (timestamp) - Fecha de creación
- `updated_at` (timestamp) - Fecha de última actualización

### Índices y Relaciones:
- Índice en `sucursal_id` para mejorar rendimiento
- Clave foránea hacia la tabla `Sucursales`

### Datos Existentes:
- Las bitácoras existentes se asignarán a la primera sucursal activa disponible

## ✅ Verificación Post-Actualización

Después de ejecutar la actualización:

1. **Verifica la estructura de la tabla:**
   ```sql
   DESCRIBE Bitacora_Limpieza;
   ```

2. **Verifica que las bitácoras existentes tengan sucursal asignada:**
   ```sql
   SELECT id_bitacora, area, sucursal_id FROM Bitacora_Limpieza;
   ```

3. **Prueba crear una nueva bitácora:**
   - Ve a `BitacoraLimpieza.php`
   - Haz clic en "Nueva Bitácora"
   - Selecciona una sucursal
   - Completa el formulario
   - Verifica que se guarde correctamente

## 🚨 Si No Actualizas la Base de Datos

**Sin la actualización:**
- ❌ Las bitácoras NO se asignarán a sucursales específicas
- ❌ El filtro por sucursal no funcionará
- ❌ La columna de sucursal mostrará "N/A - Actualizar BD"
- ✅ El sistema seguirá funcionando para crear bitácoras básicas

**Con la actualización:**
- ✅ Las bitácoras se asignan correctamente a sucursales
- ✅ El filtro por sucursal funciona perfectamente
- ✅ La columna de sucursal muestra el nombre real de la sucursal
- ✅ Todas las funcionalidades están disponibles

## 🔄 Rollback (Si es Necesario)

Si necesitas revertir los cambios:
```sql
-- Eliminar clave foránea
ALTER TABLE Bitacora_Limpieza DROP FOREIGN KEY fk_bitacora_sucursal;

-- Eliminar índice
ALTER TABLE Bitacora_Limpieza DROP INDEX idx_sucursal_id;

-- Eliminar campos
ALTER TABLE Bitacora_Limpieza DROP COLUMN sucursal_id;
ALTER TABLE Bitacora_Limpieza DROP COLUMN created_at;
ALTER TABLE Bitacora_Limpieza DROP COLUMN updated_at;
```

## 📞 Soporte

Si encuentras algún problema durante la actualización, verifica:
1. Que tienes permisos de administrador en la base de datos
2. Que la tabla `Sucursales` existe y tiene datos
3. Que no hay restricciones de integridad referencial

**¡Una vez actualizada la base de datos, el sistema funcionará completamente con asignación de bitácoras a sucursales!**
