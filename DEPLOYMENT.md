# Guía de Despliegue

## Configuración de Git para Despliegue

Este repositorio incluye configuración específica para manejar ramas divergentes durante el despliegue.

### Comandos de Configuración

Antes del despliegue, ejecutar:

```bash
git config pull.rebase false
git config pull.ff only
git config merge.strategy recursive
git config merge.strategy-option theirs
```

### Scripts de Despliegue

- `pre-deploy.sh`: Configura Git automáticamente
- `deploy-commands.sh`: Ejecuta comandos de despliegue con manejo de conflictos
- `deployment-settings.json`: Configuración para sistemas de despliegue

### Manejo de Conflictos

Si el despliegue falla por ramas divergentes:

1. Ejecutar: `git config pull.rebase false`
2. Ejecutar: `git pull origin main --no-rebase`
3. Si falla: `git reset --hard origin/main`

### Archivos de Configuración

- `.gitattributes`: Configuración de Git para el repositorio
- `deployment-settings.json`: Configuración específica para despliegue
- `deploy-config.json`: Configuración adicional de despliegue 