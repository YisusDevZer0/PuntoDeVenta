#!/bin/bash

echo "=== Pre-deployment script iniciado ==="

# Limpiar archivos temporales y cache
echo "Limpiando archivos temporales..."
rm -rf vendor/
rm -f composer.lock

# Verificar versión de PHP
echo "Verificando versión de PHP..."
php -v

# Instalar dependencias con configuración específica
echo "Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# Verificar que las dependencias se instalaron correctamente
if [ -f "vendor/autoload.php" ]; then
    echo "✓ Dependencias instaladas correctamente"
else
    echo "✗ Error: No se pudieron instalar las dependencias"
    exit 1
fi

echo "=== Pre-deployment script completado ===" 