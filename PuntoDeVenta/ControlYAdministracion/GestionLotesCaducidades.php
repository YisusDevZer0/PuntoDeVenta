<?php
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Gestión de Lotes y Caducidades - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php";?>
    
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    
    <style>
        .swal2-popup {
            font-size: 1.2rem;
            color: #333;
        }
        .badge-caducidad {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-proximo {
            background-color: #ffc107;
            color: #000;
        }
        .badge-vencido {
            background-color: #dc3545;
            color: #fff;
        }
        .badge-ok {
            background-color: #28a745;
            color: #fff;
        }
    </style>
</head>

<body>
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
                            <i class="fa-solid fa-calendar-check me-2"></i>
                            Gestión de Lotes y Caducidades - <?php echo $row['Licencia']?>
                        </h6>
                        <button type="button" class="btn btn-primary btn-sm" id="btn-actualizar-lote">
                            <i class="fa-solid fa-plus me-2"></i>Actualizar Lote
                        </button>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar por código de barras:</label>
                            <input type="text" class="form-control" id="buscar-codigo" placeholder="Código de barras">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filtrar por sucursal:</label>
                            <select class="form-select" id="filtro-sucursal">
                                <option value="">Todas las sucursales</option>
                                <?php
                                $sql_sucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";
                                $result_sucursales = $conn->query($sql_sucursales);
                                while($suc = $result_sucursales->fetch_assoc()) {
                                    $selected = ($suc['ID_Sucursal'] == $row['Fk_sucursal']) ? 'selected' : '';
                                    echo "<option value='{$suc['ID_Sucursal']}' $selected>{$suc['Nombre_Sucursal']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filtrar por estado:</label>
                            <select class="form-select" id="filtro-estado">
                                <option value="">Todos</option>
                                <option value="proximo">Próximos a vencer (15 días)</option>
                                <option value="vencido">Vencidos</option>
                                <option value="ok">Vigentes</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary w-100" id="btn-limpiar-filtros">
                                <i class="fa-solid fa-eraser me-2"></i>Limpiar Filtros
                            </button>
                        </div>
                    </div>
                    
                    <div id="DataLotesCaducidades"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para actualizar lote -->
    <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;">
        <div id="Di" class="modal-dialog modal-notify modal-success">
            <div class="text-center">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #ef7980 !important;">
                        <p class="heading lead" id="TitulosCajas" style="color:white;"></p>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div id="FormCajas"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/GestionLotesCaducidades.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar datos iniciales
            CargarLotesCaducidades();
            
            // Evento para actualizar lote
            $(document).on("click", "#btn-actualizar-lote", function() {
                $('#Di').removeClass('modal-xl').addClass('modal-dialog modal-notify modal-success');
                $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/ActualizarLoteCaducidad.php", {}, function(data) {
                    $("#FormCajas").html(data);
                    $("#TitulosCajas").html("Actualizar Lote y Fecha de Caducidad");
                });
                $('#ModalEdDele').modal('show');
            });
            
            // Evento para editar lote existente
            $(document).on("click", ".btn-editar-lote", function() {
                var id = $(this).data("id");
                $('#Di').removeClass('modal-xl').addClass('modal-dialog modal-notify modal-success');
                $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/ActualizarLoteCaducidad.php", { id: id }, function(data) {
                    $("#FormCajas").html(data);
                    $("#TitulosCajas").html("Editar Lote y Fecha de Caducidad");
                });
                $('#ModalEdDele').modal('show');
            });
            
            // Filtros
            $('#buscar-codigo, #filtro-sucursal, #filtro-estado').on('change keyup', function() {
                CargarLotesCaducidades();
            });
            
            $('#btn-limpiar-filtros').on('click', function() {
                $('#buscar-codigo').val('');
                $('#filtro-sucursal').val('');
                $('#filtro-estado').val('');
                CargarLotesCaducidades();
            });
        });
    </script>

    <!-- Footer Start -->
    <?php 
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php";
    ?>
</body>
</html>
