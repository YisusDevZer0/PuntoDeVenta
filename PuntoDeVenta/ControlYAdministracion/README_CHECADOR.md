# Sistema de Checador - Documentación

## Descripción General

El Sistema de Checador es una aplicación web que permite a los empleados registrar su entrada y salida del trabajo utilizando geolocalización. El sistema verifica que el empleado esté dentro del área de trabajo configurada antes de permitir el registro.

## Características Principales

### 🔐 Autenticación y Seguridad
- Verificación de sesión de usuario
- Validación de permisos
- Registro de logs de actividad

### 📍 Geolocalización
- Detección automática de ubicación del usuario
- Configuración de áreas de trabajo por usuario
- Verificación en tiempo real de proximidad
- Radio configurable (por defecto 100 metros)

### ⏰ Registro de Asistencia
- Registro de entrada y salida
- Validación de duplicados por día
- Almacenamiento de coordenadas GPS
- Timestamp automático

### 🎨 Interfaz de Usuario
- Diseño responsivo y moderno
- Indicadores visuales de estado
- Reloj en tiempo real
- Información meteorológica simulada
- Botones habilitados/deshabilitados según ubicación

## Archivos del Sistema

### Frontend
- `Checador.php` - Página principal del checador
- `ConfiguracionUbicaciones.php` - Configuración de ubicaciones de trabajo
- `js/checador.js` - Lógica JavaScript del checador

### Backend
- `Controladores/ChecadorController.php` - Controlador principal
- `database/checador_tables.sql` - Script de creación de tablas

## Instalación

### 1. Crear las Tablas de Base de Datos

Ejecutar el script SQL en tu base de datos:

```sql
-- Ejecutar el archivo: database/checador_tables.sql
```

### 2. Configurar Permisos

Asegurarse de que el usuario tenga permisos de escritura en:
- `Controladores/`
- `js/`
- `database/`

### 3. Verificar Dependencias

El sistema requiere:
- PHP 7.4+
- MySQL 5.7+
- Navegador con soporte para geolocalización
- Conexión a internet para geolocalización

## Uso del Sistema

### Para Empleados

1. **Acceder al Checador**
   - Navegar a `Checador.php`
   - El sistema solicitará permisos de ubicación

2. **Configurar Ubicación de Trabajo** (Primera vez)
   - Si no hay ubicación configurada, aparecerá un botón para configurar
   - Hacer clic en "Configurar Ubicación"
   - Confirmar la ubicación actual como centro de trabajo

3. **Registrar Asistencia**
   - Los botones se habilitan automáticamente cuando estás en el área de trabajo
   - Hacer clic en "Entrada" o "Salida" según corresponda
   - Confirmar el registro

### Para Administradores

1. **Configuración de Ubicaciones**
   - Acceder a `ConfiguracionUbicaciones.php`
   - Agregar, editar o eliminar ubicaciones de trabajo
   - Configurar radio de área de trabajo

2. **Monitoreo**
   - Revisar logs en la tabla `logs_checador`
   - Consultar registros en la tabla `asistencias`
   - Usar las vistas para reportes

## Estructura de Base de Datos

### Tabla: `ubicaciones_trabajo`
```sql
- id (PK)
- usuario_id (FK)
- nombre
- descripcion
- latitud
- longitud
- radio (metros)
- direccion
- estado (active/inactive)
- created_at
- updated_at
```

### Tabla: `asistencias`
```sql
- id (PK)
- usuario_id (FK)
- tipo (entrada/salida)
- latitud
- longitud
- fecha_hora
- created_at
```

### Tabla: `configuracion_checador`
```sql
- id (PK)
- usuario_id (FK)
- clave
- valor
- created_at
- updated_at
```

### Tabla: `logs_checador`
```sql
- id (PK)
- usuario_id (FK)
- accion
- detalles
- ip_address
- user_agent
- created_at
```

## API Endpoints

### Registrar Asistencia
```
POST: Controladores/ChecadorController.php
Action: registrar_asistencia
Params: tipo, latitud, longitud, timestamp
```

### Obtener Ubicaciones
```
POST: Controladores/ChecadorController.php
Action: obtener_ubicaciones
```

### Guardar Ubicación
```
POST: Controladores/ChecadorController.php
Action: guardar_ubicacion
Params: nombre, descripcion, latitud, longitud, radio, direccion, estado
```

### Verificar Ubicación
```
POST: Controladores/ChecadorController.php
Action: verificar_ubicacion
Params: latitud, longitud
```

## Configuración

### Variables de Entorno
```php
// En db_connect.php
define('DB_HOST', 'localhost');
define('DB_USER', 'usuario');
define('DB_PASS', 'password');
define('DB_NAME', 'nombre_base_datos');
```

### Configuraciones del Checador
```sql
-- Radio por defecto (metros)
INSERT INTO configuracion_checador (usuario_id, clave, valor) 
VALUES (1, 'radio_por_defecto', '100');

-- Tiempo de verificación (segundos)
INSERT INTO configuracion_checador (usuario_id, clave, valor) 
VALUES (1, 'tiempo_verificacion', '300');

-- Notificaciones activas
INSERT INTO configuracion_checador (usuario_id, clave, valor) 
VALUES (1, 'notificaciones_activas', 'true');
```

## Personalización

### Cambiar Radio por Defecto
```javascript
// En js/checador.js
this.radioPorDefecto = 150; // metros
```

### Cambiar Intervalo de Verificación
```javascript
// En js/checador.js
this.verificationInterval = setInterval(() => {
    this.checkLocation();
}, 60000); // 60 segundos
```

### Personalizar Estilos
```css
/* En Checador.php */
.attendance-button.entry {
    background: linear-gradient(135deg, #28a745, #20c997);
}
```

## Troubleshooting

### Problema: No se obtiene la ubicación
**Solución:**
- Verificar que el navegador tenga permisos de ubicación
- Asegurarse de que el sitio use HTTPS (requerido para geolocalización)
- Verificar que el GPS esté activado en dispositivos móviles

### Problema: Los botones no se habilitan
**Solución:**
- Verificar que haya ubicaciones configuradas
- Comprobar que el usuario esté dentro del radio configurado
- Revisar la consola del navegador para errores

### Problema: Error de conexión a la base de datos
**Solución:**
- Verificar credenciales en `db_connect.php`
- Comprobar que las tablas existan
- Verificar permisos de la base de datos

## Seguridad

### Consideraciones
- Todas las consultas usan prepared statements
- Validación de sesión en cada request
- Sanitización de datos de entrada
- Logs de auditoría automáticos

### Recomendaciones
- Usar HTTPS en producción
- Configurar firewall para la base de datos
- Realizar backups regulares
- Monitorear logs de actividad

## Mantenimiento

### Limpieza de Logs
```sql
-- Ejecutar mensualmente
CALL LimpiarLogsChecador(30); -- Eliminar logs de más de 30 días
```

### Optimización de Base de Datos
```sql
-- Revisar índices periódicamente
ANALYZE TABLE asistencias;
ANALYZE TABLE ubicaciones_trabajo;
ANALYZE TABLE logs_checador;
```

## Soporte

Para reportar problemas o solicitar mejoras:
1. Revisar logs en la consola del navegador
2. Verificar logs de la base de datos
3. Comprobar permisos de archivos
4. Validar configuración de red

## Versiones

- **v1.0.0** - Versión inicial con funcionalidad básica
- Funcionalidades: registro entrada/salida, geolocalización, configuración de ubicaciones 