# Guía de Solución de Problemas de Despliegue

## Problema: Dependencias de Composer no se pueden resolver

### Diagnóstico por SSH

1. **Conectarse al servidor:**
   ```bash
   ssh usuario@servidor
   cd /ruta/del/proyecto
   ```

2. **Verificar versión de PHP:**
   ```bash
   php -v
   ```

3. **Verificar extensiones de PHP:**
   ```bash
   php -m | grep -E "(json|mbstring|xml|zip)"
   ```

4. **Verificar Composer:**
   ```bash
   composer --version
   ```

5. **Limpiar e intentar instalación:**
   ```bash
   rm -rf vendor/
   rm -f composer.lock
   composer install --no-dev --no-interaction
   ```

### Soluciones alternativas

#### Opción 1: Ignorar requisitos de plataforma
```bash
composer install --no-dev --no-interaction --ignore-platform-reqs
```

#### Opción 2: Usar versiones específicas
```bash
composer require phpoffice/phpspreadsheet:^1.29 --no-dev
composer require minishlink/web-push:^9.0 --no-dev
```

#### Opción 3: Actualizar Composer
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
- Versión de PHP en el servidor (debe ser >= 7.4)
- Extensiones de PHP habilitadas
- Memoria disponible para Composer
- Permisos de escritura en el directorio

### Comandos de emergencia

Si todo falla, usar:
```bash
composer install --no-dev --no-interaction --ignore-platform-reqs --prefer-dist
``` 