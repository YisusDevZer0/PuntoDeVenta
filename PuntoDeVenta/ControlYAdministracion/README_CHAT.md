# Sistema de Mensajes/Chat - Doctor Pez

## Descripción
Sistema de chat moderno y funcional integrado en el módulo de Control y Administración. Permite comunicación rápida entre usuarios del sistema con funcionalidades avanzadas como envío de archivos, notificaciones push, y gestión de conversaciones.

## Características Principales

### 🚀 Funcionalidades Core
- **Chat en tiempo real** con actualización automática
- **Múltiples tipos de conversación**: Individual, Grupo, Sucursal, General
- **Envío de archivos** (imágenes, videos, documentos)
- **Respuestas a mensajes** específicos
- **Edición y eliminación** de mensajes
- **Notificaciones push** y de sonido
- **Tema oscuro/claro** configurable
- **Búsqueda de conversaciones**
- **Indicadores de estado** (escribiendo, leído, etc.)

### 🎨 Interfaz de Usuario
- **Diseño responsivo** para móviles y desktop
- **Interfaz moderna** con animaciones suaves
- **Scroll infinito** para mensajes antiguos
- **Emojis** y reacciones
- **Menús contextuales** para acciones rápidas

### 🔧 Funcionalidades Técnicas
- **API REST** completa
- **Actualización automática** cada 3 segundos
- **Gestión de estados** de conexión
- **Optimización de consultas** con índices
- **Triggers automáticos** para mantenimiento
- **Vistas optimizadas** para consultas frecuentes

## Estructura de Archivos

```
ControlYAdministracion/
├── Mensajes.php                 # Página principal del chat
├── Controladores/
│   └── ChatController.php       # Controlador principal
├── api/
│   └── chat_api.php            # API REST
├── css/
│   └── chat.css                # Estilos del chat
├── js/
│   └── chat.js                 # JavaScript del frontend
├── database/
│   └── chat_tables.sql         # Scripts de base de datos
├── instalar_chat.php           # Script de instalación
└── README_CHAT.md              # Esta documentación
```

## Instalación

### 1. Ejecutar Script de Instalación
```bash
# Acceder a la URL desde el navegador (como administrador)
http://tu-dominio/ControlYAdministracion/instalar_chat.php
```

### 2. Verificar Instalación
- Las tablas se crean automáticamente
- Se crean conversaciones iniciales
- Se configuran triggers y vistas

### 3. Configurar Permisos
- Asegurar que los usuarios tengan acceso a la carpeta `uploads/chat/`
- Configurar notificaciones push si es necesario

## Uso del Sistema

### Acceso
1. Ir a **Farmacia > Mensajes** en el menú principal
2. El sistema se carga automáticamente

### Crear Nueva Conversación
1. Click en **"Nueva"** en la lista de conversaciones
2. Seleccionar tipo de conversación
3. Agregar participantes
4. Crear la conversación

### Enviar Mensajes
1. Seleccionar una conversación
2. Escribir en el campo de texto
3. Presionar Enter o click en enviar

### Enviar Archivos
1. Click en el ícono de clip
2. Seleccionar archivo
3. El archivo se sube automáticamente

## Configuración

### Configuración de Usuario
- **Notificaciones de sonido**: Activar/desactivar
- **Notificaciones push**: Activar/desactivar
- **Tema oscuro**: Cambiar apariencia
- **Mensajes por página**: Controlar carga

### Configuración del Sistema
- **Intervalo de actualización**: 3 segundos (configurable)
- **Tamaño máximo de archivos**: Configurar en PHP
- **Tipos de archivo permitidos**: Configurar en JavaScript

## Base de Datos

### Tablas Principales
- `chat_conversaciones`: Conversaciones y grupos
- `chat_participantes`: Usuarios en cada conversación
- `chat_mensajes`: Mensajes enviados
- `chat_lecturas`: Estados de lectura
- `chat_reacciones`: Reacciones a mensajes
- `chat_configuraciones`: Configuraciones por usuario

