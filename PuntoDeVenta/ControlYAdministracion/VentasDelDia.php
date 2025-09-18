<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Ventas del día - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <?php
   include "header.php";?>
   
   <style>
        /* Estilos para que la tabla tenga los mismos colores que las demás */
        #Clientes {
            width: 100%;
            border-collapse: collapse;
        }
        
        #Clientes thead th {
            background-color: #ef7980 !important;
            color: white !important;
            font-weight: bold;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        #Clientes tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        #Clientes tbody tr:hover {
            background-color: #ffe6e7 !important;
        }
        
        #Clientes tbody td {
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

   <div id="loading-overlay" class="loading-overlay">
     <div>
  <div class="loader"></div>
       <div id="loading-text" class="loading-text">Cargando ventas del día...</div>
     </div>
</div>
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
                            Ventas del Día - <?php echo $row['Licencia']?>
                        </h6>
                        
                        <!-- Filtros -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#BusquedaVentasSucursal">
                                    <i class="fas fa-clinic-medical me-1"></i> Filtrar por Sucursal
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#FiltroEspecificoMesxd">
                                    <i class="fas fa-calendar-week me-1"></i> Buscar por Mes
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#FiltroEspecificoFechaVentas">
                                    <i class="fas fa-calendar me-1"></i> Rango de Fechas
</button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success" onclick="CargaListadoDeProductos()">
                                    <i class="fas fa-sync-alt me-1"></i> Actualizar Datos
</button>
                            </div>
                        </div>

                        <!-- Estadísticas Rápidas -->
                        <div class="row mb-4" id="statsRow">
                            <div class="col-md-3">
                                <div class="stats-card-primary">
                                    <div class="stats-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="stats-number" id="total-ventas-hoy">0</div>
                                    <div class="stats-label">Ventas Hoy</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card-success">
                                    <div class="stats-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="stats-number" id="total-ingresos-hoy">$0</div>
                                    <div class="stats-label">Ingresos Hoy</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card-info">
                                    <div class="stats-icon">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="stats-number" id="sucursales-activas">0</div>
                                    <div class="stats-label">Sucursales Activas</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card-warning">
                                    <div class="stats-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="stats-number" id="promedio-venta-hoy">$0</div>
                                    <div class="stats-label">Promedio por Venta</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla -->
                        <div class="table-responsive">
            <div id="DataDeServicios"></div>
                        </div>
                    </div>
                </div>
            </div>
            
          
<script src="js/VentasDelDia.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/FiltroPorSucursales.php";
            include "Modales/FiltroPorMeses.php";
            include "Modales/FiltroPorVendedor.php";
            include "Modales/FiltroRangoFechas.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>