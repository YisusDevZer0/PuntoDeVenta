#!/bin/bash

# Script específico para Hostinger
echo "=== CONFIGURACIÓN HOSTINGER ==="

# Configurar Git para Hostinger
git config --global pull.rebase false
git config --global pull.ff only
git config --global merge.strategy recursive
git config --global merge.strategy-option theirs

# Configuración local también
git config pull.rebase false
git config pull.ff only
git config merge.strategy recursive
git config merge.strategy-option theirs

echo "Git configurado para Hostinger"
echo "=== CONFIGURACIÓN COMPLETADA ===" 