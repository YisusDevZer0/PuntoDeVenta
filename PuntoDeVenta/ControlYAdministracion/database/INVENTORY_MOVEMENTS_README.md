# Inventory Movements - Instalación

Registro de movimientos de inventario para ventas, ingresos, conteos, traspasos e inventarios de sucursales.

## Orden de ejecución

1. **inventory_movements_create_table.sql** – Crear la tabla
2. **inventory_movements_trigger_ventas.sql** – Ventas (salidas)
3. **inventory_movements_trigger_ingresos.sql** – Ingresos de mercancía
4. **inventory_movements_trigger_conteos.sql** – Correcciones por conteo
5. **inventory_movements_trigger_traspasos.sql** – Traspasos (origen + destino)
6. **inventory_movements_trigger_traspasos_recepcionados.sql** – Traspasos recepcionados
7. **inventory_movements_trigger_inventarios_sucursales.sql** – Inventarios de sucursales

## Cómo ejecutar

**Desde consola MySQL:**
```bash
cd PuntoDeVenta/ControlYAdministracion/database
mysql -u usuario -p u858848268_doctorpez < inventory_movements_create_table.sql
mysql -u usuario -p u858848268_doctorpez < inventory_movements_trigger_ventas.sql
# ... etc.
```

**Desde phpMyAdmin:** Ir a la base de datos → SQL → pegar y ejecutar cada archivo en orden.

## Verificar

Después de una venta:
```sql
SELECT * FROM inventory_movements ORDER BY id DESC LIMIT 5;
```

## Revertir

**Si algo sale mal, ejecuta el script de revert:**

```bash
mysql -u usuario -p u858848268_doctorpez < inventory_movements_REVERT.sql
```

Restaura los 7 triggers a su versión original (sin INSERT en inventory_movements). La tabla `inventory_movements` no se elimina automáticamente.
