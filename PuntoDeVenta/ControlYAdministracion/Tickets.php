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
   
   <style>
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

            <!-- Table Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="col-12">
                    <div class="bg-light rounded h-100 p-4">
                        <h6 class="mb-4" style="color:#0172b6;">
                            <i class="fas fa-receipt me-2"></i>
                            Tickets de Venta - <?php echo $row['Licencia']?>
                        </h6>
                        
                        <!-- Estadísticas Rápidas -->
                        <div class="row mb-4" id="statsRow">
                            <div class="col-md-3">
                                <div class="stats-card-primary">
                                    <div class="stats-icon">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="stats-number" id="total-tickets">0</div>
                                    <div class="stats-label">Total Tickets</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card-success">
                                    <div class="stats-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="stats-number" id="total-ventas">$0</div>
                                    <div class="stats-label">Ventas Totales</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card-info">
                                    <div class="stats-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div class="stats-number" id="tickets-hoy">0</div>
                                    <div class="stats-label">Tickets Hoy</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card-warning">
                                    <div class="stats-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="stats-number" id="promedio-ticket">$0</div>
                                    <div class="stats-label">Promedio por Ticket</div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtros -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#FiltroPorSucursal">
                                        <i class="fas fa-clinic-medical me-1"></i> Filtrar por Sucursal
                                    </button>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#FiltroEspecificoMes">
                                        <i class="fas fa-calendar-week me-1"></i> Buscar por Mes
                                    </button>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#FiltroRangoFechas">
                                        <i class="fas fa-calendar me-1"></i> Rango de Fechas
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="cargarTickets()">
                                        <i class="fas fa-sync-alt me-1"></i> Actualizar Lista
                                    </button>
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
            
          
    <script src="js/DesgloseTicketss.js"></script>

    <script>
    $(document).ready(function() {
        // Esperar un momento para que todos los scripts se carguen
        setTimeout(function() {
            // Cargar tickets al iniciar la página
            if (typeof CargaServicios === 'function') {
                CargaServicios();
            } else {
                cargarTickets();
            }
        }, 500);
        
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

    // Función para cargar tickets (implementada en DesgloseTicketss.js)
    function cargarTickets() {
        if (typeof aplicarFiltros === 'function') {
            aplicarFiltros();
        } else if (typeof CargaServicios === 'function') {
            CargaServicios();
        } else {
            console.log("Cargando tickets...");
            // Recargar la página si las funciones no están disponibles
            location.reload();
        }
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