### Vistas Optimizadas
- `v_chat_conversaciones_info`: Conversaciones con información completa
- `v_chat_mensajes_info`: Mensajes con datos del usuario

### Triggers Automáticos
- Actualización de último mensaje en conversación
- Creación de configuración por defecto para nuevos usuarios

## API Endpoints

### GET
- `/api/chat_api.php?action=conversaciones` - Obtener conversaciones
- `/api/chat_api.php?action=mensajes&conversacion_id=X` - Obtener mensajes
- `/api/chat_api.php?action=usuarios` - Obtener usuarios disponibles
- `/api/chat_api.php?action=configuracion` - Obtener configuración

### POST
- `/api/chat_api.php?action=enviar_mensaje` - Enviar mensaje
- `/api/chat_api.php?action=crear_conversacion` - Crear conversación
- `/api/chat_api.php?action=agregar_participante` - Agregar participante
- `/api/chat_api.php?action=subir_archivo` - Subir archivo

### PUT
- `/api/chat_api.php?action=editar_mensaje` - Editar mensaje
- `/api/chat_api.php?action=actualizar_configuracion` - Actualizar configuración

### DELETE
- `/api/chat_api.php?action=eliminar_mensaje` - Eliminar mensaje

## Personalización

### Agregar Nuevos Tipos de Mensaje
1. Modificar enum en `chat_mensajes.tipo_mensaje`
2. Actualizar `determinarTipoMensaje()` en `chat_api.php`
3. Agregar lógica en `crearContenidoArchivo()` en `chat.js`

### Agregar Nuevas Reacciones
1. Modificar tabla `chat_reacciones`
2. Actualizar interfaz de usuario
3. Agregar lógica de envío

### Personalizar Notificaciones
1. Modificar `enviarNotificacionesPush()` en `ChatController.php`
2. Integrar con sistema de notificaciones existente
3. Configurar OneSignal o similar

## Solución de Problemas

### Problemas Comunes

#### Los mensajes no se cargan
- Verificar conexión a base de datos
- Revisar permisos de usuario
- Comprobar logs de error en consola

#### Los archivos no se suben
- Verificar permisos de carpeta `uploads/chat/`
- Revisar configuración de PHP (upload_max_filesize)
- Comprobar tipos de archivo permitidos

#### Las notificaciones no funcionan
- Verificar permisos de notificación en el navegador
- Revisar configuración de OneSignal
- Comprobar configuración de usuario

### Logs y Debugging
- Revisar logs de PHP para errores del servidor
- Usar consola del navegador para errores de JavaScript
- Verificar consultas SQL en la base de datos

## Seguridad

### Medidas Implementadas
- **Validación de entrada** en todos los endpoints
- **Sanitización de datos** antes de mostrar
- **Verificación de permisos** para cada acción
- **Preparación de consultas** para prevenir SQL injection
- **Validación de archivos** antes de subir

### Recomendaciones
- Mantener actualizado el sistema
- Revisar logs regularmente
- Configurar backup de mensajes importantes
- Implementar límites de rate limiting si es necesario

## Rendimiento

### Optimizaciones Implementadas
- **Índices optimizados** en base de datos
- **Paginación** de mensajes
- **Actualización incremental** de conversaciones
- **Caché de configuración** de usuario
- **Lazy loading** de mensajes antiguos

### Monitoreo
- Revisar tiempo de respuesta de consultas
- Monitorear uso de memoria
- Verificar carga del servidor

## Soporte

Para soporte técnico o reportar problemas:
1. Revisar esta documentación
2. Verificar logs del sistema
3. Contactar al administrador del sistema

## Changelog

### v1.0.0 (Inicial)
- Sistema básico de chat
- Envío de archivos
- Notificaciones
- Interfaz responsiva
- API REST completa

---

**Desarrollado para Doctor Pez - Sistema de Punto de Venta**
