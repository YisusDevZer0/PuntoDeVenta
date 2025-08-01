#!/bin/bash

echo "=== Limpieza de despliegue iniciada ==="

# Eliminar archivos temporales del servidor
echo "Eliminando archivos temporales..."
rm -f satisfiable
rm -f *.tmp
rm -f *.temp

# Eliminar archivos de Composer si existen
if [ -d "vendor" ]; then
    echo "Eliminando directorio vendor..."
    rm -rf vendor/
fi

if [ -f "composer.lock" ]; then
    echo "Eliminando composer.lock..."
    rm -f composer.lock
fi

# Verificar que composer.json existe
if [ ! -f "composer.json" ]; then
    echo "Error: composer.json no encontrado"
    exit 1
fi

echo "Verificando versión de PHP..."
php -v

# Instalar dependencias con configuración específica
echo "Instalando dependencias con configuración específica..."

# Primera tentativa: instalación normal con ignore-platform-reqs
if composer install --no-dev --no-interaction --ignore-platform-reqs; then
    echo "✓ Instalación exitosa"
    exit 0
fi

echo "Primera tentativa falló, intentando con prefer-dist..."

# Segunda tentativa: preferir distribuciones
if composer install --no-dev --no-interaction --ignore-platform-reqs --prefer-dist; then
    echo "✓ Instalación exitosa con prefer-dist"
    exit 0
fi

echo "Segunda tentativa falló, intentando instalación manual..."

# Tercera tentativa: instalar dependencias una por una
composer require phpoffice/phpspreadsheet:^1.29 --no-dev --ignore-platform-reqs
composer require minishlink/web-push:^9.0 --no-dev --ignore-platform-reqs

# Verificar instalación
if [ -f "vendor/autoload.php" ]; then
    echo "✓ Dependencias instaladas correctamente"
else
    echo "✗ Error: No se pudieron instalar las dependencias"
    exit 1
fi

echo "=== Limpieza de despliegue completada ===" 