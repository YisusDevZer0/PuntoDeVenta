#!/bin/bash

echo "=== Post-deployment script iniciado ==="

# Verificar que estamos en el directorio correcto
if [ ! -f "composer.json" ]; then
    echo "Error: composer.json no encontrado"
    exit 1
fi

# Verificar versión de PHP
echo "Versión de PHP:"
php -v

# Limpiar instalación anterior si existe
if [ -d "vendor" ]; then
    echo "Limpiando instalación anterior..."
    rm -rf vendor/
fi

if [ -f "composer.lock" ]; then
    echo "Eliminando composer.lock anterior..."
    rm -f composer.lock
fi

# Instalar dependencias con configuración específica para PHP 8.0
echo "Instalando dependencias de Composer..."

# Intentar instalación con diferentes estrategias
echo "Intentando instalación con --ignore-platform-reqs..."
if composer install --no-dev --no-interaction --ignore-platform-reqs; then
    echo "✓ Instalación exitosa con --ignore-platform-reqs"
    exit 0
fi

echo "Primera tentativa falló, intentando con --prefer-dist..."
if composer install --no-dev --no-interaction --ignore-platform-reqs --prefer-dist; then
    echo "✓ Instalación exitosa con --prefer-dist"
    exit 0
fi

echo "Segunda tentativa falló, intentando instalación manual..."
# Instalar dependencias una por una
composer require phpoffice/phpspreadsheet:^1.29 --no-dev --ignore-platform-reqs
composer require minishlink/web-push:^9.0 --no-dev --ignore-platform-reqs

# Verificar instalación
if [ -f "vendor/autoload.php" ]; then
    echo "✓ Dependencias instaladas correctamente"
    echo "Paquetes instalados:"
    composer show --installed
else
    echo "✗ Error: No se pudieron instalar las dependencias"
    exit 1
fi

echo "=== Post-deployment script completado ===" 