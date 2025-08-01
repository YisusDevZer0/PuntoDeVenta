#!/bin/bash

echo "=== Post-deploy installation script ==="

# Verificar que estamos en el directorio correcto
if [ ! -f "composer.json" ]; then
    echo "Error: composer.json no encontrado"
    exit 1
fi

echo "Versión de PHP:"
php -v

echo "Limpiando instalación anterior..."
rm -rf vendor/
rm -f composer.lock

echo "Instalando dependencias con configuración específica..."

# Estrategia 1: Instalación básica con ignore-platform-reqs
echo "Intentando instalación con --ignore-platform-reqs..."
if composer install --no-dev --no-interaction --ignore-platform-reqs; then
    echo "✓ Instalación exitosa"
    exit 0
fi

echo "Primera tentativa falló, intentando con --prefer-dist..."

# Estrategia 2: Instalación con prefer-dist
if composer install --no-dev --no-interaction --ignore-platform-reqs --prefer-dist; then
    echo "✓ Instalación exitosa con prefer-dist"
    exit 0
fi

echo "Segunda tentativa falló, intentando instalación manual..."

# Estrategia 3: Instalar dependencias una por una
composer require phpoffice/phpspreadsheet:^1.29 --no-dev --ignore-platform-reqs
composer require minishlink/web-push:^9.0 --no-dev --ignore-platform-reqs

# Verificar instalación
if [ -f "vendor/autoload.php" ]; then
    echo "✓ Dependencias instaladas correctamente"
    echo "Paquetes instalados:"
    composer show --installed
else
    echo "✗ Error: No se pudieron instalar las dependencias"
    echo "Intentando diagnóstico..."
    composer diagnose
    exit 1
fi

echo "=== Post-deploy installation completado ===" 