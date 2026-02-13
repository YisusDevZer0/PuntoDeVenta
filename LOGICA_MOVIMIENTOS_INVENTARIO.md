# Lógica de Movimientos de Inventario

Documento que describe cómo se manejan los **movimientos de inventario** en el proyecto, para poder reutilizar esta lógica-funcionalidad en otro proyecto.

---

## 1. Resumen

Un **movimiento de inventario** es un registro inmutable que representa un cambio de stock en una sucursal: entradas, salidas, ajustes manuales o correcciones por conteo. Cada movimiento guarda el stock antes y después, el tipo, la referencia al origen (venta, traspaso, conteo, manual) y datos del producto (por código de barras o folio).

**Principios:**
- Todo cambio de stock debe quedar registrado como movimiento.
- Los movimientos no se editan ni eliminan; son historial.
- La cantidad es **positiva** para entradas y **negativa** para salidas (en algunos flujos se usa signo y en otros el tipo `entry`/`exit` lo indica).

---

## 2. Modelo de datos

### 2.1 Entidad `InventoryMovement` (inventory_movements)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | UUID | Clave primaria |
| `branch_id` | UUID | Sucursal donde ocurre el movimiento |
| `user_id` | UUID | Usuario que registra (o sistema) |
| `movement_type` | string | `entry`, `exit`, `adjustment`, `count_correction` |
| `reference_type` | string (opcional) | Origen: `count`, `manual`, `sale`, `purchase`, `transfer` |
| `reference_id` | string (opcional) | ID del conteo, venta, traspaso, etc. |
| `folio_prod_stock` | string (opcional) | Identificador producto en sistema legacy |
| `cod_barra` | string (opcional) | Código de barras |
| `nombre_prod` | string (opcional) | Nombre del producto (denormalizado) |
| `product_id` | UUID (opcional) | FK al producto en sistema nuevo |
| `quantity` | int | Cantidad del movimiento: positivo = entrada, negativo = salida |
| `stock_before` | int | Stock en sucursal antes del movimiento |
| `stock_after` | int | Stock en sucursal después del movimiento |
| `reason` | string (opcional) | Motivo (ej. "Ajuste por merma", "Venta") |
| `notes` | text (opcional) | Notas adicionales |
| `created_at` | datetime | Fecha/hora del movimiento |

### 2.2 Tipos de movimiento (`movement_type`)

- **`entry`**: Entrada de mercancía (compra, devolución, traspaso recibido, ajuste positivo).
- **`exit`**: Salida (venta, traspaso enviado, ajuste negativo).
- **`adjustment`**: Ajuste manual; la cantidad puede ser positiva o negativa.
- **`count_correction`**: Corrección por conteo físico; la cantidad es la diferencia (contado - esperado).

### 2.3 Tipos de referencia (`reference_type`)

- **`count`**: Generado al sincronizar un conteo de inventario (`reference_id` = ID del conteo).
- **`manual`**: Creado desde pantalla de ajustes (entrada/salida/ajuste manual).
- **`sale`**: Por venta o cancelación de venta (`reference_id` = ID de la venta).
- **`transfer`**: Por traspaso completado (`reference_id` = ID del traspaso).
- **`purchase`**: Por compra (si se implementa).

---

## 3. Flujos que generan movimientos

### 3.1 Movimiento manual (ajuste de inventario)

**Cuándo:** El usuario registra una entrada, salida o ajuste desde la pantalla de ajustes.

**Lógica:**
1. Validar `movement_type` ∈ `['entry', 'exit', 'adjustment', 'count_correction']`.
2. Obtener stock actual del producto en la sucursal (desde tu almacén o tabla de stock).
3. Calcular `stock_after`:
   - **entry:** `stock_after = stock_before + abs(quantity)`
   - **exit:** `stock_after = stock_before - abs(quantity)`; si `stock_after < 0` → error "Stock insuficiente".
   - **adjustment / count_correction:** `stock_after = stock_before + quantity` (quantity puede ser negativo).
4. Actualizar el stock en la tabla de almacén/sucursal.
5. Insertar un registro en `inventory_movements` con `reference_type='manual'` (o el que corresponda).

