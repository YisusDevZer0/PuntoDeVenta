#!/bin/bash

# Script para resolver problemas de despliegue en Hostinger por SSH
echo "=== RESOLUCIÓN DE PROBLEMAS DE DESPLIEGUE ==="

# Verificar si estamos en un repositorio Git
if [ ! -d ".git" ]; then
    echo "Error: No se encontró un repositorio Git"
    exit 1
fi

echo "1. Configurando Git..."
# Configuración global
git config --global pull.rebase false
git config --global pull.ff only
git config --global merge.strategy recursive
git config --global merge.strategy-option theirs

# Configuración local
git config pull.rebase false
git config pull.ff only

echo "2. Obteniendo cambios del remoto..."
git fetch origin

echo "3. Intentando sincronizar ramas..."
if git pull origin main --no-rebase; then
    echo "✅ Pull exitoso"
else
    echo "⚠️  Conflicto detectado, aplicando reset hard..."
    git reset --hard origin/main
    echo "✅ Reset completado"
fi

echo "4. Verificando configuración..."
echo "Configuración de pull:"
git config --list | grep pull

echo "Configuración de merge:"
git config --list | grep merge

echo "5. Estado final:"
git status

echo "=== RESOLUCIÓN COMPLETADA ==="
echo "Ahora puedes intentar el despliegue desde el panel de Hostinger" 