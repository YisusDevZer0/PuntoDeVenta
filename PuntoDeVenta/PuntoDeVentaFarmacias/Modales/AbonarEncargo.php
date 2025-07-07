<?php
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar que se recibió el ID del encargo
if (!isset($_POST["id"]) || empty($_POST["id"])) {
    echo '<p class="alert alert-danger">Error: No se especificó el ID del encargo</p>';
    exit;
}

$encargo_id = intval($_POST["id"]);

// Consulta para obtener los datos del encargo
$sql = "SELECT e.*, s.Nombre_Sucursal 
        FROM encargos e 
        INNER JOIN Sucursales s ON e.Fk_Sucursal = s.ID_Sucursal 
        WHERE e.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo '<p class="alert alert-danger">Error en la preparación de la consulta: ' . $conn->error . '</p>';
    exit;
}

$stmt->bind_param("i", $encargo_id);
$stmt->execute();
$result = $stmt->get_result();
$encargo = $result->fetch_object();
$stmt->close();

if (!$encargo) {
    echo '<p class="alert alert-danger">Error: No se encontró el encargo especificado</p>';
    exit;
}

// Calcular saldo pendiente
$saldo_pendiente = $encargo->precioventa - $encargo->abono_parcial;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">Abonar Saldo Pendiente</h4>
            
            <!-- Información del encargo -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Detalles del Encargo</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Ticket:</strong> <?php echo $encargo->NumTicket; ?></p>
                            <p><strong>Paciente:</strong> <?php echo $encargo->nombre_paciente; ?></p>
                            <p><strong>Medicamento:</strong> <?php echo $encargo->medicamento; ?></p>
                            <p><strong>Cantidad:</strong> <?php echo $encargo->cantidad; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Precio Total:</strong> $<?php echo number_format($encargo->precioventa, 2); ?></p>
                            <p><strong>Abono Realizado:</strong> $<?php echo number_format($encargo->abono_parcial, 2); ?></p>
                            <p><strong>Saldo Pendiente:</strong> <span class="text-danger fw-bold">$<?php echo number_format($saldo_pendiente, 2); ?></span></p>
                            <p><strong>Sucursal:</strong> <?php echo $encargo->Nombre_Sucursal; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de abono -->
            <form id="formAbonarEncargo" action="javascript:void(0)" method="post">
                <input type="hidden" name="encargo_id" value="<?php echo $encargo_id; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="monto_abono" class="form-label">Monto a Abonar:</label>
                            <input type="number" step="0.01" name="monto_abono" id="monto_abono" 
                                   class="form-control" max="<?php echo $saldo_pendiente; ?>" required>
                            <small class="form-text text-muted">Máximo: $<?php echo number_format($saldo_pendiente, 2); ?></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="forma_pago_abono" class="form-label">Forma de Pago:</label>
                            <select class="form-control" name="forma_pago_abono" id="forma_pago_abono" required>
                                <option value="">Seleccione forma de pago</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Efectivo y Tarjeta">Efectivo y Tarjeta</option>
                                <option value="Efectivo Y Credito">Efectivo y Crédito</option>
                                <option value="Efectivo Y Transferencia">Efectivo y Transferencia</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="efectivo_recibido_abono" class="form-label">Efectivo Recibido:</label>
                            <input type="number" step="0.01" name="efectivo_recibido_abono" id="efectivo_recibido_abono" 
                                   class="form-control" value="0.00" onkeyup="calcularCambioAbono()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cambio_abono" class="form-label">Cambio:</label>
                            <input type="text" name="cambio_abono" id="cambio_abono" class="form-control" value="$0.00" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="observaciones_abono" class="form-label">Observaciones (opcional):</label>
                            <textarea class="form-control" name="observaciones_abono" id="observaciones_abono" 
                                      rows="3" placeholder="Observaciones sobre el abono..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-plus-circle"></i> Confirmar Abono
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calcularCambioAbono() {
    var montoAbono = parseFloat(document.getElementById('monto_abono').value) || 0;
    var efectivoRecibido = parseFloat(document.getElementById('efectivo_recibido_abono').value) || 0;
    var cambio = efectivoRecibido - montoAbono;
    
    if (cambio >= 0) {
        document.getElementById('cambio_abono').value = '$' + cambio.toFixed(2);
    } else {
        document.getElementById('cambio_abono').value = '$0.00';
    }
}

$(document).ready(function() {
    // Validar que el monto de abono no exceda el saldo pendiente
    $('#monto_abono').on('input', function() {
        var montoAbono = parseFloat($(this).val()) || 0;
        var saldoPendiente = <?php echo $saldo_pendiente; ?>;
        
        if (montoAbono > saldoPendiente) {
            $(this).val(saldoPendiente);
            Swal.fire({
                icon: 'warning',
                title: 'Monto excedido',
                text: 'El monto no puede exceder el saldo pendiente',
                confirmButtonText: 'Aceptar'
            });
        }
    });
    
    // Manejar el envío del formulario
    $('#formAbonarEncargo').on('submit', function(e) {
        e.preventDefault();
        
        var montoAbono = parseFloat($('#monto_abono').val());
        var formaPago = $('#forma_pago_abono').val();
        var saldoPendiente = <?php echo $saldo_pendiente; ?>;
        
        if (!formaPago) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar una forma de pago',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        if (montoAbono <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El monto a abonar debe ser mayor a 0',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        if (montoAbono > saldoPendiente) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El monto no puede exceder el saldo pendiente',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        // Confirmar el abono
        Swal.fire({
            title: '¿Confirmar abono?',
            text: '¿Está seguro de que desea procesar el abono por $' + montoAbono.toFixed(2) + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, abonar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Procesando abono...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Enviar datos del abono
                $.ajax({
                    url: 'Controladores/AbonarEncargoController.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Abono exitoso!',
                                text: response.message,
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                $('#editModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al procesar el abono',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    });
    
    // Inicializar cálculo de cambio
    calcularCambioAbono();
});
</script> 