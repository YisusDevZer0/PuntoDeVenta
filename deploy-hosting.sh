#!/bin/bash

# Script de despliegue para hosting
echo "=== DESPLIEGUE AUTOMÁTICO ==="

# Configurar Git para evitar conflictos de ramas divergentes
echo "Configurando Git..."
git config --global pull.rebase false
git config --global pull.ff only
git config --global merge.strategy recursive
git config --global merge.strategy-option theirs

# Configuración local
git config pull.rebase false
git config pull.ff only

# Obtener cambios del remoto
echo "Obteniendo cambios..."
git fetch origin

# Intentar pull con configuración específica
echo "Sincronizando ramas..."
if git pull origin main --no-rebase; then
    echo "Pull exitoso"
else
    echo "Conflicto detectado, aplicando reset hard..."
    git reset --hard origin/main
    echo "Reset completado"
fi

# Verificar estado final
echo "Estado final:"
git status

echo "=== DESPLIEGUE COMPLETADO ===" 