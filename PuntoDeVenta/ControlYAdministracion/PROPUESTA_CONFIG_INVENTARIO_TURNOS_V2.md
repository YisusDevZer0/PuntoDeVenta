# Propuesta v2: Configuración por **periodo** y turnos por día

## Ajustes respecto a la propuesta anterior

- **Periodo** en lugar de “días de la semana”: la ventana en que se permite inventario por turnos es un **periodo** (fecha inicio – fecha fin). Así se puede conectar después con otro sistema (que envíe o sincronice periodos).
- **Turno / veces por día**: lo define el administrador:
  - Un **turno** = una sesión de conteo (ya existe: una fila en `Inventario_Turnos`).
  - El admin configura **cuántas veces** se puede hacer ese inventario por día (por sucursal y, opcionalmente, por empleado).

---

## 1. Concepto de **periodo**

- **Periodo** = rango de fechas en el que el inventario por turnos está **habilitado** para una sucursal (o global).
- Si “hoy” no cae dentro de ningún periodo activo para esa sucursal → no se puede iniciar turno (mensaje claro al usuario).
- Diseño pensado para **integración futura**: el otro sistema podría crear/actualizar periodos (por API o por BD), o este sistema podría tener un “código de periodo externo” para empatar.

---

## 2. Modelo de datos revisado

### 2.1 Periodos de inventario (por sucursal o global)

**Tabla: `Inventario_Turnos_Periodos`**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `ID_Periodo` | INT PK AUTO_INCREMENT | |
| `Fk_sucursal` | INT NOT NULL | Sucursal (0 = aplica a todas si no hay periodo por sucursal) |
| `Fecha_Inicio` | DATE NOT NULL | Inicio del periodo |
| `Fecha_Fin` | DATE NOT NULL | Fin del periodo |
| `Nombre_Periodo` | VARCHAR(100) NULL | Ej. "Semana 1 Feb 2025", "Ciclo 01" (opcional, para mostrar en admin) |
| `Codigo_Externo` | VARCHAR(50) NULL | Para conectar con otro sistema después (ej. ID del periodo en sistema externo) |
| `Activo` | TINYINT(1) DEFAULT 1 | 1=activo, 0=desactivado |
| `Actualizado_Por` | VARCHAR(250) NULL | |
| `Fecha_Actualizacion` | TIMESTAMP | |

- **Regla**: para una sucursal, “hoy” debe estar dentro de al menos un periodo activo con `Fecha_Inicio <= hoy <= Fecha_Fin`. Si `Fk_sucursal = 0`, ese periodo aplica como respaldo cuando la sucursal no tiene su propio periodo.
- Se pueden tener varios periodos (ej. uno por semana o por quincena); al iniciar turno se valida si la fecha actual cae en alguno.

### 2.2 Configuración por sucursal (turnos por día y productos por turno)

**Tabla: `Inventario_Turnos_Config_Sucursal`**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `ID_Config` | INT PK AUTO_INCREMENT | |
| `Fk_sucursal` | INT NOT NULL | (0 = valores por defecto globales) |
| `Max_Turnos_Por_Dia` | INT DEFAULT 0 | **Cuántas veces** se puede hacer inventario por día en esa sucursal. 0 = sin límite |
| `Max_Productos_Por_Turno` | INT DEFAULT 50 | Productos a contar por turno (el que hoy está fijo en 50) |
| `Activo` | TINYINT(1) DEFAULT 1 | |
| `Actualizado_Por` | VARCHAR(250) NULL | |
| `Fecha_Actualizacion` | TIMESTAMP | |

- **Turno** ya está definido en el sistema: una fila en `Inventario_Turnos` = un turno.  
- **Max_Turnos_Por_Dia**: el admin define “cuántas veces por día” se puede iniciar ese turno (por sucursal).

### 2.3 Configuración por empleado (opcional)

