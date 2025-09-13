# Módulo de Tareas por Hacer

## Descripción
Sistema completo de gestión de tareas para el punto de venta de farmacias. Permite crear, asignar, gestionar y hacer seguimiento de tareas entre usuarios del sistema.

## Características Principales

### ✅ Gestión de Tareas
- **Crear tareas**: Título, descripción, prioridad, fecha límite, estado y asignación
- **Editar tareas**: Modificar todos los campos de una tarea existente
- **Eliminar tareas**: Borrar tareas (solo creador o administrador)
- **Cambiar estado**: Progreso de tareas (Por hacer → En progreso → Completada)

### ✅ Sistema de Prioridades
- **Alta**: Tareas urgentes (color rojo)
- **Media**: Tareas normales (color amarillo) 
- **Baja**: Tareas de baja prioridad (color verde)

### ✅ Estados de Tareas
- **Por hacer**: Tarea pendiente de iniciar
- **En progreso**: Tarea en desarrollo
- **Completada**: Tarea finalizada
- **Cancelada**: Tarea cancelada

### ✅ Filtros y Búsqueda
- Filtrar por estado, prioridad y usuario asignado
- Búsqueda en tiempo real
- Ordenamiento por prioridad y fecha límite

### ✅ Estadísticas
- Contador de tareas por estado
- Tareas próximas a vencer
- Dashboard visual con tarjetas de estadísticas

### ✅ Exportación
- Exportar tareas a Excel (.xls)
- Incluye todos los filtros aplicados
- Formato profesional con colores

### ✅ Permisos y Seguridad
- Los usuarios solo ven sus tareas asignadas o creadas
- Los administradores ven todas las tareas
- Validación de permisos en todas las operaciones

## Archivos del Módulo

### Archivos Principales
- `TareasPorHacer.php` - Interfaz principal del módulo
- `Controladores/TareasController.php` - Lógica de negocio
- `Controladores/ArrayTareas.php` - API REST para AJAX
- `Controladores/exportar_tareas.php` - Exportación a Excel
- `test_tareas.php` - Archivo de pruebas

### Base de Datos
- Tabla `Tareas` - Almacena todas las tareas del sistema

## Instalación

### 1. Ejecutar Scripts SQL
```sql
-- Crear la tabla Tareas
CREATE TABLE `Tareas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `prioridad` enum('Alta','Media','Baja') NOT NULL DEFAULT 'Media',
  `fecha_limite` date DEFAULT NULL,
  `estado` enum('Por hacer','En progreso','Completada','Cancelada') NOT NULL DEFAULT 'Por hacer',
  `asignado_a` varchar(50) NOT NULL,
  `creado_por` varchar(50) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_asignado_a` (`asignado_a`),
  KEY `idx_creado_por` (`creado_por`),
  KEY `idx_estado` (`estado`),
  KEY `idx_prioridad` (`prioridad`),
  KEY `idx_fecha_limite` (`fecha_limite`),
  CONSTRAINT `fk_tareas_asignado` FOREIGN KEY (`asignado_a`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tareas_creador` FOREIGN KEY (`creado_por`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Verificar Instalación
Acceder a `test_tareas.php` para verificar que todo funciona correctamente.

## Uso del Módulo

### Crear Nueva Tarea
1. Hacer clic en "Nueva Tarea"
2. Completar todos los campos obligatorios
3. Seleccionar usuario asignado
4. Guardar

### Gestionar Tareas
- **Editar**: Hacer clic en el botón de editar (lápiz)
- **Cambiar Estado**: Usar los botones de estado (play, check, etc.)
- **Eliminar**: Hacer clic en el botón de eliminar (basura)

### Filtrar Tareas
1. Usar los filtros en la parte superior
2. Seleccionar criterios deseados
3. Hacer clic en "Aplicar Filtros"

### Exportar Tareas
1. Aplicar filtros si es necesario
2. Hacer clic en "Exportar"
3. Se descargará un archivo Excel

## API Endpoints

### ArrayTareas.php
- `POST` - Listar tareas (por defecto)
- `POST` con `accion=crear` - Crear tarea
- `POST` con `accion=obtener` - Obtener tarea específica
- `POST` con `accion=actualizar` - Actualizar tarea
- `POST` con `accion=cambiar_estado` - Cambiar estado
- `POST` con `accion=eliminar` - Eliminar tarea
- `POST` con `accion=estadisticas` - Obtener estadísticas
- `POST` con `accion=usuarios` - Listar usuarios disponibles

## Dependencias

### Frontend
- jQuery 3.x
- DataTables 1.10.x
- Bootstrap 4.x
- Font Awesome 5.x
- SweetAlert2

### Backend
- PHP 7.2+
- MySQL 5.7+ / MariaDB 10.2+
- Extensión mysqli

## Notas Técnicas

### Seguridad
- Todas las consultas usan prepared statements
- Validación de permisos en cada operación
- Sanitización de datos de entrada

### Rendimiento
- Índices optimizados en la base de datos
- Consultas eficientes con JOINs
- Paginación en DataTables

### Compatibilidad
- Compatible con el sistema de usuarios existente
- Integrado con el sistema de permisos
- Responsive design para móviles

## Solución de Problemas

### Error: "Tabla Tareas no existe"
- Ejecutar el script SQL de creación de tabla

### Error: "No tienes permisos"
- Verificar que el usuario tenga permisos de administrador o sea el creador/asignado

### Error: "Error al cargar tareas"
- Verificar conexión a base de datos
- Revisar logs de PHP para errores específicos

### DataTables no carga
- Verificar que jQuery y DataTables estén cargados
- Revisar la consola del navegador para errores JavaScript

## Mantenimiento

### Limpieza de Datos
- Las tareas completadas se pueden mantener para historial
- Considerar archivar tareas muy antiguas
- Revisar tareas vencidas regularmente

### Backup
- Incluir tabla `Tareas` en backups regulares
- Exportar tareas importantes antes de actualizaciones

## Versión
- **Versión**: 1.0.0
- **Fecha**: Enero 2024
- **Autor**: Sistema Punto de Venta Farmacias
