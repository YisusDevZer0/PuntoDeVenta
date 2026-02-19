# Configuración Inventario por Turnos

Ejecutar **una vez** el script SQL para crear las tablas de configuración:

```bash
mysql -u usuario -p nombre_base_datos < inventario_turnos_config_periodos.sql
```

O desde phpMyAdmin / cliente MySQL: ejecutar el contenido de `inventario_turnos_config_periodos.sql`.

## Tablas creadas

- **Inventario_Turnos_Periodos**: periodos (fecha inicio–fin) en que está permitido el inventario por turnos por sucursal (o global).
- **Inventario_Turnos_Config_Sucursal**: por sucursal (o global): máx. turnos por día, máx. productos por turno.
- **Inventario_Turnos_Config_Empleado**: por empleado/sucursal: máx. turnos por día, máx. productos por turno (0 = usar valor de sucursal).

Si no ejecutas el script, el módulo de inventario por turnos sigue funcionando como antes (sin validación de periodo ni límites configurables).
