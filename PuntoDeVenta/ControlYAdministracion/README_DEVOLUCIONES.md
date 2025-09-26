# Sistema de Devoluciones - Doctor Pez

## 📋 Descripción General

El Sistema de Devoluciones es un módulo completo desarrollado para el sistema de clínicas y farmacias Doctor Pez que permite gestionar de manera eficiente las devoluciones de productos por diversos motivos como productos dañados, caducados, no facturados, etc.

## 🚀 Características Principales

### ✅ Funcionalidades Implementadas

1. **Escaneo Múltiple de Productos**
   - Escaneo por código de barras
   - Búsqueda manual por código
   - Soporte para cámara (preparado para QuaggaJS)
   - Validación de existencias en tiempo real

2. **Tipos de Devolución Configurables**
   - Producto no facturado
   - Producto dañado al recibir
   - Próximo a caducar
   - Producto caducado
   - Producto dañado/roto
   - Solicitado por administración
   - Error en etiquetado
   - Producto defectuoso
   - Sobrante de inventario
   - Otro motivo

3. **Gestión de Autorizaciones**
   - Tipos que requieren autorización administrativa
   - Flujo de aprobación/rechazo
   - Seguimiento de autorizaciones

4. **Sistema de Reportes Completo**
   - Reporte general de devoluciones
   - Reporte detallado por producto
   - Estadísticas generales
   - Análisis por tipos de devolución
   - Gráficos interactivos
   - Exportación a Excel (preparado)

5. **Integración con Inventario**
   - Actualización automática de stock
   - Registro en logs de movimientos
   - Control de lotes y caducidades
   - Trazabilidad completa

6. **Interfaz Moderna y Responsive**
   - Diseño Bootstrap 5
   - Compatible con dispositivos móviles
   - Interfaz intuitiva y fácil de usar
   - Notificaciones en tiempo real

## 📁 Estructura de Archivos

```
PuntoDeVenta/ControlYAdministracion/
├── Devoluciones.php                    # Módulo principal de devoluciones
├── ReportesDevoluciones.php           # Módulo de reportes
├── instalar_devoluciones.php          # Script de instalación
├── api/
│   └── devoluciones_api.php           # API REST para devoluciones
├── js/
│   └── devoluciones_scanner.js        # JavaScript para escaneo
├── sql/
│   ├── devoluciones_tables.sql        # Estructura de tablas (original)
│   ├── devoluciones_tables_fixed.sql  # Versión corregida
│   └── devoluciones_final.sql         # Versión final compatible
└── README_DEVOLUCIONES.md             # Esta documentación
```

## 🗄️ Estructura de Base de Datos

### Tablas Principales

1. **`Devoluciones`** - Registro principal de devoluciones
   - `id`, `folio`, `sucursal_id`, `usuario_id`
   - `fecha`, `estatus`, `observaciones_generales`
   - `total_productos`, `total_unidades`, `valor_total`

2. **`Devoluciones_Detalle`** - Detalles de productos devueltos
   - `devolucion_id`, `producto_id`, `codigo_barras`
   - `nombre_producto`, `cantidad`, `tipo_devolucion`
   - `lote`, `fecha_caducidad`, `precio_venta`, `valor_total`

3. **`Tipos_Devolucion`** - Configuración de tipos
   - `codigo`, `nombre`, `descripcion`, `color`
   - `requiere_autorizacion`, `activo`

4. **`Devoluciones_Autorizaciones`** - Control de autorizaciones
   - `devolucion_id`, `usuario_autoriza`, `estatus`
   - `fecha_autorizacion`, `observaciones`

5. **`Devoluciones_Acciones`** - Acciones tomadas
   - `devolucion_id`, `tipo_accion`, `descripcion`
   - `usuario_ejecuta`, `fecha_ejecucion`, `estatus`

### Vistas Creadas

- **`v_devoluciones_completas`** - Vista completa con joins
- **`v_devoluciones_detalle_completo`** - Detalles con información completa
- **`v_estadisticas_devoluciones`** - Estadísticas por fecha y sucursal
- **`v_productos_mas_devueltos`** - Productos con más devoluciones

## 🔧 Instalación

### Requisitos Previos

1. **Base de Datos MySQL/MariaDB** con las siguientes tablas existentes:
   - `Sucursales` (con campos: `ID_Sucursal`, `Nombre_Sucursal`)
   - `Usuarios_PV` (con campos: `Id_PvUser`, `Nombre_Apellidos`)
   - `Stock_POS` (inventario principal)
   - `Ventas_POSV2` (registro de ventas)

2. **PHP 7.2+** con extensiones:
   - mysqli
   - json
   - session

3. **Permisos de usuario administrador** en el sistema

### Pasos de Instalación

1. **Ejecutar el instalador**
   ```
   http://tu-dominio.com/PuntoDeVenta/ControlYAdministracion/instalar_devoluciones.php
   ```

2. **Verificar la instalación**
   - El instalador mostrará el progreso y resultados
   - Verificará que todas las tablas se crearon correctamente
   - Validará la existencia de archivos necesarios

3. **Configurar permisos**
   - Asegurar que el usuario web tenga permisos de escritura en uploads/
   - Verificar permisos de ejecución en archivos PHP

## 🎯 Uso del Sistema

### Acceso al Módulo

1. **Menú Principal**: Farmacia > Devoluciones
2. **URL Directa**: `/ControlYAdministracion/Devoluciones.php`

### Proceso de Devolución

