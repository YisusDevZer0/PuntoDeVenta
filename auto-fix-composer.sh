#!/bin/bash

echo "=== Auto-fix Composer Dependencies ==="

# Forzar limpieza completa
echo "Limpiando instalación anterior..."
rm -rf vendor/
rm -f composer.lock
rm -f satisfiable
rm -f *.tmp
rm -f *.temp

# Verificar que composer.json existe
if [ ! -f "composer.json" ]; then
    echo "Error: composer.json no encontrado"
    exit 1
fi

echo "Versión de PHP:"
php -v

echo "Instalando dependencias con configuración específica..."

# Estrategia 1: Instalación con ignore-platform-reqs
echo "Intentando instalación con --ignore-platform-reqs..."
if composer install --no-dev --no-interaction --ignore-platform-reqs; then
    echo "✓ Instalación exitosa"
    exit 0
fi

# Estrategia 2: Instalación con prefer-dist
echo "Intentando instalación con --prefer-dist..."
if composer install --no-dev --no-interaction --ignore-platform-reqs --prefer-dist; then
    echo "✓ Instalación exitosa con prefer-dist"
    exit 0
fi

# Estrategia 3: Instalación manual de dependencias
echo "Intentando instalación manual..."
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

echo "=== Auto-fix completado ===" 