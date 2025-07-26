<?php
session_start();
include("db_connect.php");
include("ControladorUsuario.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: signin.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas por Producto - Sistema de Punto de Venta</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables Buttons CSS -->
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f4f5ff;
            font-family: 'DM Sans', 'Roboto', sans-serif;
            color: #1d1e20;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            background: white;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid #e1e5e9;
            padding: 10px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            padding: 15px 10px;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9ff;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px;
            margin: 0 2px;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center text-white">
            <div class="spinner mb-3"></div>
            <h5>Procesando datos...</h5>
            <p class="mb-0">Por favor espere mientras cargamos el reporte</p>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-chart-line me-2"></i>
                Reporte de Ventas por Producto
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home me-1"></i> Inicio
                </a>
                <a class="nav-link" href="cerrar_sesion.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Reporte de Ventas por Producto
                        </h4>
                        <div>
                            <button type="button" class="btn btn-success me-2" onclick="exportarExcel()">
                                <i class="fas fa-file-excel me-1"></i> Exportar Excel
                            </button>
                            <button type="button" class="btn btn-primary" onclick="filtrarDatos()">
                                <i class="fas fa-search me-1"></i> Filtrar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filtros -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label">
                                    <i class="fas fa-calendar me-1"></i> Fecha Inicio
                                </label>
                                <input type="date" id="fecha_inicio" class="form-control" 
                                       value="<?php echo date('Y-m-01'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label">
                                    <i class="fas fa-calendar me-1"></i> Fecha Fin
                                </label>
                                <input type="date" id="fecha_fin" class="form-control" 
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="sucursal" class="form-label">
                                    <i class="fas fa-store me-1"></i> Sucursal
                                </label>
                                <select id="sucursal" class="form-control">
                                    <option value="">Todas las sucursales</option>
                                    <?php
                                    // Cargar sucursales dinámicamente
                                    $sql_sucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 1 ORDER BY Nombre_Sucursal";
                                    $result_sucursales = $conn->query($sql_sucursales);
                                    if ($result_sucursales) {
                                        while ($sucursal = $result_sucursales->fetch_assoc()) {
                                            echo "<option value='" . $sucursal['ID_Sucursal'] . "'>" . $sucursal['Nombre_Sucursal'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-primary w-100" onclick="filtrarDatos()">
                                    <i class="fas fa-search me-1"></i> Aplicar Filtros
                                </button>
                            </div>
                        </div>

                        <!-- Estadísticas Rápidas -->
                        <div class="row mb-4" id="statsRow" style="display: none;">
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <div class="stats-number" id="totalProductos">0</div>
                                    <div class="stats-label">Productos Vendidos</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <div class="stats-number" id="totalVentas">$0</div>
                                    <div class="stats-label">Total Ventas</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <div class="stats-number" id="totalUnidades">0</div>
                                    <div class="stats-label">Unidades Vendidas</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <div class="stats-number" id="promedioVenta">$0</div>
                                    <div class="stats-label">Promedio por Venta</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla -->
                        <div class="table-responsive">
                            <table id="tablaReporte" class="table table-striped table-hover">
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Mostrar loading al iniciar
            $('#loadingOverlay').show();
            
            // Inicializar DataTable
            var table = $('#tablaReporte').DataTable({
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "url": "Controladores/ArrayDeReportePorProducto.php",
                    "type": "GET",
                    "data": function(d) {
                        d.fecha_inicio = $('#fecha_inicio').val();
                        d.fecha_fin = $('#fecha_fin').val();
                        d.sucursal = $('#sucursal').val();
                    },
                    "dataSrc": function(json) {
                        // Ocultar loading
                        $('#loadingOverlay').hide();
                        
                        // Verificar si hay error en la respuesta
                        if (json.error) {
                            console.error("Error del servidor:", json.error);
                            alert("Error al cargar los datos: " + json.error);
                            return [];
                        }
                        
                        // Actualizar estadísticas
                        actualizarEstadisticas(json.data);
                        
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
                    $('#loadingOverlay').hide();
                    console.error("Error en DataTables:", error);
                    alert("Error al cargar los datos. Por favor, verifica la conexión.");
                },
                "initComplete": function() {
                    $('#loadingOverlay').hide();
                }
            });

            // Función para actualizar estadísticas
            function actualizarEstadisticas(data) {
                if (data && data.length > 0) {
                    var totalProductos = data.length;
                    var totalVentas = 0;
                    var totalUnidades = 0;
                    var totalImporte = 0;
                    
                    data.forEach(function(item) {
                        totalUnidades += parseInt(item.Total_Vendido.replace(/,/g, ''));
                        totalImporte += parseFloat(item.Total_Importe.replace(/[$,]/g, ''));
                    });
                    
                    var promedioVenta = totalProductos > 0 ? totalImporte / totalProductos : 0;
                    
                    $('#totalProductos').text(totalProductos);
                    $('#totalVentas').text('$' + totalImporte.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                    $('#totalUnidades').text(totalUnidades.toLocaleString('es-MX'));
                    $('#promedioVenta').text('$' + promedioVenta.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                    
                    $('#statsRow').show();
                }
            }

            // Función para filtrar datos
            window.filtrarDatos = function() {
                $('#loadingOverlay').show();
                table.ajax.reload(function() {
                    $('#loadingOverlay').hide();
                });
            };

            // Función para exportar a Excel
            window.exportarExcel = function() {
                $('#loadingOverlay').show();
                var fecha_inicio = $('#fecha_inicio').val();
                var fecha_fin = $('#fecha_fin').val();
                var sucursal = $('#sucursal').val();
                
                var url = 'Controladores/exportar_reporte_producto.php?fecha_inicio=' + fecha_inicio + 
                          '&fecha_fin=' + fecha_fin + 
                          '&sucursal=' + sucursal;
                
                window.open(url, '_blank');
                setTimeout(function() {
                    $('#loadingOverlay').hide();
                }, 2000);
            };
        });
    </script>
</body>
</html>