**Tabla: `Inventario_Turnos_Config_Empleado`**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `ID_Config` | INT PK AUTO_INCREMENT | |
| `Fk_usuario` | INT NOT NULL | Id_PvUser (Usuarios_PV) |
| `Fk_sucursal` | INT NOT NULL | Sucursal a la que aplica (0 = todas) |
| `Max_Turnos_Por_Dia` | INT DEFAULT 0 | **Cuántas veces** puede ese empleado iniciar turno por día. 0 = usar límite de sucursal |
| `Max_Productos_Por_Turno` | INT DEFAULT 0 | 0 = usar límite de la sucursal |
| `Activo` | TINYINT(1) DEFAULT 1 | |
| `Actualizado_Por` | VARCHAR(250) NULL | |
| `Fecha_Actualizacion` | TIMESTAMP | |

- Si hay límite por empleado, se usa ese; si no (0), se usa el de la sucursal.

---

## 3. Reglas de negocio al **iniciar turno**

En `gestion_turnos.php` (acción `iniciar`), en este orden:

1. **Periodo vigente**  
   - Buscar periodo activo donde `Fecha_Inicio <= CURDATE() <= Fecha_Fin` y (`Fk_sucursal = sucursal_del_usuario` O `Fk_sucursal = 0`).  
   - Si no hay ninguno → *"El inventario por turnos no está habilitado para esta sucursal en la fecha actual. Verifica los periodos configurados."*

2. **Límite de turnos por día – sucursal**  
   - Si `Max_Turnos_Por_Dia` > 0 en config de la sucursal: contar cuántos turnos hay hoy en esa sucursal (en `Inventario_Turnos`, misma sucursal, misma fecha).  
   - Si ya se alcanzó → *"Se alcanzó el máximo de turnos de inventario permitidos hoy para esta sucursal."*

3. **Límite de turnos por día – empleado**  
   - Si existe config del empleado con `Max_Turnos_Por_Dia` > 0: contar turnos de ese usuario (y sucursal si aplica) en la fecha de hoy.  
   - Si ya se alcanzó → *"Has alcanzado el máximo de turnos de inventario permitidos para hoy."*

4. **Límite de productos por turno**  
   - Origen: config empleado si `Max_Productos_Por_Turno` > 0; si no, config sucursal; si no, 50.  
   - Ese valor se guarda en `Inventario_Turnos.Limite_Productos` al crear el turno.

---

## 4. Integración futura con otro sistema

- **Periodo**:  
  - El otro sistema puede insertar/actualizar filas en `Inventario_Turnos_Periodos` (por API o BD).  
  - `Codigo_Externo` permite empatar con el ID o código del periodo en el sistema externo.  
  - No es obligatorio usarlo al inicio; se puede dejar NULL y solo usar fechas y sucursal.

- **Turnos por día**:  
  - Sigue siendo configuración que define el admin en este sistema (cuántas veces por día por sucursal/empleado). Si en el futuro el otro sistema debe imponer el número, se podría añadir un endpoint que actualice `Inventario_Turnos_Config_Sucursal` / `Inventario_Turnos_Config_Empleado`.

---

## 5. Resumen de cambios en pantalla (admin)

- **Gestión de Conteos** (o pantalla de configuración):
  - **Periodos**: ABM de periodos por sucursal (o global): Fecha inicio, Fecha fin, nombre opcional, código externo opcional.
  - **Config sucursal**: por sucursal (o global): “Máx. turnos por día”, “Máx. productos por turno”.
  - **Config empleado** (opcional): por usuario/sucursal: “Máx. turnos por día”, “Máx. productos por turno”.
  - Consulta de productos contados y, si se desea, botón “Liberar productos” como acción secundaria.

---

## 6. Verificación contigo

- **Periodo**: ¿Te encaja que sea solo “rango de fechas” (Fecha_Inicio, Fecha_Fin) por sucursal (más global con Fk_sucursal=0), con campo opcional `Codigo_Externo` para el otro sistema?
- **Turno / veces por día**: ¿Confirmas que el “turno” es el que ya existe (una sesión = una fila en `Inventario_Turnos`) y que el admin solo define “cuántas veces se puede hacer ese inventario por día” (por sucursal y/o por empleado)?

Cuando confirmes estos dos puntos, se puede bajar a cambios concretos en BD, API y pantallas.
