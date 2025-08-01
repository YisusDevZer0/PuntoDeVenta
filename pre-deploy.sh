#!/bin/bash

# Script de pre-despliegue para configurar Git automáticamente
echo "Configurando Git para el despliegue..."

# Configurar Git para manejar ramas divergentes
git config pull.rebase false
git config pull.ff only
git config merge.ff no

# Configurar estrategia de merge
git config merge.strategy recursive
git config merge.strategy-option theirs

# Configurar para aceptar automáticamente cambios del remoto
git config pull.rebase false
git config pull.ff only

echo "Git configurado para el despliegue." 