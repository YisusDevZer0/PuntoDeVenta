#!/bin/bash

echo "=== Instalación de dependencias de Composer ==="

# Verificar versión de PHP
echo "Versión de PHP:"
php -v

# Verificar extensiones de PHP necesarias
echo "Verificando extensiones de PHP..."
php -m | grep -E "(json|mbstring|xml|zip)"

# Limpiar instalación anterior
echo "Limpiando instalación anterior..."
rm -rf vendor/
rm -f composer.lock

# Intentar instalación con diferentes configuraciones
echo "Intentando instalación de dependencias..."

# Primera tentativa: instalación normal
if composer install --no-dev --no-interaction; then
    echo "✓ Instalación exitosa"
    exit 0
fi

echo "Primera tentativa falló, intentando con --ignore-platform-reqs..."

# Segunda tentativa: ignorar requisitos de plataforma
if composer install --no-dev --no-interaction --ignore-platform-reqs; then
    echo "✓ Instalación exitosa con --ignore-platform-reqs"
    exit 0
fi

echo "Segunda tentativa falló, intentando con --prefer-dist..."

# Tercera tentativa: preferir distribuciones
if composer install --no-dev --no-interaction --prefer-dist; then
    echo "✓ Instalación exitosa con --prefer-dist"
    exit 0
fi

echo "Todas las tentativas fallaron. Verificando dependencias específicas..."

# Verificar dependencias específicas
composer show --installed

echo "✗ Error: No se pudieron instalar las dependencias"
exit 1 