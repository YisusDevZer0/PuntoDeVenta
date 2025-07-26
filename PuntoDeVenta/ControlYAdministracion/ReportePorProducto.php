<?php
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas por Producto - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <?php include "header.php";?>
    
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    
    <style>
        /* Estilos para que la tabla tenga los mismos colores que las demás */
        #tablaReporte {
            width: 100%;
            border-collapse: collapse;
        }
        
        #tablaReporte thead th {
            background-color: #ef7980 !important;
            color: white !important;
            font-weight: bold;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        #tablaReporte tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        #tablaReporte tbody tr:hover {
            background-color: #ffe6e7 !important;
        }
        
        #tablaReporte tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        /* Estilos para los botones de paginación */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: #ef7980 !important;
            color: white !important;
            border: 1px solid #ef7980 !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #d65a62 !important;
            color: white !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #d65a62 !important;
            color: white !important;
        }
        
        /* Estilos para el loading */
        #loading-overlay {
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
        
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #ef7980;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Estilos para las estadísticas con colores por importancia */
        .stats-card-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stats-card-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stats-card-info {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stats-card-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <!-- Spinner End -->
    <?php include_once "Menu.php" ?>

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php";?>
        <!-- Navbar End -->

        <!-- Table Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">
                        <i class="fas fa-chart-bar me-2"></i>
                        Reporte de Ventas por Producto - <?php echo $row['Licencia']?>
                    </h6>
                    
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
                                // Cargar sucursales dinámicamente usando la conexión correcta
                                include_once "db_connect.php";
                                $sql_sucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 1 ORDER BY Nombre_Sucursal";
                                $result_sucursales = $conn->query($sql_sucursales);
                                if ($result_sucursales && $result_sucursales->num_rows > 0) {
                                    while ($sucursal = $result_sucursales->fetch_assoc()) {
                                        echo "<option value='" . $sucursal['ID_Sucursal'] . "'>" . htmlspecialchars($sucursal['Nombre_Sucursal']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" onclick="filtrarDatos()">
                                    <i class="fas fa-search me-1"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportarExcel()">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="row mb-4" id="statsRow" style="display: none;">
                        <div class="col-md-3">
                            <div class="stats-card-primary">
                                <div class="stats-icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div class="stats-number" id="totalProductos">0</div>
                                <div class="stats-label">Productos Vendidos</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card-success">
                                <div class="stats-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="stats-number" id="totalVentas">$0</div>
                                <div class="stats-label">Total Ventas</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card-info">
                                <div class="stats-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="stats-number" id="totalUnidades">0</div>
                                <div class="stats-label">Unidades Vendidas</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card-warning">
                                <div class="stats-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
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
                                    <th><i class="fas fa-hashtag me-1"></i>ID Producto</th>
                                    <th><i class="fas fa-barcode me-1"></i>Código de Barras</th>
                                    <th><i class="fas fa-box me-1"></i>Nombre del Producto</th>
                                    <th><i class="fas fa-tag me-1"></i>Tipo</th>
                                    <th><i class="fas fa-store me-1"></i>Sucursal</th>
                                    <th><i class="fas fa-dollar-sign me-1"></i>Precio Venta</th>
                                    <th><i class="fas fa-shopping-bag me-1"></i>Precio Compra</th>
                                    <th><i class="fas fa-warehouse me-1"></i>Existencias</th>
                                    <th><i class="fas fa-sort-amount-up me-1"></i>Total Vendido</th>
                                    <th><i class="fas fa-money-bill-wave me-1"></i>Total Importe</th>
                                    <th><i class="fas fa-cash-register me-1"></i>Total Venta</th>
                                    <th><i class="fas fa-percentage me-1"></i>Total Descuento</th>
                                    <th><i class="fas fa-chart-bar me-1"></i>Número Ventas</th>
                                    <th><i class="fas fa-user me-1"></i>Vendedor</th>
                                    <th><i class="fas fa-calendar-plus me-1"></i>Primera Venta</th>
                                    <th><i class="fas fa-calendar-check me-1"></i>Última Venta</th>
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

    <!-- Footer Start -->
    <?php 
    include "Modales/NuevoFondoDeCaja.php";
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php";?>
    
    <script src="js/ReportePorProductos.js"></script>
</body>
</html>