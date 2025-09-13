<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Conteos Diarios - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <?php
   include "header.php";?>
   <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0" style="color:#0172b6;">
                                <i class="fa-solid fa-calendar-day me-2"></i>
                                Comparación de Conteos Diarios vs Stock Real - <?php echo $row['Licencia']?>
                            </h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm" onclick="CargarConteosDiarios()">
                                    <i class="fa-solid fa-refresh me-1"></i>Actualizar
                                </button>
                                <button class="btn btn-success btn-sm" onclick="ExportarConteosDiarios()">
                                    <i class="fa-solid fa-file-excel me-1"></i>Exportar Excel
                                </button>
                                <button class="btn btn-secondary btn-sm" onclick="ImprimirConteosDiarios()">
                                    <i class="fa-solid fa-print me-1"></i>Imprimir
                                </button>
                            </div>
                        </div>
                        
                        <!-- Filtros -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Sucursal:</label>
                                <select class="form-select" id="filtroSucursal" onchange="CargarConteosDiarios()">
                                    <option value="">Todas las sucursales</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Estado:</label>
                                <select class="form-select" id="filtroEstado" onchange="CargarConteosDiarios()">
                                    <option value="">Todos</option>
                                    <option value="0">Completados</option>
                                    <option value="1">En Pausa</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha desde:</label>
                                <input type="date" class="form-control" id="fechaDesde" onchange="CargarConteosDiarios()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha hasta:</label>
                                <input type="date" class="form-control" id="fechaHasta" onchange="CargarConteosDiarios()">
                            </div>
                        </div>
                        
                        <!-- Estadísticas Resumen -->
                        <div class="row mb-4" id="estadisticasResumen">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Total Conteos</h6>
                                                <h4 id="totalConteos">0</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fa-solid fa-list-check fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Conteos Exactos</h6>
                                                <h4 id="conteosExactos">0</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fa-solid fa-check-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Diferencias > 5%</h6>
                                                <h4 id="diferenciasAltas">0</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fa-solid fa-exclamation-triangle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Sucursales Evaluadas</h6>
                                                <h4 id="sucursalesEvaluadas">0</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fa-solid fa-building fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="DataConteosDiarios"></div>
                    </div>
                </div>
            </div>
            
<script src="js/ConteosDiarios.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>

<script>
$(document).ready(function() {
    // Cargar datos iniciales
    CargarSucursales();
    CargarConteosDiarios();
    
    // Establecer fecha de hoy por defecto
    const hoy = new Date().toISOString().split('T')[0];
    $('#fechaHasta').val(hoy);
    
    // Delegación de eventos para botones de acción
    $(document).on("click", ".btn-ver-detalles", function() {
        var folio = $(this).data("folio");
        var codigo = $(this).data("codigo");
        
        $('#CajasDi').removeClass('modal-dialog modal-xl modal-notify modal-success').addClass('modal-dialog modal-xl modal-notify modal-success');
        
        $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/DetallesConteoDiario.php", 
            { folio: folio, codigo: codigo }, 
            function(data) {
                $("#FormCajas").html(data);
                $("#TitulosCajas").html("Detalles del Conteo");
            });
        
        $('#ModalEdDele').modal('show');
    });

    $(document).on("click", ".btn-exportar", function() {
        var folio = $(this).data("folio");
        var codigo = $(this).data("codigo");
        
        // Exportar conteo específico
        const url = `https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ExportarConteoIndividual.php?folio=${folio}&codigo=${codigo}`;
        window.open(url, '_blank');
    });

    $(document).on("click", ".btn-imprimir", function() {
        var folio = $(this).data("folio");
        var codigo = $(this).data("codigo");
        
        // Imprimir conteo específico
        const url = `https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ImprimirConteoIndividual.php?folio=${folio}&codigo=${codigo}`;
        window.open(url, '_blank');
    });
});

// Función para cargar estadísticas resumen
function CargarEstadisticasResumen() {
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        estado: $('#filtroEstado').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val()
    };

    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/EstadisticasConteosDiarios.php", 
        filtros, 
        function(data) {
            if (data.success) {
                $('#totalConteos').text(data.totalConteos || 0);
                $('#conteosExactos').text(data.conteosExactos || 0);
                $('#diferenciasAltas').text(data.diferenciasAltas || 0);
                $('#sucursalesEvaluadas').text(data.sucursalesEvaluadas || 0);
            }
        }, 'json')
        .fail(function() {
            console.error('Error al cargar estadísticas');
        });
}
</script>

  <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
  <div id="CajasDi"class="modal-dialog  modal-notify modal-success" >
    <div class="text-center">
      <div class="modal-content">
      <div class="modal-header" style=" background-color: #ef7980 !important;" >
         <p class="heading lead" id="TitulosCajas"  style="color:white;" ></p>
       </div>
        
	        <div class="modal-body">
          <div class="text-center">
        <div id="FormCajas"></div>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal --></div>
</body>

</html>
