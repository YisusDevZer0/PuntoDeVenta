#!/bin/bash

# Comandos específicos para el despliegue
echo "Ejecutando comandos de despliegue..."

# Configurar Git
git config pull.rebase false
git config pull.ff only

# Obtener cambios del remoto
git fetch origin

# Intentar pull con configuración específica
if ! git pull origin main --no-rebase; then
    echo "Conflicto detectado, aplicando reset hard..."
    git reset --hard origin/main
    echo "Reset completado."
fi

# Verificar estado final
git status

echo "Despliegue completado." 