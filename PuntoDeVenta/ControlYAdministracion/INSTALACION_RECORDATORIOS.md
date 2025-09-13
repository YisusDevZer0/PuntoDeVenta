# 🔔 Instalación del Sistema de Recordatorios - Doctor Pez

## ✅ Pasos Completados

### 1. Base de Datos
- ✅ Script SQL creado y corregido para tu estructura de base de datos
- ✅ 7 tablas principales creadas
- ✅ Vistas optimizadas implementadas
- ✅ Triggers y procedimientos almacenados configurados

### 2. Archivos del Sistema
- ✅ Controladores PHP creados
- ✅ Servicios de WhatsApp y Notificaciones implementados
- ✅ API REST completa
- ✅ Interfaz de usuario con Material Design
- ✅ JavaScript funcional
- ✅ Estilos CSS personalizados

### 3. Menú Principal
- ✅ Opción "Recordatorios" agregada al menú principal
- ✅ Enlace directo a `RecordatoriosSistema.php`

## 📋 Pasos Pendientes para Completar la Instalación

### 1. Ejecutar el SQL en tu Base de Datos
```sql
-- Copia y ejecuta el código SQL que te proporcioné anteriormente
-- en tu base de datos u858848268_doctorpez
```

### 2. Subir Archivos al Servidor
Sube estos archivos a tu servidor en la carpeta `PuntoDeVenta/ControlYAdministracion/`:

**Controladores:**
- `Controladores/RecordatoriosSistemaController.php`
- `Controladores/WhatsAppService.php`
- `Controladores/NotificacionesService.php`

**Interfaz:**
- `RecordatoriosSistema.php`
- `css/recordatorios.css`
- `js/recordatorios.js`

**API:**
- `api/recordatorios_api.php`

**Sistema de Programación:**
- `cron/recordatorios_cron.php`
- `cron/cron_config.php`

**Instalación:**
- `instalar_recordatorios.php`

### 3. Configurar Permisos de Archivos
```bash
chmod 755 cron/recordatorios_cron.php
chmod 755 uploads/recordatorios/
chmod 755 logs/
```

### 4. Configurar Cron Job
Agregar esta línea al crontab de tu servidor:
```bash
* * * * * /usr/bin/php /ruta/completa/a/tu/proyecto/PuntoDeVenta/ControlYAdministracion/cron/recordatorios_cron.php
```

### 5. Configurar WhatsApp
1. Acceder a `RecordatoriosSistema.php`
2. Ir a configuración de WhatsApp
3. Agregar:
   - URL de tu API de WhatsApp
   - Token de autenticación
   - Número de teléfono

## 🚀 Cómo Usar el Sistema

### Acceso
1. Iniciar sesión en el sistema
2. Ir a **"Recordatorios"** en el menú principal
3. El sistema se cargará automáticamente

### Crear Recordatorio
1. Click en el botón **"+"** (nuevo recordatorio)
2. Llenar formulario:
   - **Título**: Título del recordatorio
   - **Descripción**: Descripción detallada
   - **Fecha Programada**: Cuándo enviar
   - **Prioridad**: Baja, Media, Alta, Urgente
   - **Destinatarios**: Todos, Sucursal, Grupo, Individual
   - **Tipo de Envío**: WhatsApp, Notificación, Ambos
   - **Mensajes**: Personalizar mensajes específicos

### Gestionar Grupos
1. Crear grupos de usuarios desde la interfaz
2. Agregar miembros a cada grupo
3. Usar grupos como destinatarios

### Ver Estadísticas
1. Acceder a la sección de estadísticas
2. Ver métricas de envío
3. Analizar rendimiento

## 🔧 Configuración Adicional

### Variables de Entorno
Si necesitas configurar variables específicas, edita:
- `cron/cron_config.php` - Configuración del cron
- `Controladores/WhatsAppService.php` - Configuración de WhatsApp

### Personalización
- **Estilos**: Edita `css/recordatorios.css`
- **Funcionalidad**: Modifica `js/recordatorios.js`
- **Plantillas**: Crea nuevas plantillas desde la interfaz

## 🚨 Solución de Problemas

### Si los recordatorios no se envían:
1. Verificar que el cron job esté configurado
2. Revisar logs: `tail -f cron/recordatorios_cron.log`
3. Verificar configuración de WhatsApp
4. Comprobar conexión a base de datos

### Si hay errores de permisos:
1. Verificar permisos de archivos
2. Asegurar que el servidor web pueda escribir en logs/
3. Verificar permisos de la base de datos

### Si la interfaz no carga:
1. Verificar que todos los archivos estén subidos
2. Comprobar rutas de archivos CSS y JS
3. Revisar logs de error del servidor web

## 📞 Soporte

Para soporte técnico:
1. Revisar logs del sistema
2. Verificar configuración
3. Comprobar permisos de archivos
4. Contactar al administrador del sistema

---

**¡El sistema de recordatorios está listo para usar!** 🎉

Una vez completados estos pasos, podrás crear y enviar recordatorios tanto por WhatsApp como por notificaciones internas a todos los usuarios del sistema.
