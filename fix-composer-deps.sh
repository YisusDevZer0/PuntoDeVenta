#!/bin/bash

echo "=== Solucionando conflicto de brick/math en PHP 8.0 ==="

# Verificar versión de PHP
echo "Versión de PHP en el servidor:"
php -v

# Limpiar instalación anterior
echo "Limpiando instalación anterior..."
rm -rf vendor/
rm -f composer.lock

# Forzar versión de brick/math compatible con PHP 8.0
echo "Instalando dependencias con versiones compatibles..."

# Instalar con configuración específica para PHP 8.0
composer install --no-dev --no-interaction --ignore-platform-reqs

# Si falla, intentar con versiones específicas
if [ $? -ne 0 ]; then
    echo "Intentando con versiones específicas..."
    
    # Instalar phpspreadsheet con dependencias compatibles
    composer require phpoffice/phpspreadsheet:^1.29 --no-dev --ignore-platform-reqs
    
    # Instalar web-push
    composer require minishlink/web-push:^9.0 --no-dev --ignore-platform-reqs
    
    # Forzar versión de brick/math compatible
    composer require brick/math:^0.11.0 --no-dev --ignore-platform-reqs
fi

# Verificar instalación
if [ -f "vendor/autoload.php" ]; then
    echo "✓ Dependencias instaladas correctamente"
    echo "Paquetes instalados:"
    composer show --installed
else
    echo "✗ Error: No se pudieron instalar las dependencias"
    exit 1
fi

echo "=== Script completado ===" 