<?php
include_once "Controladores/ControladorUsuario.php";
$sucursal_usuario = (int) ($row['Fk_Sucursal'] ?? $row['Fk_sucursal'] ?? 0);
$nombre_sucursal = $row['Nombre_Sucursal'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de Lotes y Caducidades - Farmacias - <?php echo $row['Licencia'] ?? ''; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <style>
        .swal2-popup { font-size: 1.2rem; color: #333; }
        .badge-caducidad { padding: 5px 10px; border-radius: 4px; font-size: 12px; }
        .badge-proximo { background-color: #ffc107; color: #000; }
        .badge-vencido { background-color: #dc3545; color: #fff; }
        .badge-ok { background-color: #28a745; color: #fff; }
    </style>
    <?php include "header.php"; ?>
</head>
<body>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>

        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="mb-0" style="color:#0172b6;">
                                <i class="fa-solid fa-calendar-check me-2"></i>
                                Gestión de Lotes y Caducidades - Farmacias
                            </h6>
                            <small class="text-muted">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Sucursal: <strong><?php echo htmlspecialchars($nombre_sucursal); ?></strong>.
                                Solo puede registrar lotes cuando hay stock sin cubrir por lote/caducidad.
                            </small>
                        </div>
                        <button type="button" class="btn btn-primary" id="btn-actualizar-lote">
                            <i class="fa-solid fa-plus me-2"></i>Registrar lote
                        </button>
                    </div>

                    <div class="alert alert-info mb-3" role="alert">
                        <i class="fa-solid fa-lightbulb me-2"></i>
                        <strong>Restricción:</strong> Solo puede dar de alta nuevos lotes si el producto tiene unidades
                        sin asignar a lote/caducidad. Cuando todo el stock esté cubierto, no se permiten más altas.
                        Sí puede editar lotes existentes.
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar por código de barras:</label>
                            <input type="text" class="form-control" id="buscar-codigo" placeholder="Código de barras">
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

                    <input type="hidden" id="sucursal-usuario" value="<?php echo $sucursal_usuario; ?>">
                    <div id="DataLotesCaducidades"></div>
                </div>
            </div>
        </div>
    </div>

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

    <script src="js/GestionLotesCaducidadesFarmacias.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar datos iniciales
            CargarLotesCaducidadesFarmacias();

            // Botón menú (hamburguesa): ocultar/desplegar sidebar
            $(document).on("click", ".sidebar-toggler", function(e) {
                e.preventDefault();
                $(".sidebar").toggleClass("active");
                $(".content").toggleClass("active");
            });

            $(document).on("click", "#btn-actualizar-lote", function() {
                var suc = $("#sucursal-usuario").val();
                $('#Di').removeClass('modal-xl').addClass('modal-dialog');
                $.post("Modales/ActualizarLoteCaducidadFarmacias.php", { id: 0, sucursal: suc }, function(data) {
                    $("#FormCajas").html(data);
                    $("#TitulosCajas").html("<i class='fa-solid fa-plus-circle me-2'></i>Registrar Lote");
                });
                $('#ModalEdDele').modal('show');
            });

            $(document).on("click", ".btn-editar-lote", function() {
                var id = $(this).data("id");
                var suc = $("#sucursal-usuario").val();
                $('#Di').removeClass('modal-xl').addClass('modal-dialog');
                $.post("Modales/ActualizarLoteCaducidadFarmacias.php", { id: id, sucursal: suc }, function(data) {
                    $("#FormCajas").html(data);
                    $("#TitulosCajas").html("<i class='fa-solid fa-edit me-2'></i>Editar Lote, Fecha o Cantidad");
                });
                $('#ModalEdDele').modal('show');
            });

            $('#buscar-codigo, #filtro-estado').on('change keyup', function() {
                CargarLotesCaducidadesFarmacias();
            });

            $('#btn-limpiar-filtros').on('click', function() {
                $('#buscar-codigo').val('');
                $('#filtro-estado').val('');
                CargarLotesCaducidadesFarmacias();
            });
        });
    </script>

    <?php include "Modales/Modales_Errores.php"; include "Footer.php"; ?>
</body>
</html>
