# Instalación de Módulos: Gestión de Lotes y Inventario por Turnos

## Descripción

Se han creado dos nuevos módulos para el sistema de punto de venta:

1. **Gestión de Lotes y Caducidades**: Permite actualizar lotes y fechas de caducidad con descuento automático mediante triggers.
2. **Inventario por Turnos Diarios**: Sistema de inventario por turnos donde los productos seleccionados por un usuario quedan bloqueados para otros hasta completarse.

## Instalación

### Paso 1: Ejecutar Scripts SQL

Ejecutar los siguientes scripts SQL en la base de datos en este orden:

1. **gestion_lotes_caducidades.sql**
   ```sql
   -- Ubicación: database/gestion_lotes_caducidades.sql
   -- Crea tablas y triggers para gestión de lotes
   ```

2. **inventario_turnos.sql**
   ```sql
   -- Ubicación: database/inventario_turnos.sql
   -- Crea tablas y procedimientos para inventario por turnos
   ```

3. **inventario_turnos_lote_caducidad.sql** (recomendado para farmacias)
   ```sql
   -- Ubicación: database/inventario_turnos_lote_caducidad.sql
   -- Agrega columnas Lote y Fecha_Caducidad a Inventario_Turnos_Productos
   -- para que el modal "Registrar conteo físico" guarde lote y fecha de caducidad.
   -- Si no ejecutas este script, el conteo seguirá funcionando pero sin lote/caducidad.
   ```

### Paso 2: Verificar Archivos Creados

Los siguientes archivos han sido creados:

#### Módulo de Lotes y Caducidades:
- `GestionLotesCaducidades.php` - Página principal
- `Controladores/DataLotesCaducidades.php` - Controlador de datos
- `api/actualizar_lote_caducidad.php` - API para actualizar lotes
- `api/buscar_producto.php` - API para buscar productos
- `js/GestionLotesCaducidades.js` - JavaScript del módulo
- `Modales/ActualizarLoteCaducidad.php` - Modal de actualización

#### Módulo de Inventario por Turnos:
- `InventarioTurnos.php` - Página principal
- `Controladores/DataInventarioTurnos.php` - Controlador de datos
- `api/gestion_turnos.php` - API para gestión de turnos
- `js/InventarioTurnos.js` - JavaScript del módulo

### Paso 3: Verificar Menú

Los enlaces han sido agregados al menú en `Menu.php` dentro de la sección "Almacen":
- Gestión de Lotes y Caducidades
- Inventario por Turnos

## Funcionalidades

### Módulo de Gestión de Lotes y Caducidades

1. **Actualizar Lotes**: Permite actualizar o crear nuevos lotes con sus fechas de caducidad
2. **Filtros**: 
   - Por código de barras
   - Por sucursal
   - Por estado (próximos a vencer, vencidos, vigentes)
3. **Trigger Automático**: Al realizar una venta, se descuenta automáticamente del lote más próximo a vencer
4. **Historial**: Registra todos los movimientos de lotes

### Módulo de Inventario por Turnos

1. **Iniciar Turno**: Cada usuario puede iniciar un turno diario de inventario
2. **Pausar/Reanudar**: Los turnos se pueden pausar y reanudar, guardando el estado
3. **Bloqueo de Productos**: 
   - Cuando un usuario selecciona un producto, queda bloqueado para otros usuarios
   - El producto se libera cuando se completa el conteo o se agota el stock
4. **Conteo Físico**: Registra existencias físicas y calcula diferencias automáticamente
5. **Progreso**: Muestra el progreso del turno (productos completados vs total)

## Uso

### Gestión de Lotes y Caducidades

1. Acceder al menú: **Almacen > Gestión de Lotes y Caducidades**
2. Usar filtros para encontrar productos
3. Click en "Actualizar Lote" para crear/editar lotes
4. El sistema automáticamente descuenta de los lotes al vender

### Inventario por Turnos

1. Acceder al menú: **Almacen > Inventario por Turnos**
2. Click en "Iniciar Turno" para comenzar
3. Buscar productos y click en "Seleccionar" para agregarlos al turno
4. Click en "Contar" para registrar existencias físicas
5. Pausar si es necesario (los datos se guardan)
6. Finalizar cuando termine el turno

## Estructura de Base de Datos

### Tablas Creadas:

**Módulo de Lotes:**
- `Gestion_Lotes_Movimientos` - Historial de movimientos
- `Lotes_Descuentos_Ventas` - Registro de descuentos automáticos

**Módulo de Turnos:**
- `Inventario_Turnos` - Turnos de inventario
- `Inventario_Turnos_Productos` - Productos en cada turno
- `Inventario_Turnos_Historial` - Historial de acciones
- `Inventario_Productos_Bloqueados` - Productos bloqueados

### Triggers Creados:

1. `trg_descontar_lote_venta` - Descuenta automáticamente de lotes al vender
2. `trg_bloquear_producto_inventario` - Bloquea productos al seleccionarlos
3. `trg_liberar_producto_inventario` - Libera productos al completarlos
4. `trg_historial_turnos` - Registra historial de acciones

### Procedimientos:

- `sp_generar_folio_turno` - Genera folios únicos para turnos

## Notas Importantes

1. **Permisos**: Asegúrate de que los usuarios tengan permisos adecuados
2. **Backup**: Realiza backup antes de ejecutar los scripts SQL
3. **Pruebas**: Prueba en un ambiente de desarrollo primero
4. **Rendimiento**: Los triggers pueden afectar el rendimiento en ventas masivas

## Solución de Problemas

### Error al ejecutar triggers:
- Verificar que las tablas `Ventas_POS_Detalle` y `Historial_Lotes` existan
- Verificar permisos del usuario de base de datos

### Productos no se bloquean:
- Verificar que el trigger `trg_bloquear_producto_inventario` esté activo
- Revisar la tabla `Inventario_Productos_Bloqueados`

### Lotes no se descuentan:
- Verificar que el trigger `trg_descontar_lote_venta` esté activo
- Revisar la tabla `Lotes_Descuentos_Ventas` para ver si se registran los descuentos

### Lote y Fecha de caducidad no se guardan en "Registrar conteo físico":
- Ejecutar el script **database/inventario_turnos_lote_caducidad.sql** en tu base de datos.
- Ese script agrega las columnas `Lote` y `Fecha_Caducidad` a la tabla `Inventario_Turnos_Productos`.
- Sin ejecutarlo, el modal funciona pero solo guarda existencias físicas y diferencia.

## Soporte

Para problemas o dudas, revisar:
- Logs de errores de PHP
- Logs de MySQL
- Tablas de auditoría creadas
