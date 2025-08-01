#!/bin/bash

# Script de despliegue para manejar ramas divergentes
echo "Configurando Git para manejar ramas divergentes..."

# Configurar Git para usar merge en lugar de rebase
git config pull.rebase false
git config pull.ff only

# Obtener los últimos cambios
echo "Obteniendo cambios del repositorio remoto..."
git fetch origin

# Intentar hacer merge automáticamente
echo "Sincronizando ramas..."
git pull origin main --no-rebase

# Si hay conflictos, hacer reset hard al origin/main
if [ $? -ne 0 ]; then
    echo "Resolviendo conflictos con reset hard..."
    git reset --hard origin/main
fi

echo "Despliegue completado exitosamente!" 