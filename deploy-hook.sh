#!/bin/bash

echo "=== Deploy Hook iniciado ==="

# Ejecutar script de limpieza y fix de Composer
if [ -f "auto-fix-composer.sh" ]; then
    echo "Ejecutando auto-fix de Composer..."
    chmod +x auto-fix-composer.sh
    ./auto-fix-composer.sh
    
    if [ $? -eq 0 ]; then
        echo "✓ Auto-fix completado exitosamente"
    else
        echo "✗ Error en auto-fix, intentando limpieza manual..."
        
        # Limpieza manual de emergencia
        rm -rf vendor/
        rm -f composer.lock
        composer install --no-dev --no-interaction --ignore-platform-reqs --prefer-dist
        
        if [ -f "vendor/autoload.php" ]; then
            echo "✓ Instalación manual exitosa"
        else
            echo "✗ Error: No se pudieron instalar las dependencias"
            exit 1
        fi
    fi
else
    echo "Script auto-fix no encontrado, ejecutando limpieza manual..."
    rm -rf vendor/
    rm -f composer.lock
    composer install --no-dev --no-interaction --ignore-platform-reqs
fi

echo "=== Deploy Hook completado ===" 