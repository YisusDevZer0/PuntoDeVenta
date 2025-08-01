#!/bin/bash

# Hook de despliegue que se ejecuta automáticamente
echo "=== CONFIGURACIÓN AUTOMÁTICA DE DESPLIEGUE ==="

# Configurar Git para manejar ramas divergentes
echo "Configurando Git..."
git config pull.rebase false
git config pull.ff only
git config merge.strategy recursive
git config merge.strategy-option theirs

# Verificar configuración
echo "Configuración actual:"
git config --list | grep -E "(pull|merge)"

echo "=== CONFIGURACIÓN COMPLETADA ===" 