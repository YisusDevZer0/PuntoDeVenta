# Módulo de Bitácora de Limpieza

## Descripción
Este módulo proporciona un sistema completo para el control y seguimiento de actividades de limpieza en farmacias, incluyendo bitácoras semanales, elementos de limpieza específicos y sistema de recordatorios.

## Características Principales

### 1. Gestión de Bitácoras
- **Crear nuevas bitácoras** con información de área, semana, fechas y responsables
- **Visualizar bitácoras existentes** en tabla con paginación y búsqueda
- **Editar y eliminar bitácoras** con confirmación de seguridad
- **Asignar responsables** (responsable principal, supervisor, auxiliar)

### 2. Control de Elementos de Limpieza
- **Agregar elementos** específicos de limpieza a cada bitácora
- **Seguimiento diario** con checkboxes para mañana y tarde de cada día de la semana
- **Actualización en tiempo real** del estado de cada elemento
- **Eliminación de elementos** no deseados

### 3. Sistema de Recordatorios
- **Crear recordatorios** con título, descripción, fecha y prioridad
- **Gestión de estados** (activo, completado, cancelado)
- **Visualización por prioridad** con códigos de color
- **Notificaciones** para recordatorios próximos

## Estructura de Archivos

### Controladores
- `BitacoraLimpiezaController.php` - Lógica de negocio para bitácoras
- `RecordatoriosController.php` - Lógica de negocio para recordatorios

### APIs
- `api/crear_bitacora.php` - Crear nuevas bitácoras
- `api/obtener_bitacoras.php` - Obtener lista de bitácoras
- `api/obtener_detalles_limpieza.php` - Obtener elementos de una bitácora
- `api/actualizar_estado_limpieza.php` - Actualizar estado de elementos
- `api/agregar_elemento.php` - Agregar elementos de limpieza
- `api/eliminar_bitacora.php` - Eliminar bitácoras
- `api/crear_recordatorio.php` - Crear recordatorios
- `api/obtener_recordatorios.php` - Obtener recordatorios
- `api/actualizar_recordatorio.php` - Actualizar estado de recordatorios

### Modales
- `Modales/NuevaBitacora.php` - Formulario para crear bitácoras
- `Modales/AgregarElemento.php` - Formulario para agregar elementos
- `Modales/RecordatoriosLimpieza.php` - Gestión de recordatorios

### Archivo Principal
- `BitacoraLimpieza.php` - Interfaz principal del módulo

## Base de Datos

### Tabla: Bitacora_Limpieza
```sql
CREATE TABLE `Bitacora_Limpieza` (
  `id_bitacora` int(11) NOT NULL,
  `area` varchar(100) NOT NULL,
  `semana` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `responsable` varchar(100) NOT NULL,
  `supervisor` varchar(100) NOT NULL,
  `aux_res` varchar(100) NOT NULL,
  `firma_responsable` varchar(255) DEFAULT NULL,
  `firma_supervisor` varchar(255) DEFAULT NULL,
  `firma_aux_res` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabla: Detalle_Limpieza
```sql
CREATE TABLE `Detalle_Limpieza` (
  `id_detalle` int(11) NOT NULL,
  `id_bitacora` int(11) NOT NULL,
  `elemento` varchar(100) NOT NULL,
  `lunes_mat` tinyint(1) DEFAULT 0,
  `lunes_vesp` tinyint(1) DEFAULT 0,
  `martes_mat` tinyint(1) DEFAULT 0,
  `martes_vesp` tinyint(1) DEFAULT 0,
  `miercoles_mat` tinyint(1) DEFAULT 0,
  `miercoles_vesp` tinyint(1) DEFAULT 0,
  `jueves_mat` tinyint(1) DEFAULT 0,
  `jueves_vesp` tinyint(1) DEFAULT 0,
  `viernes_mat` tinyint(1) DEFAULT 0,
  `viernes_vesp` tinyint(1) DEFAULT 0,
  `sabado_mat` tinyint(1) DEFAULT 0,
  `sabado_vesp` tinyint(1) DEFAULT 0,
  `domingo_mat` tinyint(1) DEFAULT 0,
  `domingo_vesp` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabla: recordatorios_limpieza (Se crea automáticamente)
```sql
CREATE TABLE `recordatorios_limpieza` (
  `id_recordatorio` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL,
  `descripcion` TEXT,
  `fecha_recordatorio` DATETIME NOT NULL,
  `prioridad` ENUM('baja', 'media', 'alta') DEFAULT 'media',
  `estado` ENUM('activo', 'completado', 'cancelado') DEFAULT 'activo',
  `id_usuario` INT,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Uso del Módulo

### 1. Crear una Nueva Bitácora
1. Hacer clic en "Nueva Bitácora"
2. Completar el formulario con:
   - Área de limpieza
   - Semana
   - Fechas de inicio y fin
   - Responsables
3. Hacer clic en "Guardar Bitácora"

### 2. Gestionar Elementos de Limpieza
1. Hacer clic en "Ver Elementos" en una bitácora
2. Hacer clic en "Agregar Elemento" para añadir nuevos elementos
3. Marcar/desmarcar checkboxes según se completen las tareas
4. Los cambios se guardan automáticamente

### 3. Usar Recordatorios
1. Hacer clic en "Recordatorios"
2. Crear nuevos recordatorios con fecha y prioridad
3. Marcar como completados o cancelar según corresponda

## Pruebas

Para probar el módulo, ejecutar:
```bash
php test_bitacora.php
```

Este archivo verificará:
- Conexión a la base de datos
- Funcionamiento de controladores
- Creación y eliminación de datos de prueba
- Todas las operaciones CRUD

## Dependencias

- PHP 7.4+
- MySQL 5.7+
- jQuery 3.0+
- Bootstrap 5.0+
- DataTables
- SweetAlert2
- Font Awesome

## Notas Técnicas

- El módulo utiliza AJAX para todas las operaciones
- Los datos se validan tanto en frontend como backend
- Se implementa manejo de errores robusto
- La interfaz es responsive y compatible con dispositivos móviles
- Se utiliza prepared statements para prevenir SQL injection
