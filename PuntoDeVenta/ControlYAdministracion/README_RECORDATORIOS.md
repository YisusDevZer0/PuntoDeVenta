# Sistema de Recordatorios - Doctor Pez

## 📋 Descripción

Sistema completo de recordatorios que permite enviar notificaciones tanto por WhatsApp como por notificaciones internas del sistema a todos los usuarios del Punto de Venta. Incluye interfaz administrativa, programación automática y gestión de destinatarios.

## 🚀 Características Principales

### ✨ Funcionalidades Core
- **Creación de recordatorios** con programación de fecha y hora
- **Envío dual**: WhatsApp + Notificaciones internas
- **Múltiples tipos de destinatarios**: Todos, Sucursal, Grupo, Individual
- **Sistema de prioridades**: Baja, Media, Alta, Urgente
- **Plantillas personalizables** para mensajes
- **Programación automática** con cron jobs
- **Logs detallados** de envío y errores
- **Estadísticas completas** del sistema

### 🎨 Interfaz de Usuario
- **Diseño responsivo** con Material Design
- **Filtros avanzados** para búsqueda
- **Gestión completa** de recordatorios
- **Vista previa** de mensajes
- **Configuración** de WhatsApp y plantillas

### 🔧 Funcionalidades Técnicas
- **API REST** completa
- **Base de datos optimizada** con vistas y triggers
- **Servicios modulares** para WhatsApp y notificaciones
- **Sistema de logs** automático
- **Limpieza automática** de datos antiguos

## 📁 Estructura de Archivos

```
ControlYAdministracion/
├── RecordatoriosSistema.php              # Interfaz principal
├── Controladores/
│   ├── RecordatoriosSistemaController.php # Controlador principal
│   ├── WhatsAppService.php               # Servicio de WhatsApp
│   └── NotificacionesService.php         # Servicio de notificaciones
├── api/
│   └── recordatorios_api.php             # API REST
├── css/
│   └── recordatorios.css                 # Estilos del sistema
├── js/
│   └── recordatorios.js                  # JavaScript del frontend
├── database/
│   └── recordatorios_tables.sql          # Scripts de base de datos
├── cron/
│   ├── recordatorios_cron.php            # Cron job principal
│   └── cron_config.php                   # Configuración del cron
├── instalar_recordatorios.php            # Instalador del sistema
└── README_RECORDATORIOS.md               # Esta documentación
```

## 🛠️ Instalación

### 1. Ejecutar Instalador
```bash
# Acceder desde el navegador (como administrador)
http://tu-dominio/ControlYAdministracion/instalar_recordatorios.php
```

### 2. Configurar Cron Job
Agregar esta línea al crontab del servidor:
```bash
* * * * * /usr/bin/php /ruta/al/proyecto/ControlYAdministracion/cron/recordatorios_cron.php
```

### 3. Configurar WhatsApp
1. Ir a **Recordatorios > Configuración**
2. Configurar API URL, Token y número de teléfono
3. Probar la conexión

## 📊 Base de Datos

### Tablas Principales

#### `recordatorios_sistema`
Tabla principal que almacena todos los recordatorios:
- `id_recordatorio`: ID único
- `titulo`: Título del recordatorio
- `descripcion`: Descripción detallada
- `mensaje_whatsapp`: Mensaje específico para WhatsApp
- `mensaje_notificacion`: Mensaje para notificaciones internas
- `fecha_programada`: Fecha y hora de envío
- `prioridad`: Baja, Media, Alta, Urgente
- `estado`: Programado, Enviando, Enviado, Cancelado, Error
- `tipo_envio`: WhatsApp, Notificación, Ambos
- `destinatarios`: Todos, Sucursal, Grupo, Individual

#### `recordatorios_destinatarios`
Destinatarios específicos de cada recordatorio:
- `recordatorio_id`: ID del recordatorio
- `usuario_id`: ID del usuario destinatario
- `telefono_whatsapp`: Número de WhatsApp
- `estado_envio`: Pendiente, Enviado, Error, Cancelado

#### `recordatorios_grupos`
Grupos de destinatarios:
- `nombre_grupo`: Nombre del grupo
- `descripcion`: Descripción del grupo
- `sucursal_id`: Sucursal asociada (opcional)

#### `recordatorios_plantillas`
Plantillas de mensajes:
- `nombre`: Nombre de la plantilla
- `tipo`: WhatsApp, Notificación, Ambos
- `plantilla_whatsapp`: Plantilla para WhatsApp
- `plantilla_notificacion`: Plantilla para notificaciones
- `variables_disponibles`: Variables que puede usar la plantilla

### Vistas Optimizadas
- `v_recordatorios_completos`: Recordatorios con información completa
- `v_recordatorios_destinatarios_completos`: Destinatarios con datos de usuario

## 🔧 Uso del Sistema

### Crear Recordatorio
1. Ir a **Recordatorios** en el menú principal
2. Click en **"+"** para crear nuevo recordatorio
3. Llenar formulario:
   - **Título**: Título del recordatorio
   - **Descripción**: Descripción detallada
   - **Fecha Programada**: Cuándo enviar
   - **Prioridad**: Nivel de importancia
   - **Destinatarios**: Quién recibirá el recordatorio
   - **Tipo de Envío**: WhatsApp, Notificación o ambos
   - **Mensajes**: Personalizar mensajes específicos

