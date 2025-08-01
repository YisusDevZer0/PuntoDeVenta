#!/bin/bash

echo "=== Force install script ==="

# Forzar eliminación de composer.lock si existe
if [ -f "composer.lock" ]; then
    echo "Eliminando composer.lock conflictivo..."
    rm -f composer.lock
fi

# Limpiar vendor/ si existe
if [ -d "vendor" ]; then
    echo "Limpiando vendor/ anterior..."
    rm -rf vendor/
fi

echo "Instalando dependencias con configuración específica..."

# Instalar sin usar composer.lock
composer install --no-dev --no-interaction --ignore-platform-reqs --prefer-dist

# Verificar instalación
if [ -f "vendor/autoload.php" ]; then
    echo "✓ Dependencias instaladas correctamente"
    echo "Paquetes instalados:"
    composer show --installed
else
    echo "✗ Error: No se pudieron instalar las dependencias"
    exit 1
fi

echo "=== Force install completado ===" 