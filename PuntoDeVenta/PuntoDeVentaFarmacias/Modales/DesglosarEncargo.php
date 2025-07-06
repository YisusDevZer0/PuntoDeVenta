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
$sql = "SELECT e.*, s.Nombre_Sucursal, c.Empleado 
        FROM encargos e 
        INNER JOIN Sucursales s ON e.Fk_Sucursal = s.ID_Sucursal 
        LEFT JOIN Cajas c ON e.Fk_Caja = c.ID_Caja
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
            <h4 class="mb-3">Detalles del Encargo</h4>
            
            <!-- Información del encargo -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Número de Ticket:</strong> <?php echo $encargo->NumTicket; ?></p>
                            <p><strong>Paciente:</strong> <?php echo $encargo->nombre_paciente; ?></p>
                            <p><strong>Medicamento:</strong> <?php echo $encargo->medicamento; ?></p>
                            <p><strong>Cantidad:</strong> <?php echo $encargo->cantidad; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Precio Total:</strong> $<?php echo number_format($encargo->precioventa, 2); ?></p>
                            <p><strong>Abono Realizado:</strong> $<?php echo number_format($encargo->abono_parcial, 2); ?></p>
                            <p><strong>Saldo Pendiente:</strong> 
                                <span class="<?php echo $saldo_pendiente > 0 ? 'text-warning' : 'text-success'; ?> fw-bold">
                                    $<?php echo number_format($saldo_pendiente, 2); ?>
                                </span>
                            </p>
                            <p><strong>Estado:</strong> 
                                <span class="badge <?php echo $encargo->estado === 'Pagado' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo $encargo->estado; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Información Adicional</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fecha de Encargo:</strong> <?php echo date("d/m/Y H:i", strtotime($encargo->fecha_encargo)); ?></p>
                            <p><strong>Sucursal:</strong> <?php echo $encargo->Nombre_Sucursal; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Empleado:</strong> <?php echo $encargo->Empleado ?: 'No asignado'; ?></p>
                            <p><strong>Forma de Pago:</strong> <?php echo $encargo->FormaDePago ?: 'No especificada'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="text-center mt-3">
                <?php if ($saldo_pendiente > 0): ?>
                    <button type="button" class="btn btn-success btn-CobrarEncargo" data-id="<?php echo $encargo_id; ?>">
                        <i class="fas fa-money-bill"></i> Cobrar Encargo
                    </button>
                    <button type="button" class="btn btn-warning btn-AbonarEncargo" data-id="<?php echo $encargo_id; ?>">
                        <i class="fas fa-plus-circle"></i> Abonar Saldo
                    </button>
                <?php else: ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Este encargo ya está completamente pagado.
                    </div>
                <?php endif; ?>
                
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Evento para cobrar encargo
    $('.btn-CobrarEncargo').on('click', function() {
        var encargoId = $(this).data('id');
        
        Swal.fire({
            title: '¿Cobrar encargo completo?',
            text: '¿Está seguro de que desea cobrar el encargo completo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, cobrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Procesando cobro...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Mostrar modal de cobro
                $.ajax({
                    url: 'Modales/CobrarEncargo.php',
                    type: 'POST',
                    data: { id: encargoId },
                    success: function(response) {
                        Swal.close();
                        $('#modalContent').html(response);
                        $('#editModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar modal de cobro:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo cargar el modal de cobro',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    });

    // Evento para abonar encargo
    $('.btn-AbonarEncargo').on('click', function() {
        var encargoId = $(this).data('id');
        
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Cargando formulario...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Mostrar modal de abono
        $.ajax({
            url: 'Modales/AbonarEncargo.php',
            type: 'POST',
            data: { id: encargoId },
            success: function(response) {
                Swal.close();
                $('#modalContent').html(response);
                $('#editModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar modal de abono:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar el modal de abono',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
});
</script> 