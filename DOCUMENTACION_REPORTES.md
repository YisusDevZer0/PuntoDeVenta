# Documentación Completa de Reportes de Ventas y Administración

## Índice
1. [Introducción](#introducción)
2. [Reportes Disponibles](#reportes-disponibles)
3. [Estructura de Archivos](#estructura-de-archivos)
4. [APIs y Controladores](#apis-y-controladores)
5. [Funcionalidades por Reporte](#funcionalidades-por-reporte)
6. [Estructura de Base de Datos](#estructura-de-base-de-datos)
7. [Dependencias y Tecnologías](#dependencias-y-tecnologías)
8. [Guía de Implementación](#guía-de-implementación)

---

## Introducción

Este documento describe todos los módulos de reportes disponibles en el sistema de Punto de Venta. Los reportes proporcionan análisis detallados de ventas, inventarios, servicios, devoluciones y otros aspectos administrativos del negocio.

**Ubicación Principal:** `PuntoDeVenta/ControlYAdministracion/`

---

## Reportes Disponibles

### 1. Reporte de Ventas Generales
**Archivo:** `ReporteVentas.php`  
**Controlador:** `Controladores/VentasDelDia.php`  
**JavaScript:** `js/ReporteVentas.js`

**Descripción:**  
Reporte principal que muestra todas las ventas realizadas con filtros por sucursal, fecha, mes y vendedor.

**Características:**
- Estadísticas generales (Total ventas, Ingresos, Sucursales, Promedio)
- Filtros avanzados (Sucursal, Mes, Rango de fechas, Vendedor)
- Tabla interactiva con DataTables
- Exportación a Excel

**Campos mostrados:**
- Código de barras
- Nombre del producto
- Sucursal
- Folio del ticket
- Turno
- Cantidad vendida
- Total de venta
- Importes (efectivo, tarjeta, crédito)
- Descuentos aplicados
- Forma de pago
- Cliente
- Fecha de venta
- Vendedor

---

### 2. Reporte de Ventas Totales por Fechas
**Archivo:** `ReporteVentasTotalesPorFechas.php`  
**Controlador:** `Controladores/ArrayVentasPorTotalesPorFechas.php`  
**JavaScript:** `js/VentasTotalesPorFechaYSucursal.js`

**Descripción:**  
Reporte detallado de ventas filtrado por rango de fechas específico.

**Características:**
- Filtros por mes y año
- Filtros por sucursal
- Filtros por rango de fechas
- Exportación a Excel/CSV

---

### 3. Reporte de Ventas Totales
**Archivo:** `ReporteDeVentasTotales.php`  
**Controlador:** `Controladores/ArrayVentasSimple.php`

**Descripción:**  
Vista simplificada de ventas totales por vendedor.

---

### 4. Reporte de Rendimiento por Sucursal
**Archivo:** `ReporteSucursales.php`  
**Controlador:** `Controladores/ArrayDeReporteSucursales.php`  
**JavaScript:** `js/ReporteSucursales.js`

**Descripción:**  
Análisis comparativo del rendimiento de todas las sucursales.

**Características:**
- Estadísticas por sucursal
- Total de ventas por sucursal
- Total de importe
- Total de descuentos
- Número de transacciones
- Promedio por venta
- Productos vendidos
- Clientes atendidos
- Última venta registrada

**Filtros:**
- Rango de fechas (inicio y fin)
- Exportación a Excel

**Estadísticas mostradas:**
- Total de sucursales analizadas
- Total de ventas
- Mejor sucursal
- Promedio por sucursal

---

### 5. Reporte de Productos Más Vendidos
**Archivo:** `ReporteProductosMasVendidos.php`  
**Controlador:** `Controladores/ArrayDeReporteProductosMasVendidos.php`  
**JavaScript:** `js/ReporteProductosMasVendidos.js`  
**Exportación:** `Controladores/exportar_reporte_productos_mas_vendidos.php`

**Descripción:**  
Ranking de productos ordenados por cantidad vendida.

**Características:**
- Ranking de productos
- Filtros por fecha y sucursal
- Top 10, 25, 50 o 100 productos
- Estadísticas de rendimiento

**Campos mostrados:**
- Ranking
- ID del producto
- Nombre del producto
- Código de barras
- Categoría
- Total vendido (cantidad)
- Total importe
- Promedio por venta
- Número de ventas
- Última venta

**Estadísticas:**
- Total de productos analizados
- Total de ventas
- Producto estrella
- Porcentaje del Top 10

---

### 6. Reporte de Ventas por Producto
**Archivo:** `ReportePorProducto.php`  
**Controlador:** `Controladores/ArrayDeReportePorProducto.php`  
**JavaScript:** `js/ReportePorProductos.js`  
**Exportación:** `Controladores/exportar_reporte_producto.php`

**Descripción:**  
Análisis detallado de ventas agrupadas por producto individual.

**Características:**
- Filtros por fecha y sucursal
- Información completa del producto
- Precios de venta y compra
- Existencias actuales

**Campos mostrados:**
- ID del producto
- Código de barras
- Nombre del producto
- Tipo
- Sucursal
- Precio de venta
- Precio de compra
- Existencias
- Total vendido
- Total importe
- Total venta
- Total descuento
- Número de ventas
- Vendedor
- Primera venta
- Última venta

**Estadísticas:**
- Total de productos vendidos
- Total de ventas
- Unidades vendidas
- Promedio por venta

---

### 7. Reporte por Forma de Pago
**Archivo:** `ReporteFormaDePago.php`  
**Controlador:** `Controladores/ArrayDeReporteFormaDePago.php`  
**JavaScript:** `js/ReporteFormaDePago.js`  
**Exportación:** `Controladores/exportar_reporte_forma_pago.php`

**Descripción:**  
Análisis de ventas agrupadas por método de pago (efectivo, tarjeta, crédito, etc.).

**Características:**
- Desglose por forma de pago
- Porcentajes del total
- Estadísticas comparativas

**Campos mostrados:**
- Forma de pago
- Total de ventas
- Total importe
- Total descuento
- Número de transacciones
- Promedio por venta
- Porcentaje del total
- Última transacción

**Estadísticas:**
- Total de formas de pago
- Total de ventas
- Forma de pago más usada
- Porcentaje de efectivo

---

### 8. Reporte Anual de Ventas
**Archivo:** `ReportesAnuales.php`  
**Controlador:** `Controladores/ArrayDeReportesAnuales.php`  
**JavaScript:** `js/ReportesAnuales.js`  
**Exportación:** `Controladores/exportar_reporte_anual.php`

**Descripción:**  
Análisis de ventas agrupadas por períodos anuales (mes, trimestre o semana).

**Características:**
- Vista anual consolidada
- Agrupación por mes, trimestre o semana
- Comparativa de períodos

**Campos mostrados:**
- Período
- Total de ventas
- Total importe
- Total descuento
- Número de transacciones
- Promedio por venta
- Productos vendidos
- Clientes atendidos

**Filtros:**
- Año (últimos 5 años)
- Sucursal
- Tipo de período (mes, trimestre, semana)

**Estadísticas:**
- Total de períodos analizados
- Total de ventas anual
- Promedio por período
- Mejor período

---

### 9. Reporte de Servicios
**Archivo:** `ReporteServicios.php`  
**Controlador:** `Controladores/ArrayDeReportePorServicios.php`  
**JavaScript:** `js/ReporteServicios.js`  
**Exportación:** `Controladores/exportar_reporte_servicios.php`

**Descripción:**  
Análisis de servicios vendidos (distintos de productos físicos).

**Características:**
- Filtros por fecha y sucursal
- Información completa del servicio

**Campos mostrados:**
- ID del servicio
- Código de barras
- Nombre del servicio
- Tipo de servicio
- Sucursal
- Precio de venta
- Precio de compra
- Total vendido
- Total importe
- Total venta
- Total descuento
- Número de ventas
- Vendedor
- Primera venta
- Última venta

**Estadísticas:**
- Total de servicios vendidos
- Total de ventas
- Clientes atendidos
- Promedio por servicio

---

### 10. Reporte de Ventas por Vendedor
**Archivo:** `TotalesDeVentaPorVendedor.php`  
**Controlador:** `Controladores/ArrayDeReporteVentasPorVendedor.php`  
**JavaScript:** `js/ReporteVentasPorVendedor.js`  
**Exportación:** `Controladores/exportar_reporte_ventas_vendedor.php`

**Descripción:**  
Análisis de rendimiento individual de cada vendedor.

**Características:**
- Ranking de vendedores
- Comparativa de rendimiento
- Estadísticas individuales

**Campos mostrados:**
- Vendedor
- Sucursal
- Total de ventas
- Total importe
- Total descuento
- Número de transacciones
- Promedio por venta
- Primera venta
- Última venta

**Filtros:**
- Rango de fechas
- Sucursal

**Estadísticas:**
- Total de vendedores activos
- Total de ventas
- Total de transacciones
- Promedio por vendedor

---

### 11. Reporte de Devoluciones
**Archivo:** `ReportesDevoluciones.php`  
**JavaScript:** Incluido en el mismo archivo

**Descripción:**  
Sistema completo de reportes de devoluciones con múltiples tipos de análisis.

**Tipos de Reporte:**
1. **Reporte General:** Lista de devoluciones con información básica
2. **Reporte Detallado:** Información completa de cada producto devuelto
3. **Estadísticas:** Métricas generales de devoluciones
4. **Por Tipos:** Agrupación por tipo de devolución

**Características:**
- Múltiples tipos de reporte
- Gráficos interactivos (Chart.js)
- Estadísticas en tiempo real
- Exportación a Excel

**Funciones PHP:**
- `generarReporteGeneral()`: Reporte básico de devoluciones
- `generarReporteDetallado()`: Reporte con detalles de productos
- `generarEstadisticas()`: Métricas generales
- `obtenerTiposDevolucionStats()`: Estadísticas por tipo

**Campos del Reporte General:**
- Folio
- Fecha
- Sucursal
- Usuario
- Total de productos
- Total de unidades
- Valor total
- Estatus

**Campos del Reporte Detallado:**
- Folio
- Fecha
- Producto
- Código de barras
- Cantidad
- Tipo de devolución
- Lote
- Fecha de caducidad
- Valor
- Estatus

**Estadísticas mostradas:**
- Total de devoluciones
- Unidades devueltas
- Valor total
- Promedio por devolución
- Pendientes
- Procesadas
- Canceladas

---

### 12. Reporte de Inventarios
**Archivo:** `ReportesInventarios.php`  
**JavaScript:** `js/ReporteInventariosSucursales.js`

**Descripción:**  
Reporte de inventarios de sucursales con filtros por fecha.

**Características:**
- Filtros por fechas
- Descarga de reportes en Excel
- Vista de inventarios por sucursal

**Archivos relacionados:**
- `GeneraReporteInventarios.php`
- `ReportesCedisInventario.php`
- `Controladores/ArrayReporteInventariosSucursales.php`
- `Controladores/ArrayReporteInventariosCEDIS.php`

---

## Estructura de Archivos

### Archivos PHP Principales
```
PuntoDeVenta/ControlYAdministracion/
├── ReporteVentas.php
├── ReporteDeVentasTotales.php
├── ReporteVentasTotalesPorFechas.php
├── ReporteSucursales.php
├── ReporteProductosMasVendidos.php
├── ReportePorProducto.php
├── ReporteFormaDePago.php
├── ReportesAnuales.php
├── ReporteServicios.php
├── TotalesDeVentaPorVendedor.php
├── ReportesDevoluciones.php
└── ReportesInventarios.php
```

### Controladores (APIs)
```
PuntoDeVenta/ControlYAdministracion/Controladores/
├── ArrayDeReporteSucursales.php
├── ArrayDeReportePorProducto.php
├── ArrayDeReporteProductosMasVendidos.php
├── ArrayDeReporteFormaDePago.php
├── ArrayDeReportesAnuales.php
├── ArrayDeReportePorServicios.php
├── ArrayDeReporteVentasPorVendedor.php
├── ArrayVentasDelDia.php
├── ArrayVentasSimple.php
├── ArrayVentasPorTotalesPorFechas.php
├── ArrayReporteInventariosSucursales.php
└── ArrayReporteInventariosCEDIS.php
```

### Archivos de Exportación
```
PuntoDeVenta/ControlYAdministracion/Controladores/
├── exportar_reporte_producto.php
├── exportar_reporte_sucursales.php
├── exportar_reporte_productos_mas_vendidos.php
├── exportar_reporte_servicios.php
├── exportar_reporte_anual.php
├── exportar_reporte_ventas_vendedor.php
└── exportar_reporte_forma_pago.php
```

### Archivos JavaScript
```
PuntoDeVenta/ControlYAdministracion/js/
├── ReporteVentas.js
├── ReporteSucursales.js
├── ReporteProductosMasVendidos.js
├── ReportePorProductos.js
├── ReporteFormaDePago.js
├── ReportesAnuales.js
├── ReporteServicios.js
├── ReporteVentasPorVendedor.js
├── VentasTotalesPorFechaYSucursal.js
└── ReporteInventariosSucursales.js
```

---

## APIs y Controladores

### Formato de Respuesta Estándar

Todos los controladores devuelven JSON en formato DataTables:

```json
{
  "draw": 1,
  "recordsTotal": 100,
  "recordsFiltered": 100,
  "data": [
    {
      "campo1": "valor1",
      "campo2": "valor2"
    }
  ]
}
```

### Parámetros Comunes

La mayoría de los controladores aceptan estos parámetros GET:

- `fecha_inicio`: Fecha de inicio (formato: YYYY-MM-DD)
- `fecha_fin`: Fecha de fin (formato: YYYY-MM-DD)
- `sucursal`: ID de sucursal (opcional)
- `draw`: Número de draw para DataTables
- `start`: Índice de inicio para paginación
- `length`: Cantidad de registros por página

### Ejemplo de Uso

```javascript
$.ajax({
    url: 'Controladores/ArrayDeReporteSucursales.php',
    method: 'GET',
    data: {
        fecha_inicio: '2024-01-01',
        fecha_fin: '2024-01-31',
        sucursal: '',
        draw: 1
    },
    dataType: 'json',
    success: function(response) {
        // Procesar datos
        console.log(response.data);
    }
});
```

---

## Funcionalidades por Reporte

### Funcionalidades Comunes

Todos los reportes incluyen:

1. **Filtros de Fecha:**
   - Rango de fechas personalizado
   - Filtro por mes específico
   - Filtro por año

2. **Filtros de Sucursal:**
   - Todas las sucursales
   - Sucursal específica
   - Múltiples sucursales (en algunos reportes)

3. **Visualización:**
   - Tablas interactivas con DataTables
   - Paginación
   - Búsqueda en tiempo real
   - Ordenamiento por columnas
   - Responsive design

4. **Exportación:**
   - Exportación a Excel (.xlsx)
   - Exportación a CSV
   - Formato personalizado

5. **Estadísticas:**
   - Tarjetas de resumen
   - Métricas clave
   - Comparativas

6. **UI/UX:**
   - Loading overlay con mensajes personalizados
   - Animaciones suaves
   - Diseño moderno con gradientes
   - Iconos Font Awesome

---

## Estructura de Base de Datos

### Tablas Principales

#### Ventas_POS
Tabla principal de ventas que contiene:
- `ID_Venta_POS`: ID único de la venta
- `ID_Prod_POS`: ID del producto
- `Cod_Barra`: Código de barras
- `Nombre_Prod`: Nombre del producto
- `Cantidad_Venta`: Cantidad vendida
- `Total_Venta`: Total de la venta
- `Importe`: Importe en efectivo
- `Importetarjeta`: Importe en tarjeta
- `Importecredito`: Importe a crédito
- `DescuentoAplicado`: Descuento aplicado
- `FormaDePago`: Forma de pago
- `Fecha_venta`: Fecha de la venta
- `Fk_sucursal`: ID de la sucursal
- `AgregadoPor`: Usuario que realizó la venta
- `Cliente`: Nombre del cliente
- `Estatus`: Estado de la venta (generalmente 'Pagado')
- `Turno`: Turno de trabajo
- `Folio_Ticket`: Folio del ticket

#### Sucursales
- `ID_Sucursal`: ID único
- `Nombre_Sucursal`: Nombre de la sucursal
- `Sucursal_Activa`: Estado activo/inactivo

#### Productos_POS
- `ID_Prod_POS`: ID único
- `Cod_Barra`: Código de barras
- `Nombre_Prod`: Nombre del producto
- `Precio_Venta`: Precio de venta
- `Precio_C`: Precio de compra
- `Tipo`: Tipo de producto
- `Tipo_Servicio`: Tipo de servicio (si aplica)
- `Componente_Activo`: Componente activo

#### Stock_POS
- `ID_Prod_POS`: ID del producto
- `Fk_sucursal`: ID de la sucursal
- `Existencias_R`: Existencias reales

#### Usuarios_PV
- `Id_PvUser`: ID del usuario
- `Nombre_Apellidos`: Nombre completo
- `TipoUsuario`: Tipo de usuario

#### Devoluciones
- `id`: ID único
- `folio`: Folio de devolución
- `fecha`: Fecha de devolución
- `sucursal_id`: ID de sucursal
- `usuario_id`: ID de usuario
- `total_productos`: Total de productos
- `total_unidades`: Total de unidades
- `valor_total`: Valor total
- `estatus`: Estado (pendiente, procesada, cancelada)
- `observaciones_generales`: Observaciones

#### Devoluciones_Detalle
- `id`: ID único
- `devolucion_id`: ID de la devolución padre
- `codigo_barras`: Código de barras
- `nombre_producto`: Nombre del producto
- `cantidad`: Cantidad devuelta
- `tipo_devolucion`: Tipo de devolución
- `lote`: Lote del producto
- `fecha_caducidad`: Fecha de caducidad
- `precio_venta`: Precio de venta
- `valor_total`: Valor total
- `observaciones`: Observaciones

---

## Dependencias y Tecnologías

### Frontend

1. **jQuery 3.6.0+**
   - Manejo de AJAX
   - Manipulación del DOM
   - Eventos

2. **DataTables**
   - Tablas interactivas
   - Paginación
   - Búsqueda
   - Ordenamiento
   - Exportación

3. **Bootstrap 5.3.0**
   - Framework CSS
   - Componentes UI
   - Grid system
   - Modales

4. **Font Awesome 6.0+**
   - Iconos
   - Iconografía consistente

5. **SweetAlert2**
   - Alertas modernas
   - Confirmaciones
   - Notificaciones

6. **Chart.js 3.9.1** (para reportes de devoluciones)
   - Gráficos interactivos
   - Visualización de datos

### Backend

1. **PHP 7.4+**
   - Lenguaje del servidor
   - Procesamiento de datos

2. **MySQLi**
   - Conexión a base de datos
   - Consultas preparadas
   - Manejo de resultados

3. **PhpSpreadsheet** (para exportaciones)
   - Generación de archivos Excel
   - Formateo de celdas
   - Múltiples hojas

### Librerías PHP

```json
{
  "phpoffice/phpspreadsheet": "^1.29",
  "guzzlehttp/guzzle": "^7.0"
}
```

---

## Guía de Implementación

### Paso 1: Estructura de Archivos

Crear la siguiente estructura en el nuevo proyecto:

```
nuevo-proyecto/
├── reportes/
│   ├── ventas/
│   │   ├── ReporteVentas.php
│   │   ├── ReporteSucursales.php
│   │   ├── ReporteProductosMasVendidos.php
│   │   └── ...
│   ├── servicios/
│   │   └── ReporteServicios.php
│   ├── inventarios/
│   │   └── ReportesInventarios.php
│   └── devoluciones/
│       └── ReportesDevoluciones.php
├── api/
│   ├── reportes/
│   │   ├── ventas.php
│   │   ├── sucursales.php
│   │   └── ...
│   └── exportar/
│       └── excel.php
└── assets/
    ├── js/
    │   └── reportes/
    └── css/
        └── reportes/
```

### Paso 2: Configuración de Base de Datos

1. Crear conexión a base de datos:
```php
// db_connect.php
<?php
$servername = "localhost";
$username = "usuario";
$password = "contraseña";
$dbname = "nombre_bd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
```

2. Verificar que existan las tablas necesarias:
- Ventas_POS
- Sucursales
- Productos_POS
- Stock_POS
- Usuarios_PV
- Devoluciones
- Devoluciones_Detalle

### Paso 3: Implementar Controlador Base

Crear un controlador base que pueda ser extendido:

```php
<?php
// api/reportes/base.php
class ReporteBase {
    protected $conn;
    protected $fecha_inicio;
    protected $fecha_fin;
    protected $sucursal;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $this->fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $this->sucursal = $_GET['sucursal'] ?? '';
    }
    
    protected function respuestaDataTables($data) {
        return [
            "draw" => intval($_GET['draw'] ?? 1),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        ];
    }
}
?>
```

### Paso 4: Implementar Reporte de Ejemplo

Ejemplo de implementación de un reporte completo:

**PHP (api/reportes/sucursales.php):**
```php
<?php
include_once '../db_connect.php';
include_once 'base.php';

class ReporteSucursales extends ReporteBase {
    public function obtenerDatos() {
        $sql = "SELECT 
            s.Nombre_Sucursal AS Sucursal,
            SUM(v.Total_Venta) AS Total_Ventas,
            SUM(v.Importe) AS Total_Importe,
            COUNT(*) AS Numero_Transacciones
        FROM Ventas_POS v
        LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
        WHERE v.Fecha_venta BETWEEN ? AND ?
        AND v.Estatus = 'Pagado'";
        
        if (!empty($this->sucursal)) {
            $sql .= " AND v.Fk_sucursal = ?";
        }
        
        $sql .= " GROUP BY s.ID_Sucursal ORDER BY Total_Importe DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($this->sucursal)) {
            $stmt->bind_param("sss", $this->fecha_inicio, $this->fecha_fin, $this->sucursal);
        } else {
            $stmt->bind_param("ss", $this->fecha_inicio, $this->fecha_fin);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                "Sucursal" => $row['Sucursal'],
                "Total_Ventas" => '$' . number_format($row['Total_Ventas'], 2),
                "Total_Importe" => '$' . number_format($row['Total_Importe'], 2),
                "Numero_Transacciones" => number_format($row['Numero_Transacciones'])
            ];
        }
        
        return $this->respuestaDataTables($data);
    }
}

$reporte = new ReporteSucursales($conn);
header('Content-Type: application/json');
echo json_encode($reporte->obtenerDatos());
?>
```

**JavaScript (assets/js/reportes/sucursales.js):**
```javascript
$(document).ready(function() {
    const tabla = $('#tablaReporte').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'api/reportes/sucursales.php',
            type: 'GET',
            data: function(d) {
                d.fecha_inicio = $('#fecha_inicio').val();
                d.fecha_fin = $('#fecha_fin').val();
                d.sucursal = $('#sucursal').val();
            }
        },
        columns: [
            { data: 'Sucursal' },
            { data: 'Total_Ventas' },
            { data: 'Total_Importe' },
            { data: 'Numero_Transacciones' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        }
    });
    
    $('#filtrar').on('click', function() {
        tabla.ajax.reload();
    });
});
```

**HTML (reportes/ventas/ReporteSucursales.php):**
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Sucursales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Reporte de Rendimiento por Sucursal</h2>
        
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" class="form-control" value="<?php echo date('Y-m-01'); ?>">
            </div>
            <div class="col-md-3">
                <label>Fecha Fin:</label>
                <input type="date" id="fecha_fin" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-md-3">
                <label>Sucursal:</label>
                <select id="sucursal" class="form-control">
                    <option value="">Todas</option>
                    <!-- Opciones de sucursales -->
                </select>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button id="filtrar" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
        
        <table id="tablaReporte" class="table table-striped">
            <thead>
                <tr>
                    <th>Sucursal</th>
                    <th>Total Ventas</th>
                    <th>Total Importe</th>
                    <th>Transacciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/reportes/sucursales.js"></script>
</body>
</html>
```

### Paso 5: Implementar Exportación a Excel

```php
<?php
// api/exportar/excel.php
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function exportarReporteExcel($datos, $nombreArchivo) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Encabezados
    $encabezados = array_keys($datos[0]);
    $col = 'A';
    foreach ($encabezados as $encabezado) {
        $sheet->setCellValue($col . '1', $encabezado);
        $col++;
    }
    
    // Datos
    $fila = 2;
    foreach ($datos as $filaDatos) {
        $col = 'A';
        foreach ($filaDatos as $valor) {
            $sheet->setCellValue($col . $fila, $valor);
            $col++;
        }
        $fila++;
    }
    
    // Descargar
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $nombreArchivo . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>
```

### Paso 6: Estilos CSS Comunes

```css
/* assets/css/reportes/common.css */
.report-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    color: white;
    padding: 20px;
    margin-bottom: 20px;
}

.stats-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-left: 4px solid #667eea;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    display: none;
}
```

---

## Consideraciones Importantes

### Seguridad

1. **Validación de Entrada:**
   - Validar todos los parámetros GET/POST
   - Usar consultas preparadas (prepared statements)
   - Sanitizar datos de salida

2. **Autenticación:**
   - Verificar sesión de usuario
   - Validar permisos por tipo de usuario
   - Control de acceso por roles

3. **SQL Injection:**
   - Siempre usar consultas preparadas
   - Nunca concatenar directamente valores en SQL

### Rendimiento

1. **Índices de Base de Datos:**
   - Índices en `Fecha_venta`
   - Índices en `Fk_sucursal`
   - Índices en `Estatus`
   - Índices en `ID_Prod_POS`

2. **Optimización de Consultas:**
   - Usar LIMIT en consultas grandes
   - Agrupar datos en el servidor
   - Cachear resultados frecuentes

3. **Paginación:**
   - Implementar paginación del lado del servidor
   - Limitar registros por página

### Mantenimiento

1. **Código Modular:**
   - Separar lógica de presentación
   - Reutilizar funciones comunes
   - Documentar funciones complejas

2. **Versionado:**
   - Mantener control de versiones
   - Documentar cambios
   - Testing antes de producción

---

## Notas Adicionales

- Todos los reportes requieren sesión activa de usuario
- Los permisos varían según el tipo de usuario (Administrador, Marketing, etc.)
- Los reportes pueden ser personalizados según necesidades específicas
- Se recomienda implementar cache para reportes frecuentes
- Los formatos de fecha deben ser consistentes (YYYY-MM-DD)
- Los montos monetarios deben formatearse con 2 decimales

---

## Contacto y Soporte

Para implementar estos reportes en un nuevo proyecto, asegúrese de:

1. Tener acceso a la base de datos con las tablas necesarias
2. Configurar correctamente las conexiones de base de datos
3. Instalar todas las dependencias (composer install)
4. Verificar permisos de usuario y sesiones
5. Probar cada reporte individualmente antes de producción

---

**Última actualización:** Diciembre 2024  
**Versión del documento:** 1.0
