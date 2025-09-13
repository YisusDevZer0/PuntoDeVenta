<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Conteos Pausados - <?php echo $row['Licencia']?></title>
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
                                <i class="fa-solid fa-pause-circle me-2"></i>
                                Conteos Pausados - <?php echo $row['Licencia']?>
                            </h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm" onclick="CargarConteosPausados()">
                                    <i class="fa-solid fa-refresh me-1"></i>Actualizar
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="ReanudarTodosConteos()">
                                    <i class="fa-solid fa-play me-1"></i>Reanudar Todos
                                </button>
                            </div>
                        </div>
                        
                        <!-- Filtros -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Sucursal:</label>
                                <select class="form-select" id="filtroSucursal" onchange="CargarConteosPausados()">
                                    <option value="">Todas las sucursales</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Usuario:</label>
                                <select class="form-select" id="filtroUsuario" onchange="CargarConteosPausados()">
                                    <option value="">Todos los usuarios</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha desde:</label>
                                <input type="date" class="form-control" id="fechaDesde" onchange="CargarConteosPausados()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha hasta:</label>
                                <input type="date" class="form-control" id="fechaHasta" onchange="CargarConteosPausados()">
                            </div>
                        </div>
                        
                        <!-- Estadísticas -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Total Pausados</h6>
                                                <h4 id="totalPausados">0</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fa-solid fa-pause fa-2x"></i>
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
                                                <h6 class="card-title">Sucursales Afectadas</h6>
                                                <h4 id="sucursalesAfectadas">0</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fa-solid fa-building fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Más de 24h Pausados</h6>
                                                <h4 id="conteosAntiguos">0</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fa-solid fa-clock fa-2x"></i>
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
                                                <h6 class="card-title">Reanudados Hoy</h6>
                                                <h4 id="reanudadosHoy">0</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fa-solid fa-play fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="DataConteosPausados"></div>
                    </div>
                </div>
            </div>
            
<script src="js/ConteosPausados.js"></script>

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
    CargarUsuarios();
    CargarConteosPausados();
    
    // Establecer fecha de hoy por defecto
    const hoy = new Date().toISOString().split('T')[0];
    $('#fechaHasta').val(hoy);
    
    // Delegación de eventos para botones de acción
    $(document).on("click", ".btn-reanudar-conteo", function() {
        var folio = $(this).data("folio");
        var codigo = $(this).data("codigo");
        
        Swal.fire({
            title: '¿Reanudar conteo?',
            text: '¿Estás seguro de que quieres reanudar este conteo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, reanudar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                ReanudarConteo(folio, codigo);
            }
        });
    });

    $(document).on("click", ".btn-finalizar-conteo", function() {
        var folio = $(this).data("folio");
        var codigo = $(this).data("codigo");
        
        Swal.fire({
            title: '¿Finalizar conteo?',
            text: '¿Estás seguro de que quieres finalizar este conteo?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, finalizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                FinalizarConteo(folio, codigo);
            }
        });
    });

    $(document).on("click", ".btn-eliminar-conteo", function() {
        var folio = $(this).data("folio");
        var codigo = $(this).data("codigo");
        
        Swal.fire({
            title: '¿Eliminar conteo?',
            text: '¿Estás seguro de que quieres eliminar este conteo pausado? Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                EliminarConteo(folio, codigo);
            }
        });
    });
});

function ReanudarTodosConteos() {
    Swal.fire({
        title: '¿Reanudar todos los conteos?',
        text: '¿Estás seguro de que quieres reanudar todos los conteos pausados?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, reanudar todos',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ReanudarTodosConteos.php", 
                {}, 
                function(data) {
                    if (data.success) {
                        Swal.fire('¡Éxito!', 'Todos los conteos han sido reanudados', 'success');
                        CargarConteosPausados();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                }, 'json');
        }
    });
}

function ReanudarConteo(folio, codigo) {
    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ReanudarConteo.php", 
        { folio: folio, codigo: codigo }, 
        function(data) {
            if (data.success) {
                Swal.fire('¡Éxito!', 'Conteo reanudado correctamente', 'success');
                CargarConteosPausados();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        }, 'json');
}

function FinalizarConteo(folio, codigo) {
    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/FinalizarConteo.php", 
        { folio: folio, codigo: codigo }, 
        function(data) {
            if (data.success) {
                Swal.fire('¡Éxito!', 'Conteo finalizado correctamente', 'success');
                CargarConteosPausados();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        }, 'json');
}

function EliminarConteo(folio, codigo) {
    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/EliminarConteo.php", 
        { folio: folio, codigo: codigo }, 
        function(data) {
            if (data.success) {
                Swal.fire('¡Éxito!', 'Conteo eliminado correctamente', 'success');
                CargarConteosPausados();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        }, 'json');
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
