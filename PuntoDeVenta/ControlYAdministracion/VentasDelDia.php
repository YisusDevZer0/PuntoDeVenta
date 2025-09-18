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
   
   <!-- Estilos personalizados para el reporte -->
   <style>
   .report-card {
       background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
       border-radius: 15px;
       color: white;
       padding: 20px;
       margin-bottom: 20px;
       box-shadow: 0 8px 32px rgba(0,0,0,0.1);
       backdrop-filter: blur(10px);
       border: 1px solid rgba(255,255,255,0.2);
   }
   
   .stats-card {
       background: white;
       border-radius: 12px;
       padding: 20px;
       margin-bottom: 20px;
       box-shadow: 0 4px 20px rgba(0,0,0,0.08);
       border-left: 4px solid #667eea;
       transition: transform 0.3s ease, box-shadow 0.3s ease;
   }
   
   .stats-card:hover {
       transform: translateY(-5px);
       box-shadow: 0 8px 30px rgba(0,0,0,0.15);
   }
   
   .stats-icon {
       font-size: 2.5rem;
       color: #667eea;
       margin-bottom: 10px;
   }
   
   .stats-number {
       font-size: 2rem;
       font-weight: bold;
       color: #2c3e50;
       margin-bottom: 5px;
   }
   
   .stats-label {
       color: #7f8c8d;
       font-size: 0.9rem;
       text-transform: uppercase;
       letter-spacing: 1px;
   }
   
   .filter-section {
       background: white;
       border-radius: 12px;
       padding: 25px;
       margin-bottom: 25px;
       box-shadow: 0 4px 20px rgba(0,0,0,0.08);
   }
   
   .filter-title {
       color: #2c3e50;
       font-weight: 600;
       margin-bottom: 20px;
       font-size: 1.2rem;
   }
   
   .btn-filter {
       background: linear-gradient(45deg, #667eea, #764ba2);
       border: none;
       border-radius: 25px;
       padding: 10px 25px;
       color: white;
       font-weight: 500;
       transition: all 0.3s ease;
       margin: 5px;
   }
   
   .btn-filter:hover {
       transform: translateY(-2px);
       box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
       color: white;
   }
   
   .table-container {
       background: white;
       border-radius: 12px;
       padding: 20px;
       box-shadow: 0 4px 20px rgba(0,0,0,0.08);
   }
   
   .page-title {
       background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
       -webkit-background-clip: text;
       -webkit-text-fill-color: transparent;
       background-clip: text;
       font-size: 2.5rem;
       font-weight: 700;
       margin-bottom: 10px;
   }
   
   .page-subtitle {
       color: #7f8c8d;
       font-size: 1.1rem;
       margin-bottom: 30px;
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
   
   .loader {
       border: 4px solid #f3f3f3;
       border-top: 4px solid #667eea;
       border-radius: 50%;
       width: 50px;
       height: 50px;
       animation: spin 1s linear infinite;
   }
   
   @keyframes spin {
       0% { transform: rotate(0deg); }
       100% { transform: rotate(360deg); }
   }
   
   .loading-text {
       color: white;
       margin-top: 20px;
       font-size: 1.1rem;
       text-align: center;
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


            <!-- Reporte de Ventas del Día Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="col-12">
                    <!-- Header del Reporte -->
                    <div class="report-card">
                        <h1 class="page-title">Ventas del Día</h1>
                        <p class="page-subtitle">Registro detallado de ventas - <?php echo $row['Licencia']?></p>
                    </div>

                    <!-- Cards de Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="stats-number" id="total-ventas-hoy">0</div>
                                <div class="stats-label">Ventas Hoy</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="stats-number" id="total-ingresos-hoy">$0</div>
                                <div class="stats-label">Ingresos Hoy</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="stats-number" id="sucursales-activas">0</div>
                                <div class="stats-label">Sucursales Activas</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stats-number" id="promedio-venta-hoy">$0</div>
                                <div class="stats-label">Promedio por Venta</div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Filtros -->
                    <div class="filter-section">
                        <h5 class="filter-title">
                            <i class="fas fa-filter"></i> Filtros de Búsqueda
                        </h5>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-filter" data-bs-toggle="modal" data-bs-target="#BusquedaVentasSucursal">
                                    <i class="fas fa-clinic-medical"></i> Filtrar por Sucursal
                                </button>
                                <button type="button" class="btn btn-filter" data-toggle="modal" data-target="#FiltroEspecificoMesxd">
                                    <i class="fas fa-calendar-week"></i> Buscar por Mes
                                </button>
                                <button type="button" class="btn btn-filter" data-toggle="modal" data-target="#FiltroEspecificoFechaVentas">
                                    <i class="fas fa-calendar"></i> Rango de Fechas
                                </button>
                                <button type="button" class="btn btn-filter" onclick="CargaListadoDeProductos()">
                                    <i class="fas fa-sync-alt"></i> Actualizar Datos
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor de la Tabla -->
                    <div class="table-container">
                        <h6 class="mb-4" style="color:#667eea; font-weight: 600;">
                            <i class="fas fa-table"></i> Detalle de Ventas del Día
                        </h6>
                        <div id="DataDeServicios"></div>
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