<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Tickets de Venta - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <?php
   include "header.php";?>
   
   <!-- Estilos personalizados para tickets -->
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

   /* Estilos para la tabla */
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
   </style>

   <div id="loading-overlay" class="loading-overlay">
     <div>
       <div class="loader"></div>
       <div id="loading-text" class="loading-text">Cargando tickets...</div>
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

        <!-- Tickets de Venta Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <!-- Header del Reporte -->
                <div class="report-card">
                    <h1 class="page-title">Tickets de Venta</h1>
                    <p class="page-subtitle">Gestión y consulta de tickets de venta - <?php echo $row['Licencia']?></p>
                </div>

                <!-- Cards de Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="stats-number" id="total-tickets">0</div>
                            <div class="stats-label">Total Tickets</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stats-number" id="total-ventas">$0</div>
                            <div class="stats-label">Ventas Totales</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="stats-number" id="tickets-hoy">0</div>
                            <div class="stats-label">Tickets Hoy</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stats-number" id="promedio-ticket">$0</div>
                            <div class="stats-label">Promedio por Ticket</div>
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
                            <button type="button" class="btn btn-filter" data-bs-toggle="modal" data-bs-target="#FiltroPorSucursal">
                                <i class="fas fa-clinic-medical"></i> Filtrar por Sucursal
                            </button>
                            <button type="button" class="btn btn-filter" data-toggle="modal" data-target="#FiltroEspecificoMes">
                                <i class="fas fa-calendar-week"></i> Buscar por Mes
                            </button>
                            <button type="button" class="btn btn-filter" data-toggle="modal" data-target="#FiltroRangoFechas">
                                <i class="fas fa-calendar"></i> Rango de Fechas
                            </button>
                            <button type="button" class="btn btn-filter" onclick="cargarTickets()">
                                <i class="fas fa-sync-alt"></i> Actualizar Lista
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contenedor de la Tabla -->
                <div class="table-container">
                    <h6 class="mb-4" style="color:#667eea; font-weight: 600;">
                        <i class="fas fa-table"></i> Lista de Tickets
                    </h6>
                    <div id="DataDeServicios"></div>
                </div>
            </div>
        </div>
    </div></div>
            
          
    <script src="js/DesgloseTicketss.js"></script>

    <script>
    $(document).ready(function() {
        // Cargar tickets al iniciar la página
        cargarTickets();
        
        // Delegación de eventos para el botón "btn-Reimpresion"
        $(document).on("click", ".btn-Reimpresion", function() {
            var id = $(this).data("id");
            console.log("Botón de reimpresión clickeado para el ID:", id);
            $('#CajasDi').removeClass('modal-dialog modal-xl modal-notify modal-success').addClass('modal-dialog modal-notify modal-success');
            
            $.post("https://doctorpez.mx/PuntoDeVentaControlYAdministracion/Modales/ReimprimeTicketsVenta.php", { id: id }, function(data) {
                $("#FormCajas").html(data);
                $("#TitulosCajas").html("Generando archivo para reimpresión");
            });
            
            $('#ModalEdDele').modal('show');
        });

        // Delegación de eventos para el botón "btn-desglose"
        $(document).on("click", ".btn-desglose", function() {
            var id = $(this).data("id");
            console.log("Botón de desglose clickeado para el ID:", id);
            
            $('#CajasDi').removeClass('modal-dialog modal-notify modal-success').addClass('modal-dialog modal-xl modal-notify modal-success');
            
            $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/DesgloseTicketsVenta.php", { id: id }, function(data) {
                $("#TitulosCajas").html("Desglose de ticket");  
                $("#FormCajas").html(data);
            });
            
            $('#ModalEdDele').modal('show');
        });

        // Delegación de eventos para el botón "btn-eliminar"
        $(document).on("click", ".btn-eliminar", function() {
            var id = $(this).data("id");
            console.log("Botón de eliminar clickeado para el ID:", id);
            
            $('#CajasDi').removeClass('modal-dialog modal-notify modal-success').addClass('modal-dialog modal-xl modal-notify modal-success');
            
            $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/eliminar_ticket.php", { id: id }, function(data) {
                $("#TitulosCajas").html("Eliminar ticket");  
                $("#FormCajas").html(data);
            });
            
            $('#ModalEdDele').modal('show');
        });
    });

    // Función para cargar tickets (debe ser implementada en el archivo JS correspondiente)
    function cargarTickets() {
        // Esta función debe ser implementada en el archivo DesgloseTicketss.js
        // o en el archivo JS correspondiente para cargar los datos de tickets
        console.log("Cargando tickets...");
    }
    </script>

    <!-- Footer Start -->
    <?php 
    include "Modales/FiltroPorSucursales.php";
    include "Modales/FiltroPorMeses.php";
    include "Modales/FiltroRangoFechas.php";
    include "Modales/NuevoFondoDeCaja.php";
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php";?>

    <!-- Modal para acciones de tickets -->
    <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
        <div id="CajasDi" class="modal-dialog modal-notify modal-success">
            <div class="text-center">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #ef7980 !important;">
                        <p class="heading lead" id="TitulosCajas" style="color:white;"></p>
                    </div>
                    
                    <div class="modal-body">
                        <div class="text-center">
                            <div id="FormCajas"></div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
         
</body>

</html>