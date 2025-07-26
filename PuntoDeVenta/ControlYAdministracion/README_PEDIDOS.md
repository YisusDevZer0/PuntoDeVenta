# Sistema de Gestión de Pedidos - Documentación

## 🚀 Características Principales

### ✨ Interfaz Moderna y Dinámica
- **Diseño responsivo** con gradientes y efectos visuales modernos
- **Drag & Drop** para reorganizar productos en pedidos
- **Animaciones suaves** y transiciones elegantes
- **Interfaz intuitiva** con iconos y badges de estado

### 🔍 Búsqueda y Filtros Avanzados
- **Búsqueda en tiempo real** por folio, producto, usuario
- **Filtros múltiples**: estado, fecha, sucursal
- **Búsqueda inteligente** de productos con autocompletado
- **Vista de productos con stock bajo** para resurtido automático

### 📊 Dashboard con Estadísticas
- **Tarjetas de estadísticas** en tiempo real
- **Contadores dinámicos** de pedidos por estado
- **Total estimado** de todos los pedidos
- **Indicadores visuales** de prioridad y estado

### 🛠️ Gestión Completa de Pedidos
- **Crear pedidos** con múltiples productos
- **Editar cantidades** en tiempo real
- **Cambiar estados** con comentarios
- **Eliminar pedidos** pendientes
- **Historial completo** de cambios

### 🔄 Estados de Pedidos
- **Pendiente**: Pedido recién creado
- **Aprobado**: Pedido autorizado para compra
- **En Proceso**: Pedido siendo procesado
- **Completado**: Pedido recibido y finalizado
- **Rechazado**: Pedido cancelado
- **Cancelado**: Pedido eliminado

### 🏪 Gestión Multi-Sucursal
- **Vista por sucursal** o todas las sucursales
- **Filtros por sucursal** específica
- **Estadísticas por sucursal**
- **Control de acceso** por permisos

## 📋 Instalación

### 1. Instalar las Tablas de Base de Datos

Ejecuta el script de instalación:

```bash
# Navegar al directorio de la base de datos
cd PuntoDeVenta/ControlYAdministracion/database/

# Ejecutar el script de instalación
php instalar_pedidos.php
```

O visita en tu navegador:
```
http://tu-dominio.com/PuntoDeVenta/ControlYAdministracion/database/instalar_pedidos.php
```

### 2. Verificar la Instalación

El script verificará que se crearon las siguientes tablas:
- `pedidos` - Tabla principal de pedidos
- `pedido_detalles` - Detalles de productos en pedidos
- `pedido_historial` - Historial de cambios de estado
- `proveedores_pedidos` - Catálogo de proveedores
- `producto_proveedor` - Relación productos-proveedores

### 3. Acceder al Sistema

Navega a:
```
http://tu-dominio.com/PuntoDeVenta/ControlYAdministracion/Pedidos.php
```

## 🎯 Cómo Usar el Sistema

### Crear un Nuevo Pedido

1. **Hacer clic** en "Nuevo Pedido"
2. **Buscar productos** escribiendo nombre, código o clave
3. **Agregar productos** haciendo clic en "Agregar"
4. **Ajustar cantidades** en los campos numéricos
5. **Agregar observaciones** y seleccionar prioridad
6. **Guardar pedido** con el botón correspondiente

### Gestionar Pedidos Existentes

#### Ver Detalles
- **Hacer clic** en el ícono de ojo (👁️)
- **Ver información completa** del pedido
- **Revisar historial** de cambios
- **Ver productos** y cantidades

#### Cambiar Estado
- **Aprobar**: Cambiar a estado "Aprobado"
- **Rechazar**: Cambiar a estado "Rechazado"
- **Procesar**: Cambiar a estado "En Proceso"
- **Completar**: Marcar como recibido

#### Eliminar Pedido
- **Solo pedidos pendientes** pueden eliminarse
- **Confirmación requerida** antes de eliminar
- **Eliminación permanente** del sistema

### Usar Filtros y Búsqueda

