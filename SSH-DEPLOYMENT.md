# Resolución del Problema de Despliegue por SSH

## Problema Actual
Hostinger sigue teniendo problemas con ramas divergentes durante el despliegue automático.

## Solución: Conexión SSH

### 1. Conectarse por SSH a Hostinger

```bash
ssh usuario@tu-dominio.com
# o
ssh usuario@IP-del-servidor
```

### 2. Navegar al Directorio del Proyecto

```bash
cd public_html
# o
cd /home/usuario/public_html
```

### 3. Configurar Git Manualmente

```bash
# Configuración global
git config --global pull.rebase false
git config --global pull.ff only
git config --global merge.strategy recursive
git config --global merge.strategy-option theirs

# Configuración local
git config pull.rebase false
git config pull.ff only
```

### 4. Resolver el Problema Actual

```bash
# Obtener cambios del remoto
git fetch origin

# Intentar pull con configuración específica
git pull origin main --no-rebase

# Si falla, hacer reset hard
git reset --hard origin/main
```

### 5. Verificar Configuración

```bash
# Verificar configuración actual
git config --list | grep pull
git config --list | grep merge

# Verificar estado
git status
```

### 6. Probar Despliegue

Después de configurar Git, intentar el despliegue nuevamente desde el panel de Hostinger.

## Comandos de Verificación

```bash
# Verificar ramas
git branch -a

# Verificar remotes
git remote -v

# Verificar configuración
git config --list
```

## Script de Configuración Automática

Si tienes acceso SSH, puedes ejecutar este script:

```bash
#!/bin/bash
echo "Configurando Git para Hostinger..."
git config --global pull.rebase false
git config --global pull.ff only
git config --global merge.strategy recursive
git config --global merge.strategy-option theirs
git config pull.rebase false
git config pull.ff only
echo "Configuración completada"
``` 