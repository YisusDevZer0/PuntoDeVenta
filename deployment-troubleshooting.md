# Guía de Solución de Problemas de Despliegue

## Problema: Dependencias de Composer no se pueden resolver

### Problema específico identificado:
**Error:** `brick/math 0.12.1 requires php ^8.1 -> your PHP version (8.0.30) does not satisfy that requirement`

### Solución inmediata:

1. **Conectarse al servidor:**
   ```bash
   ssh usuario@servidor
   cd /ruta/del/proyecto
   ```

2. **Ejecutar el script de solución:**
   ```bash
   chmod +x fix-composer-deps.sh
   ./fix-composer-deps.sh
   ```

3. **Si el script falla, usar comandos manuales:**
   ```bash
   # Limpiar instalación
   rm -rf vendor/
   rm -f composer.lock
   
   # Instalar con versiones compatibles
   composer install --no-dev --no-interaction --ignore-platform-reqs
   ```

### Diagnóstico por SSH

1. **Verificar versión de PHP:**
   ```bash
   php -v
   ```

2. **Verificar extensiones de PHP:**
   ```bash
   php -m | grep -E "(json|mbstring|xml|zip)"
   ```

3. **Verificar Composer:**
   ```bash
   composer --version
   ```

### Soluciones alternativas

#### Opción 1: Forzar versión de brick/math compatible
```bash
composer require brick/math:^0.11.0 --no-dev --ignore-platform-reqs
```

#### Opción 2: Usar versiones específicas de todas las dependencias
```bash
composer require phpoffice/phpspreadsheet:^1.29 --no-dev --ignore-platform-reqs
composer require minishlink/web-push:^9.0 --no-dev --ignore-platform-reqs
```

#### Opción 3: Actualizar Composer (si es posible)
```bash
composer self-update
```

### Verificación de dependencias

Para ver qué dependencias están causando conflictos:
```bash
composer diagnose
composer show --tree
```

### Configuración del servidor

Si el problema persiste, verificar:
- Versión de PHP en el servidor (actualmente 8.0.30)
- Extensiones de PHP habilitadas
- Memoria disponible para Composer
- Permisos de escritura en el directorio

### Comandos de emergencia

Si todo falla, usar:
```bash
composer install --no-dev --no-interaction --ignore-platform-reqs --prefer-dist
```

### Nota importante:
El servidor tiene PHP 8.0.30, pero `brick/math 0.12.1` requiere PHP 8.1+. La solución es forzar versiones compatibles o usar `--ignore-platform-reqs`. 