#### Búsqueda Rápida
- **Escribir** en el campo de búsqueda
- **Búsqueda automática** en folio, producto, usuario
- **Resultados en tiempo real**

#### Filtros Avanzados
- **Estado**: Filtrar por estado del pedido
- **Fecha**: Rango de fechas de creación
- **Sucursal**: Filtrar por sucursal específica
- **Limpiar filtros**: Restaurar vista completa

### Productos con Stock Bajo

1. **Hacer clic** en "Stock Bajo"
2. **Ver lista** de productos que necesitan resurtido
3. **Agregar automáticamente** al pedido con cantidad necesaria
4. **Crear pedido** con productos prioritarios

## 🔧 Configuración Avanzada

### Personalizar Estados

Edita el archivo `PedidosController.php` para modificar los estados disponibles:

```php
// Estados disponibles en la tabla pedidos
'estado ENUM('pendiente', 'aprobado', 'rechazado', 'en_proceso', 'completado', 'cancelado')'
```

### Configurar Prioridades

Las prioridades disponibles son:
- **Baja**: Verde
- **Normal**: Azul (por defecto)
- **Alta**: Amarillo
- **Urgente**: Rojo

### Personalizar Folios

El sistema genera folios automáticamente con el formato:
```
PED + YYYYMMDD + 0001
```

Ejemplo: `PED202412010001`

## 📊 Reportes y Estadísticas

### Dashboard Principal
- **Pedidos pendientes**: Contador en tiempo real
- **Pedidos aprobados**: Total autorizados
- **Pedidos en proceso**: En compra/transporte
- **Total estimado**: Valor total de todos los pedidos

### Filtros por Fecha
- **Rango personalizable**: Desde-hasta
- **Filtro por mes**: Selección rápida
- **Filtro por año**: Vista anual

## 🔒 Seguridad y Permisos

### Control de Acceso
- **Verificación de sesión** en cada página
- **Validación de usuario** en controladores
- **Filtro por sucursal** según permisos

### Validaciones
- **Datos de entrada**: Sanitización automática
- **Transacciones SQL**: Rollback en errores
- **Prepared Statements**: Prevención de SQL Injection

## 🐛 Solución de Problemas

### Error: "Tablas no encontradas"
```bash
# Ejecutar instalación nuevamente
php database/instalar_pedidos.php
```

### Error: "No se pueden cargar pedidos"
1. **Verificar conexión** a la base de datos
2. **Revisar permisos** de usuario
3. **Verificar tablas** existentes

### Error: "No se pueden buscar productos"
1. **Verificar tabla** `Stock_POS`
2. **Revisar permisos** de lectura
3. **Comprobar datos** de productos

### Performance Lenta
1. **Verificar índices** en tablas
2. **Optimizar consultas** complejas
3. **Revisar configuración** de MySQL

## 📱 Características Móviles

### Responsive Design
- **Adaptable** a tablets y móviles
- **Touch-friendly** para pantallas táctiles
- **Navegación optimizada** para móviles

### Funcionalidades Móviles
- **Búsqueda por voz** (compatible)
- **Gestos táctiles** para drag & drop
- **Zoom automático** en campos de entrada

## 🔄 Actualizaciones Futuras

### Próximas Características
- **Notificaciones push** en tiempo real
- **Integración con proveedores** externos
- **Reportes PDF** automáticos
- **API REST** para integraciones
- **Dashboard avanzado** con gráficos

### Mejoras Planificadas
- **Sistema de alertas** por email
- **Aprobación en cadena** (múltiples niveles)
- **Presupuestos** por sucursal
- **Análisis de tendencias** de compra

## 📞 Soporte

### Contacto
- **Desarrollador**: Sistema de Gestión de Pedidos
- **Versión**: 1.0.0
- **Fecha**: Diciembre 2024

### Logs y Debugging
- **Archivo de logs**: `/logs/pedidos.log`
- **Debug mode**: Activar en `PedidosController.php`
- **Errores**: Revisar consola del navegador

---

**¡El sistema está listo para usar!** 🎉

Sigue las instrucciones de instalación y disfruta de una gestión de pedidos moderna, eficiente y completamente funcional. 