# Solución para el Problema del Controlador del Checador

## Problema Identificado

El controlador del checador está devolviendo errores 404 y 301, lo que indica problemas de configuración del servidor o rutas incorrectas.

## Archivos Creados para Diagnóstico

### 1. `diagnostico_servidor.php`
- Verifica la configuración del servidor
- Comprueba la existencia de archivos necesarios
- Valida la conexión a la base de datos
- Revisa extensiones PHP requeridas

### 2. `prueba_exacta.php`
- Simula exactamente la prueba que falló
- Usa los mismos datos proporcionados en la consulta
- Proporciona análisis detallado de la respuesta

### 3. `test_checador_completo.php`
- Script de prueba completo con cURL
- Captura headers y body de la respuesta
- Decodifica respuestas JSON

### 4. `test_checador.php`
- Prueba local del controlador
- Simula datos POST internamente

## Pasos para Solucionar el Problema

### Paso 1: Ejecutar Diagnóstico
```bash
# Acceder al script de diagnóstico
http://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/diagnostico_servidor.php
```

### Paso 2: Verificar Estructura de Archivos
Asegúrate de que existan estos archivos:
- `ChecadorController.php`
- `db_connect.php`
- `ControladorUsuario.php`

### Paso 3: Verificar Permisos
Los archivos deben tener permisos de lectura (644 o 755):
```bash
chmod 644 *.php
```

### Paso 4: Verificar Configuración del Servidor
1. **Apache**: Verificar que `.htaccess` no esté bloqueando el acceso
2. **Nginx**: Verificar configuración de PHP-FPM
3. **Logs**: Revisar logs de error del servidor web

### Paso 5: Probar con URL Relativa
En lugar de usar la URL absoluta, prueba con:
```php
$url = './ChecadorController.php';
```

### Paso 6: Verificar Base de Datos
Asegúrate de que las tablas necesarias existan:
- `asistencias`
- `ubicaciones_trabajo`
- `Usuarios_PV`

## Mejoras Implementadas en el Controlador

### 1. Manejo de Errores Mejorado
- Verificación de existencia de archivos antes de incluirlos
- Logging detallado de errores
- Respuestas JSON estructuradas

### 2. Modo de Prueba
- Permite pruebas sin autenticación usando `test_mode=1`
- ID de usuario configurable para pruebas

### 3. Debugging
- Reporte de errores habilitado
- Logging de acciones y respuestas
- Información de debug en respuestas de error

## Código de Prueba

### Datos de Prueba Estándar
```php
$datosPrueba = [
    'action' => 'registrar_asistencia',
    'tipo' => 'entrada',
    'latitud' => '20.9674',
    'longitud' => '-89.5926',
    'timestamp' => '2025-08-18 02:09:10',
    'test_mode' => '1',
    'usuario_id' => '1'
];
```

### Ejemplo de cURL
```bash
curl -X POST \
  -d "action=registrar_asistencia&tipo=entrada&latitud=20.9674&longitud=-89.5926&timestamp=2025-08-18%2002:09:10&test_mode=1&usuario_id=1" \
  http://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ChecadorController.php
```

## Posibles Causas del Error 404/301

1. **Ruta Incorrecta**: El archivo no existe en la ubicación especificada
2. **Configuración del Servidor**: Problemas con la configuración de Apache/Nginx
3. **Redirecciones**: Configuración de redirecciones en el servidor
4. **Permisos**: Archivos sin permisos de lectura
5. **Estructura de Directorios**: Cambios en la estructura del proyecto

## Comandos de Verificación

### Verificar Existencia de Archivos
```bash
ls -la /ruta/al/servidor/PuntoDeVenta/ControlYAdministracion/Controladores/
```

### Verificar Logs del Servidor
```bash
# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log
```

### Verificar Permisos
```bash
find /ruta/al/servidor/PuntoDeVenta -name "*.php" -exec ls -la {} \;
```

## Contacto y Soporte

Si el problema persiste después de seguir estos pasos:
1. Revisar los logs del servidor web
2. Verificar la configuración de PHP
3. Comprobar la conectividad de la base de datos
4. Validar la estructura de directorios del proyecto
