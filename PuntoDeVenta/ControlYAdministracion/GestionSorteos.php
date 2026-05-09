<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Gestión de Sorteos - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <?php
   include "header.php";?>
   <style>
        .nav-pills .nav-link.active {
            background-color: #ef7980 !important;
        }
        .nav-pills .nav-link {
            color: #ef7980;
        }
   </style>
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


            <!-- Tabs + Botón -->
            <div class="container-fluid pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <ul class="nav nav-pills" id="sorteoTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-sorteos" data-bs-toggle="pill" href="#panel-sorteos" role="tab">
                                <i class="fa-solid fa-gift"></i> Sorteos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-participaciones" data-bs-toggle="pill" href="#panel-participaciones" role="tab">
                                <i class="fa-solid fa-users"></i> Participaciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-clientes" data-bs-toggle="pill" href="#panel-clientes" role="tab">
                                <i class="fa-solid fa-address-book"></i> Clientes
                            </a>
                        </li>
                    </ul>
                    <div>
                        <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                            <i class="fa-solid fa-user-plus"></i> Nuevo Cliente
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoSorteo">
                            <i class="fa-solid fa-plus"></i> Nuevo Sorteo
                        </button>
                    </div>
                </div>

                <div class="tab-content">
                    <!-- Panel Sorteos -->
                    <div class="tab-pane fade show active" id="panel-sorteos" role="tabpanel">
                        <div class="col-12">
                            <div class="bg-light rounded h-100 p-4">
                                <h6 class="mb-4" style="color:#ef7980;"><i class="fa-solid fa-trophy"></i> Gestión de Sorteos - <?php echo $row['Licencia']?></h6>
                                <div id="SorteosDisponibles"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Participaciones -->
                    <div class="tab-pane fade" id="panel-participaciones" role="tabpanel">
                        <div class="col-12">
                            <div class="bg-light rounded h-100 p-4">
                                <h6 class="mb-4" style="color:#17a2b8;"><i class="fa-solid fa-users"></i> Participaciones en Sorteos</h6>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Filtrar por sorteo:</label>
                                        <select id="filtroSorteoParticipaciones" class="form-control">
                                            <option value="0">Todos los sorteos</option>
                                            <?php
                                            $sqlFiltro = "SELECT ID_Sorteo, Nombre_Sorteo FROM Sorteos ORDER BY ID_Sorteo DESC";
                                            $resFiltro = mysqli_query($conn, $sqlFiltro);
                                            if ($resFiltro) {
                                                while ($sf = mysqli_fetch_assoc($resFiltro)) {
                                                    echo '<option value="'.$sf['ID_Sorteo'].'">'.$sf['Nombre_Sorteo'].'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div id="ParticipacionesDisponibles"></div>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Panel Clientes -->
                    <div class="tab-pane fade" id="panel-clientes" role="tabpanel">
                        <div class="col-12">
                            <div class="bg-light rounded h-100 p-4">
                                <h6 class="mb-4" style="color:#28a745;"><i class="fa-solid fa-address-book"></i> Clientes Registrados</h6>
                                <div id="ClientesDisponibles"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

<script src="js/GestionSorteosTabla.js"></script>
<script src="js/GestionSorteosParticipaciones.js"></script>
<script src="js/GestionSorteosAcciones.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoSorteo.php";
            include "Modales/EditarSorteo.php";
            include "Modales/NuevoClienteSorteo.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>

<script>
$(document).ready(function() {

    // Cargar participaciones al hacer clic en el tab
    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
        if ($(e.target).attr('id') === 'tab-participaciones') {
            CargaParticipaciones();
        }
        if ($(e.target).attr('id') === 'tab-clientes') {
            CargaClientes();
        }
    });

    // Delegar clic en botón editar
    $(document).on("click", ".btn-editar-sorteo", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        $.post("Controladores/SorteosController.php", { action: 'obtener', id: id }, function(data) {
            var resp = typeof data === 'string' ? JSON.parse(data) : data;
            if (resp.status === 'success') {
                var d = resp.data;
                $('#edit_sorteo_id').val(d.ID_Sorteo);
                $('#edit_sorteo_nombre').val(d.Nombre_Sorteo);
                $('#edit_sorteo_descripcion').val(d.Descripcion || '');
                $('#edit_sorteo_fecha_inicio').val(d.Fecha_Inicio);
                $('#edit_sorteo_fecha_fin').val(d.Fecha_Fin);
                $('#edit_sorteo_prefijo').val(d.Prefijo_Folio || '');
                $('#edit_sorteo_folio_inicio').val(d.Folio_Inicio || 1);
                
                var aplicaTodas = d.Aplica_Todas_Sucursales == 1;
                $('#edit_sorteo_aplica_todas').prop('checked', aplicaTodas);
                $('#edit_divSucursales').toggle(!aplicaTodas);
                
                // Marcar sucursales
                $('.edit-sucursal-cb').prop('checked', false);
                if (d.sucursales_ids) {
                    d.sucursales_ids.forEach(function(sid) {
                        $('#edit_suc_' + sid).prop('checked', true);
                    });
                }
            }
        });
        $('#modalEditarSorteo').modal('show');
    });

    // Delegar clic en botón toggle activo
    $(document).on("click", ".btn-toggle-sorteo", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        Swal.fire({
            title: '¿Cambiar estado del sorteo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.post("Controladores/SorteosController.php", { action: 'toggleActivo', id: id }, function(data) {
                    var resp = typeof data === 'string' ? JSON.parse(data) : data;
                    if (resp.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Estado actualizado', timer: 1500, showConfirmButton: false });
                        CargaSorteos();
                    }
                });
            }
        });
    });

    // Delegar clic en botón eliminar
    $(document).on("click", ".btn-eliminar-sorteo", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        Swal.fire({
            title: '¿Eliminar este sorteo?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.post("Controladores/SorteosController.php", { action: 'eliminar', id: id }, function(data) {
                    var resp = typeof data === 'string' ? JSON.parse(data) : data;
                    if (resp.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500, showConfirmButton: false });
                        CargaSorteos();
                    } else {
                        Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: resp.message });
                    }
                });
            }
        });
    });

    // Toggle sucursales en modal editar
    $('#edit_sorteo_aplica_todas').on('change', function() {
        $('#edit_divSucursales').toggle(!this.checked);
    });

    // Filtro de participaciones
    $('#filtroSorteoParticipaciones').on('change', function() {
        CargaParticipaciones();
    });
});
</script>
</body>

</html>