**Datos mínimos para crear:** `branch_id`, `movement_type`, identificador del producto (`cod_barra` o `folio_prod_stock` o `product_id`), `quantity`, `reason`. Opcional: `reference_type`, `reference_id`, `notes`.

---

### 3.2 Movimientos por conteo de inventario

**Cuándo:** Se completa un conteo físico y se “sincroniza” con el sistema (en este proyecto: endpoint `POST /counts/{count_id}/sync-to-pos`).

**Lógica:**
1. Solo se generan movimientos para **detalles del conteo con diferencia distinta de cero** (`difference != 0`).
2. Por cada detalle con diferencia:
   - `quantity = difference` (contado - esperado; puede ser positivo o negativo).
   - `stock_before` = stock esperado (o el leído del almacén antes de actualizar).
   - `stock_after` = stock contado (valor que quedará en almacén).
3. Actualizar el stock del producto en la sucursal al valor contado.
4. Insertar movimiento con:
   - `movement_type = 'count_correction'`
   - `reference_type = 'count'`
   - `reference_id = count_id`
   - `reason` y `notes` descriptivos (ej. "Corrección automática por conteo").

Si en tu proyecto no tienes “conteo” como entidad, puedes omitir este flujo o adaptarlo a tu proceso de conciliación físico/sistema.

---

### 3.3 Movimientos por venta

**Cuándo:**
- Al **crear una venta**: se genera un movimiento de **salida**.
- Al **cancelar una venta**: se genera un movimiento de **entrada** (devolución).

**Regla importante:** Una misma venta solo debe tener **un** movimiento asociado (por venta activa = salida; por cancelación = entrada). Antes de crear, comprobar que no exista ya un movimiento con `reference_type='sale'` y `reference_id = sale.id`.

**Lógica por venta nueva:**
- `movement_type = 'exit'`
- `reference_type = 'sale'`, `reference_id = sale.id`
- `quantity = -cantidad_venta` (negativo = salida)
- `reason = "Venta"`, `notes` con folio o ID de venta.
- Opcional: rellenar `stock_before` y `stock_after` si tienes stock en tiempo real.

**Lógica por cancelación:**
- `movement_type = 'entry'`
- Mismo `reference_type` y `reference_id` (venta cancelada).
- `quantity = +cantidad_venta` (positivo = devolución).
- `reason = "Cancelación de venta"`.

---

### 3.4 Movimientos por traspaso

**Cuándo:** El traspaso pasa a estado **completado** (no al crearlo ni al enviar).

**Lógica:** Por cada **línea del traspaso** se crean **dos** movimientos:

1. **Salida en sucursal origen:**
   - `branch_id = origin_branch_id`
   - `movement_type = 'exit'`
   - `reference_type = 'transfer'`, `reference_id = transfer.id`
   - `quantity = -detail.quantity` (negativo = salida)
   - Producto: `cod_barra`, `folio_prod_stock`, `nombre_prod`, `product_id` del detalle.

2. **Entrada en sucursal destino:**
   - `branch_id = destination_branch_id`
   - `movement_type = 'entry'`
   - `reference_type = 'transfer'`, `reference_id = transfer.id`
   - `quantity = quantity_received` (o `detail.quantity` si no hay “recibido”)
   - Mismos datos de producto.

Así el historial queda por sucursal y por tipo (entrada/salida).

---

## 4. Reglas de negocio (resumen)

1. **Tipos válidos:** Solo aceptar `entry`, `exit`, `adjustment`, `count_correction`.
2. **Salidas:** No permitir salida si `stock_before - abs(quantity) < 0`; devolver error de stock insuficiente.
3. **Entradas:** Siempre `quantity > 0` o usar `abs(quantity)` al calcular.
4. **Unicidad por referencia:** Para ventas, evitar duplicar movimientos con el mismo `reference_type` + `reference_id`.
5. **Inmutabilidad:** Los movimientos no se modifican ni borran; solo consulta e historial.
6. **Stock actual:** Si mantienes una tabla de “stock por sucursal/producto”, actualizarla **después** de calcular `stock_before`/`stock_after` y **antes o en la misma transacción** que el insert del movimiento, para mantener consistencia.

