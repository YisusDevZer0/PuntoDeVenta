<?php
include_once "Controladores/ControladorUsuario.php";
$sucursal_usuario = (int) ($row['Fk_Sucursal'] ?? 0);
$nombre_sucursal = $row['Nombre_Sucursal'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recepción de Traspasos - <?php echo $row['Licencia'] ?? ''; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>

        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="mb-0" style="color:#0172b6;">
                                <i class="fa-solid fa-truck-ramp-box me-2"></i>
                                Recepción de Traspasos
                            </h6>
                            <small class="text-muted">
                                Sucursal: <strong><?php echo htmlspecialchars($nombre_sucursal); ?></strong>.
                                Traspasos pendientes de recibir. Al recibir, registre lote y fecha de caducidad.
                            </small>
                        </div>
                    </div>

                    <div class="alert alert-info mb-3" role="alert">
                        <i class="fa-solid fa-lightbulb me-2"></i>
                        <strong>Lote y caducidad:</strong> Al recibir un traspaso deberá ingresar el <strong>lote</strong> y la <strong>fecha de caducidad</strong>
                        del producto. El stock se actualizará en esta sucursal y se registrará en el historial de lotes.
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Buscar por código o nombre:</label>
                            <input type="text" class="form-control" id="buscar-codigo" placeholder="Código de barras o nombre">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary" id="btn-limpiar-filtros">
                                <i class="fa-solid fa-eraser me-2"></i>Limpiar
                            </button>
                        </div>
                    </div>

                    <div id="DataRecepcionTraspasos"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalRecepcionTraspaso" tabindex="-1" style="overflow-y: auto;">
        <div id="DiRecepcion" class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0172b6;">
                    <h5 class="modal-title text-white" id="TituloRecepcion">
                        <i class="fa-solid fa-truck-ramp-box me-2"></i>Recibir traspaso – Lote y caducidad
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="FormRecepcionTraspaso"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/RecepcionTraspasos.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.btn-recibir-traspaso', function() {
                var id = $(this).data('id');
                $('#DiRecepcion').removeClass('modal-xl').addClass('modal-lg');
                $.post('Modales/RecepcionTraspasoLoteCaducidad.php', { id: id }, function(html) {
                    $('#FormRecepcionTraspaso').html(html);
                    $('#TituloRecepcion').html("<i class='fa-solid fa-truck-ramp-box me-2'></i>Recibir traspaso – Lote y caducidad");
                });
                $('#ModalRecepcionTraspaso').modal('show');
            });

            $('#buscar-codigo').on('change keyup', function() {
                CargarRecepcionTraspasos();
            });

            $('#btn-limpiar-filtros').on('click', function() {
                $('#buscar-codigo').val('');
                CargarRecepcionTraspasos();
            });
        });
    </script>

    <?php include "Modales/Modales_Errores.php"; include "Footer.php"; ?>
</body>
</html>