1. **Crear Nueva Devolución**
   - Hacer clic en "Nueva Devolución"
   - Activar el escáner de productos

2. **Agregar Productos**
   - Escanear código de barras o ingresarlo manualmente
   - Seleccionar tipo de devolución
   - Especificar cantidad y observaciones
   - Confirmar agregado del producto

3. **Procesar Devolución**
   - Revisar lista de productos
   - Agregar observaciones generales
   - Procesar la devolución

4. **Autorización (si aplica)**
   - Algunos tipos requieren autorización administrativa
   - El administrador puede aprobar/rechazar desde el listado

### Generación de Reportes

1. **Acceder a Reportes**
   - Menú: Farmacia > Devoluciones > Reportes
   - O usar el botón "Reportes" en el módulo principal

2. **Configurar Filtros**
   - Seleccionar rango de fechas
   - Filtrar por sucursal
   - Elegir tipo de reporte

3. **Generar y Exportar**
   - Visualizar datos en pantalla
   - Exportar a Excel (funcionalidad preparada)

## 🔄 Integración con Otros Módulos

### Sistema de Inventarios

- **Actualización automática** de existencias en `Stock_POS`
- **Registro en logs** de movimientos de inventario
- **Control de lotes** y fechas de caducidad
- **Trazabilidad completa** de movimientos

### Sistema de Ventas

- **Consulta de ventas recientes** del producto
- **Validación de existencias** antes de procesar
- **Historial de transacciones** relacionadas

### Sistema de Usuarios

- **Control de permisos** por tipo de usuario
- **Registro de acciones** por usuario
- **Flujo de autorizaciones** administrativas

## 📊 Tipos de Reportes Disponibles

### 1. Reporte General
- Lista de todas las devoluciones
- Información básica: folio, fecha, usuario, totales
- Filtros por fecha y sucursal

### 2. Reporte Detallado
- Desglose por producto devuelto
- Información completa: lotes, caducidades, precios
- Observaciones específicas por producto

### 3. Estadísticas
- Totales generales y promedios
- Distribución por estatus
- Métricas de rendimiento

### 4. Análisis por Tipos
- Distribución de devoluciones por tipo
- Gráficos circulares interactivos
- Análisis de tendencias

## ⚙️ Configuración Avanzada

### Tipos de Devolución Personalizados

```sql
INSERT INTO Tipos_Devolucion (codigo, nombre, descripcion, color, requiere_autorizacion) 
VALUES ('custom_type', 'Tipo Personalizado', 'Descripción del tipo', '#ff6b6b', 1);
```

### Modificar Colores y Estilos

Los colores de los tipos se pueden modificar directamente en la tabla `Tipos_Devolucion` campo `color`.

### Configurar Autorizaciones

Modificar el campo `requiere_autorizacion` en la tabla `Tipos_Devolucion` para cambiar qué tipos requieren aprobación administrativa.

## 🔒 Seguridad

### Validaciones Implementadas

- **Autenticación obligatoria** para acceder al módulo
- **Validación de permisos** por tipo de usuario
- **Sanitización de datos** de entrada
- **Prevención de SQL injection** con prepared statements
- **Validación de existencias** antes de procesar

### Logs y Auditoría

- **Registro completo** de todas las acciones
- **Trazabilidad** de cambios en inventario
- **Historial de autorizaciones** y rechazos
- **Logs de errores** y excepciones

## 🐛 Solución de Problemas

### Problemas Comunes

1. **Error: Tabla no existe**
   - Ejecutar nuevamente el instalador
   - Verificar permisos de base de datos

2. **Producto no encontrado**
   - Verificar que el código de barras sea correcto
   - Confirmar que el producto tenga existencias

3. **Error de permisos**
   - Verificar que el usuario tenga permisos de administrador
   - Revisar permisos de archivos y directorios

### Logs de Debug

Los errores se registran en:
- Logs de Apache/Nginx
- Base de datos en tabla `Stock_POS_Log`
- Consola del navegador (JavaScript)

## 📞 Soporte y Mantenimiento

### Respaldos Recomendados

1. **Base de datos**: Respaldar diariamente las tablas de devoluciones
2. **Archivos**: Mantener copia de archivos PHP y JavaScript
3. **Configuración**: Documentar cambios personalizados

### Actualizaciones

Para actualizar el sistema:
1. Respaldar base de datos y archivos
2. Reemplazar archivos PHP y JavaScript
3. Ejecutar scripts de actualización si aplican
4. Verificar funcionamiento

### Contacto

Para soporte técnico o consultas:
- **Desarrollador**: DevZero
- **Sistema**: Doctor Pez
- **Versión**: 1.0.0
- **Fecha**: Septiembre 2025

---

## 📝 Notas de Versión

### Versión 1.0.0 (Septiembre 2025)
- ✅ Implementación inicial completa
- ✅ Sistema de escaneo múltiple
- ✅ 10 tipos de devolución predefinidos
- ✅ Sistema de reportes con gráficos
- ✅ Integración con inventarios
- ✅ Interfaz responsive y moderna
- ✅ Sistema de autorizaciones
- ✅ Documentación completa

### Próximas Versiones Planificadas
- 📱 App móvil para escaneo
- 📧 Notificaciones por email
- 📊 Dashboards avanzados
- 🔄 Integración con proveedores
- 📦 Gestión de devoluciones a proveedores

---

*Este sistema fue desarrollado específicamente para Doctor Pez, adaptándose a su estructura de base de datos y flujos de trabajo existentes.*
