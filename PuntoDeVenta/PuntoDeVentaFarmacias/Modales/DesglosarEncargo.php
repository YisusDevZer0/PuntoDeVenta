<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Verificar que se recibió el ID del encargo
if (!isset($_POST["id"]) || empty($_POST["id"])) {
    echo '<p class="alert alert-danger">Error: No se especificó el ID del encargo</p>';
    exit;
}

$encargo_id = intval($_POST["id"]);

// Consulta para obtener los datos del encargo
$sql = "SELECT 
    e.`id`, 
    e.`nombre_paciente`, 
    e.`medicamento`, 
    e.`cantidad`, 
    e.`precioventa`, 
    e.`fecha_encargo`, 
    e.`estado`, 
    e.`costo`, 
    e.`abono_parcial`, 
    e.`NumTicket`, 
    e.`Fk_Sucursal`, 
    e.`Fk_Caja`, 
    e.`Empleado`,
    s.`Nombre_Sucursal`,
    c.`ID_Caja`
FROM 
    encargos e
JOIN 
    Sucursales s ON e.`Fk_Sucursal` = s.`ID_Sucursal`
LEFT JOIN 
    Cajas c ON e.`Fk_Caja` = c.`ID_Caja`
WHERE 
    e.`id` = ?";

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
            <h4 class="text-center mb-4">Detalles del Encargo</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Información del Paciente</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($encargo->nombre_paciente); ?></p>
                            <p><strong>Medicamento:</strong> <?php echo htmlspecialchars($encargo->medicamento); ?></p>
                            <p><strong>Cantidad:</strong> <?php echo $encargo->cantidad; ?></p>
                            <p><strong>Fecha de Encargo:</strong> <?php echo date('d/m/Y', strtotime($encargo->fecha_encargo)); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Información Financiera</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Precio de Venta:</strong> $<?php echo number_format($encargo->precioventa, 2); ?></p>
                            <p><strong>Costo:</strong> $<?php echo number_format($encargo->costo, 2); ?></p>
                            <p><strong>Abono Realizado:</strong> $<?php echo number_format($encargo->abono_parcial, 2); ?></p>
                            <p><strong>Saldo Pendiente:</strong> <span class="badge bg-warning text-dark">$<?php echo number_format($saldo_pendiente, 2); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Información del Sistema</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Número de Ticket:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($encargo->NumTicket); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Estado:</strong> 
                                        <?php 
                                        $estado_class = '';
                                        switch($encargo->estado) {
                                            case 'Pendiente':
                                                $estado_class = 'bg-warning text-dark';
                                                break;
                                            case 'Pagado':
                                                $estado_class = 'bg-success';
                                                break;
                                            case 'Cancelado':
                                                $estado_class = 'bg-danger';
                                                break;
                                            default:
                                                $estado_class = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?php echo $estado_class; ?>"><?php echo htmlspecialchars($encargo->estado); ?></span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Sucursal:</strong> <?php echo htmlspecialchars($encargo->Nombre_Sucursal); ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Empleado:</strong> <?php echo htmlspecialchars($encargo->Empleado); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Caja:</strong> <?php echo $encargo->ID_Caja; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <?php if ($saldo_pendiente > 0): ?>
                        <button type="button" class="btn btn-success" onclick="cobrarEncargo(<?php echo $encargo->id; ?>)">
                            <i class="fas fa-money-bill"></i> Cobrar Encargo
                        </button>
                        <button type="button" class="btn btn-warning" onclick="abonarEncargo(<?php echo $encargo->id; ?>)">
                            <i class="fas fa-plus-circle"></i> Abonar Saldo
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cobrarEncargo(encargoId) {
    $('#editModal').modal('hide');
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
            $.ajax({
                url: 'Modales/CobrarEncargo.php',
                type: 'POST',
                data: { id: encargoId },
                success: function(response) {
                    $('#modalContent').html(response);
                    $('#editModal').modal('show');
                },
                error: function(xhr, status, error) {
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
}
function abonarEncargo(encargoId) {
    $('#editModal').modal('hide');
    $.ajax({
        url: 'Modales/AbonarEncargo.php',
        type: 'POST',
        data: { id: encargoId },
        success: function(response) {
            $('#modalContent').html(response);
            $('#editModal').modal('show');
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar el modal de abono',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}
</script> 