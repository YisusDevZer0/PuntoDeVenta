# Despliegue en Hostinger

## Problema de Ramas Divergentes

Hostinger está experimentando problemas con ramas divergentes durante el despliegue. Este archivo contiene la solución.

## Solución Automática

### Archivos de Configuración

1. **hostinger-deploy.sh**: Script que configura Git automáticamente
2. **hostinger-config.json**: Configuración específica para Hostinger
3. **.gitattributes**: Configuración de Git para el repositorio

### Comandos de Configuración

Antes del despliegue, ejecutar estos comandos:

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

### Script de Despliegue

Ejecutar el script antes del despliegue:

```bash
chmod +x hostinger-deploy.sh
./hostinger-deploy.sh
```

### Manejo de Conflictos

Si el despliegue falla:

1. Ejecutar: `git config pull.rebase false`
2. Ejecutar: `git pull origin main --no-rebase`
3. Si falla: `git reset --hard origin/main`

## Configuración Automática

El archivo `hostinger-config.json` contiene todos los comandos necesarios para configurar Git automáticamente en Hostinger. 