---

## 5. API sugerida (para otro proyecto)

Puedes exponer algo equivalente a:

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/inventory/movements` | Crear movimiento (manual o desde otro módulo). Body: branch_id, movement_type, product id/cod_barra, quantity, reason, reference_type, reference_id, etc. |
| GET | `/inventory/movements` | Listar movimientos con filtros: branch_id, movement_type, cod_barra, fechas, paginación (page, per_page). |
| GET | `/inventory/movements/{id}` | Obtener un movimiento por ID. |

**Filtros útiles en listado:** sucursal, tipo de movimiento, código de barras, rango de fechas (`created_at`).

---

## 6. Reportes que usan movimientos

En este proyecto los movimientos se usan para:

1. **Rotación de inventario:** Agrupar movimientos por producto en un período y contar entradas, salidas, ajustes; ordenar por cantidad de movimientos o por último movimiento.
2. **Movimientos por período:** Agrupar por fecha y sumar cantidades/tipos (entradas, salidas, ajustes, correcciones por conteo).
3. **Productos sin movimiento:** Productos con stock > 0 cuyo último movimiento sea anterior a X días (útil para inventario obsoleto o lento).
4. **Cierres / valoración:** En cierres de período se recorren los movimientos para reconstruir stock inicial/final por producto y valorar.

En otro proyecto puedes reutilizar la misma idea: consultar `inventory_movements` por `branch_id` y fechas, agrupar por producto o por día, y calcular totales o “último movimiento”.

---

## 7. Integración con sistema legacy (opcional)

En este proyecto existe integración con una base **Stock_POS** (legacy): al crear movimientos manuales o por conteo se actualiza también la tabla legacy `Stock_POS` (campo tipo `Existencias_R`) por sucursal y producto (`Cod_Barra` / `Folio_Prod_Stock`).  
Para **otro proyecto** puedes:

- No tener legacy: solo tu tabla de movimientos y tu tabla de stock.
- Tener un solo almacén: actualizar stock en tu BD y registrar el movimiento en `inventory_movements`.
- Tener legacy: después de actualizar tu stock (y opcionalmente el legacy), insertar el movimiento con los mismos `stock_before` y `stock_after` que uses en tu sistema.

---

## 8. Esquemas de ejemplo (request/response)

**Crear movimiento (request):**
```json
{
  "branch_id": "uuid-sucursal",
  "movement_type": "entry",
  "reference_type": "manual",
  "folio_prod_stock": "opcional",
  "cod_barra": "7501234567890",
  "nombre_prod": "Producto ejemplo",
  "product_id": "uuid-opcional",
  "quantity": 10,
  "reason": "Ajuste por inventario físico",
  "notes": "Conteo mensual"
}
```

**Respuesta (movimiento creado):**
Incluir todos los campos del modelo, en especial `id`, `stock_before`, `stock_after`, `created_at`, `user_id`.

---

## 9. Ubicación en este proyecto (referencia)

- **Modelo:** `Microservices/FarmacitasCore/app/models/inventory.py` → `InventoryMovement`
- **Schemas:** `Microservices/FarmacitasCore/app/schemas/inventory.py` → `InventoryMovementCreate`, `InventoryMovementResponse`, `InventoryMovementListResponse`
- **API movimientos:** `Microservices/FarmacitasCore/app/api/v1/inventory.py` → endpoints bajo `/inventory/movements` y helper `_generate_movements_from_count`
- **Ventas → movimientos:** `Microservices/FarmacitasCore/app/services/sale_service.py` → `_generate_inventory_movement`, `sync_sales_with_count`, `generate_movements_for_sales`
- **Traspasos → movimientos:** `Microservices/FarmacitasCore/app/services/transfer_service.py` → `_generate_inventory_movements`
- **Migración tabla:** `Microservices/FarmacitasCore/alembic/versions/add_inventory_movements_table.py`

Con esta lógica puedes replicar el manejo de movimientos de inventario en otro proyecto, adaptando modelos, APIs y si aplica la integración con legacy o conteos.
