<style>
  /* Personalizar el diseño de la paginación con CSS */
  .dataTables_wrapper .dataTables_paginate {
    text-align: center !important; /* Centrar los botones de paginación */
    margin-top: 10px !important;
  }

  .dataTables_paginate .paginate_button {
    padding: 5px 10px !important;
    border: 1px solid #ef7980 !important;
    margin: 2px !important;
    cursor: pointer !important;
    font-size: 16px !important;
    color: #ef7980 !important;
    background-color: #fff !important;
  }

  /* Cambiar el color del paginado seleccionado */
  .dataTables_paginate .paginate_button.current {
    background-color: #ef7980 !important;
    color: #fff !important;
    border-color: #ef7980 !important;
  }

  /* Cambiar el color del hover */
  .dataTables_paginate .paginate_button:hover {
    background-color: #ef7980 !important;
    color: #fff !important;
    border-color: #ef7980 !important;
  }

  /* Estilo para las celdas de la tabla */
  .table td {
    vertical-align: middle !important;
    padding: 8px !important;
  }

  /* Estilo para el encabezado de la tabla */
  .table th {
    background-color: #f8f9fa !important;
    border-bottom: 2px solid #dee2e6 !important;
    font-weight: bold !important;
  }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Reporte de Ventas por Producto</h4>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success" onclick="exportarExcel()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="fecha_inicio">Fecha Inicio:</label>
                            <input type="date" id="fecha_inicio" class="form-control" value="<?php echo date('Y-m-01'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_fin">Fecha Fin:</label>
                            <input type="date" id="fecha_fin" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="sucursal">Sucursal:</label>
                            <select id="sucursal" class="form-control">
                                <option value="">Todas las sucursales</option>
                                <!-- Aquí se cargarían las sucursales dinámicamente -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-block" onclick="filtrarDatos()">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table id="tablaReporte" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID Producto</th>
                                    <th>Código de Barras</th>
                                    <th>Nombre del Producto</th>
                                    <th>Tipo</th>
                                    <th>Sucursal</th>
                                    <th>Precio Venta</th>
                                    <th>Precio Compra</th>
                                    <th>Existencias</th>
                                    <th>Total Vendido</th>
                                    <th>Total Importe</th>
                                    <th>Total Venta</th>
                                    <th>Total Descuento</th>
                                    <th>Número Ventas</th>
                                    <th>Vendedor</th>
                                    <th>Primera Venta</th>
                                    <th>Última Venta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#tablaReporte').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "ArrayDeReportePorProducto.php",
            "type": "GET",
            "data": function(d) {
                d.fecha_inicio = $('#fecha_inicio').val();
                d.fecha_fin = $('#fecha_fin').val();
                d.sucursal = $('#sucursal').val();
            },
            "dataSrc": function(json) {
                // Verificar si hay error en la respuesta
                if (json.error) {
                    console.error("Error del servidor:", json.error);
                    alert("Error al cargar los datos: " + json.error);
                    return [];
                }
                return json.data || [];
            }
        },
        "columns": [
            {"data": "ID_Prod_POS"},
            {"data": "Cod_Barra"},
            {"data": "Nombre_Prod"},
            {"data": "Tipo"},
            {"data": "Nombre_Sucursal"},
            {"data": "Precio_Venta"},
            {"data": "Precio_C"},
            {"data": "Existencias_R"},
            {"data": "Total_Vendido"},
            {"data": "Total_Importe"},
            {"data": "Total_Venta"},
            {"data": "Total_Descuento"},
            {"data": "Numero_Ventas"},
            {"data": "AgregadoPor"},
            {"data": "Primera_Venta"},
            {"data": "Ultima_Venta"}
        ],
        "order": [[8, "desc"]], // Ordenar por Total Vendido descendente
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "error": function(xhr, error, thrown) {
            console.error("Error en DataTables:", error);
            alert("Error al cargar los datos. Por favor, verifica la conexión.");
        }
    });

    // Función para filtrar datos
    window.filtrarDatos = function() {
        table.ajax.reload();
    };

    // Función para exportar a Excel
    window.exportarExcel = function() {
        var fecha_inicio = $('#fecha_inicio').val();
        var fecha_fin = $('#fecha_fin').val();
        var sucursal = $('#sucursal').val();
        
        var url = 'exportar_reporte_producto.php?fecha_inicio=' + fecha_inicio + 
                  '&fecha_fin=' + fecha_fin + 
                  '&sucursal=' + sucursal;
        
        window.open(url, '_blank');
    };
});
</script>