### Gestionar Grupos
1. Ir a **Recordatorios > Grupos**
2. Crear grupos de usuarios
3. Agregar miembros a cada grupo
4. Usar grupos como destinatarios en recordatorios

### Crear Plantillas
1. Ir a **Recordatorios > Plantillas**
2. Crear plantillas reutilizables
3. Usar variables como `{titulo}`, `{fecha}`, `{prioridad}`
4. Aplicar plantillas al crear recordatorios

### Ver Estadísticas
1. Ir a **Recordatorios > Estadísticas**
2. Ver métricas de envío
3. Analizar rendimiento del sistema
4. Exportar reportes

## 🔌 API Endpoints

### GET
- `/api/recordatorios_api.php?action=listar` - Listar recordatorios
- `/api/recordatorios_api.php?action=obtener&id=X` - Obtener recordatorio específico
- `/api/recordatorios_api.php?action=estadisticas` - Obtener estadísticas
- `/api/recordatorios_api.php?action=grupos` - Listar grupos
- `/api/recordatorios_api.php?action=plantillas` - Listar plantillas
- `/api/recordatorios_api.php?action=usuarios` - Listar usuarios

### POST
- `/api/recordatorios_api.php?action=crear` - Crear recordatorio
- `/api/recordatorios_api.php?action=actualizar` - Actualizar recordatorio
- `/api/recordatorios_api.php?action=eliminar` - Eliminar recordatorio
- `/api/recordatorios_api.php?action=enviar` - Enviar recordatorio inmediatamente

## ⚙️ Configuración

### WhatsApp
```php
// En recordatorios_config_whatsapp
api_url: 'https://api.whatsapp.com/send'
api_token: 'tu_token_aqui'
numero_telefono: '1234567890'
```

### Cron Job
```bash
# Ejecutar cada minuto
* * * * * /usr/bin/php /ruta/cron/recordatorios_cron.php

# Ver logs
tail -f /ruta/logs/recordatorios_cron.log
```

### Notificaciones Push
El sistema se integra automáticamente con OneSignal si está configurado en el proyecto.

## 📈 Monitoreo y Logs

### Logs del Sistema
- **Cron Job**: `/cron/recordatorios_cron.log`
- **Base de Datos**: Tabla `recordatorios_logs`
- **Errores PHP**: Logs del servidor web

### Métricas Disponibles
- Total de recordatorios enviados
- Tasa de éxito/error
- Tiempo promedio de envío
- Uso por tipo de destinatario
- Estadísticas por prioridad

## 🔒 Seguridad

### Permisos Requeridos
- **Crear recordatorios**: Usuario activo
- **Enviar inmediatamente**: Usuario activo
- **Eliminar recordatorios**: Usuario activo
- **Configurar sistema**: Administrador

### Validaciones
- Fechas futuras obligatorias
- Números de teléfono válidos
- Límites de caracteres en mensajes
- Validación de permisos por acción

## 🚨 Solución de Problemas

### Problemas Comunes

#### Los recordatorios no se envían
1. Verificar que el cron job esté configurado
2. Revisar logs del cron: `tail -f cron/recordatorios_cron.log`
3. Verificar configuración de WhatsApp
4. Comprobar conexión a base de datos

#### Errores de WhatsApp
1. Verificar token de API
2. Comprobar número de teléfono
3. Revisar formato de números
4. Verificar límites de la API

#### Notificaciones no aparecen
1. Verificar configuración de OneSignal
2. Comprobar permisos de notificación del navegador
3. Revisar logs de notificaciones
4. Verificar suscripciones de usuarios

### Logs de Depuración
```bash
# Ver logs del cron
tail -f ControlYAdministracion/cron/recordatorios_cron.log

# Ver logs de PHP
tail -f /var/log/apache2/error.log

# Ver logs de base de datos
SELECT * FROM recordatorios_logs ORDER BY fecha_log DESC LIMIT 50;
```

## 🔄 Mantenimiento

### Limpieza Automática
- **Logs**: Se limpian automáticamente después de 30 días
- **Notificaciones**: Se eliminan después de 30 días
- **Archivos temporales**: Se limpian semanalmente

### Backup Recomendado
```sql
-- Backup de tablas de recordatorios
mysqldump -u usuario -p base_datos recordatorios_sistema recordatorios_destinatarios recordatorios_grupos recordatorios_plantillas > backup_recordatorios.sql
```

### Actualizaciones
1. Hacer backup de la base de datos
2. Actualizar archivos del sistema
3. Ejecutar migraciones si las hay
4. Verificar configuración

## 📞 Soporte

Para soporte técnico o reportar problemas:
1. Revisar esta documentación
2. Verificar logs del sistema
3. Comprobar configuración
4. Contactar al administrador del sistema

---

**Sistema de Recordatorios - Doctor Pez v1.0**  
Desarrollado para el sistema Punto de Venta Doctor Pez
