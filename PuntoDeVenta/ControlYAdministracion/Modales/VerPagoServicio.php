<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Verificar que se recibió el ID del pago
if (!isset($_POST["id"]) || empty($_POST["id"])) {
    echo '<div class="alert alert-danger">Error: No se especificó el ID del pago</div>';
    exit;
}

$pago_id = intval($_POST["id"]);
$pago = null;

// Consulta para obtener los detalles del pago con información relacionada
$sql = "SELECT 
    ps.`id`, 
    ps.`nombre_paciente`, 
    ps.`Servicio`, 
    ps.`estado`, 
    ps.`costo`, 
    ps.`NumTicket`, 
    ps.`Fk_Sucursal`, 
    ps.`Fk_Caja`, 
    ps.`Empleado`,
    ps.`FormaDePago`,
    s.`Nombre_Sucursal`,
    c.`ID_Caja`
FROM 
    PagosServicios ps
JOIN 
    Sucursales s ON ps.`Fk_Sucursal` = s.`ID_Sucursal`
LEFT JOIN 
    Cajas c ON ps.`Fk_Caja` = c.`ID_Caja`
WHERE 
    ps.`id` = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo '<div class="alert alert-danger">Error en la preparación de la consulta: ' . htmlspecialchars($conn->error) . '</div>';
    exit;
}

$stmt->bind_param("i", $pago_id);
$stmt->execute();
$result = $stmt->get_result();
$pago = $result->fetch_assoc();
$stmt->close();

if (!$pago) {
    echo '<div class="alert alert-danger">No se encontró el pago de servicio especificado</div>';
    exit;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h5 class="mb-4">
                <i class="fas fa-receipt me-2"></i>Detalles del Pago de Servicio
            </h5>
        </div>
    </div>

    <div class="row">
        <!-- Información del Cliente y Servicio -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Paciente:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($pago['nombre_paciente']); ?></p>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-concierge-bell me-2"></i>Información del Servicio</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Servicio:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($pago['Servicio']); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado:</label>
                        <span class="badge bg-<?php echo $pago['estado'] === 'Pagado' ? 'success' : 'warning'; ?>">
                            <?php echo htmlspecialchars($pago['estado'] ?? 'N/A'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Pago y Ubicación -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Información del Pago</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Número de Ticket:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($pago['NumTicket']); ?></span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Costo:</label>
                        <p class="form-control-plaintext fs-4 text-success fw-bold">
                            $<?php echo number_format($pago['costo'], 2); ?>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Forma de Pago:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-info"><?php echo htmlspecialchars($pago['FormaDePago']); ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Información de Ubicación</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sucursal:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($pago['Nombre_Sucursal']); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Caja:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($pago['ID_Caja'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Empleado:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($pago['Empleado']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i>Cerrar
            </button>
        </div>
    </div>
</div>

<style>
.card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    padding: 0.75rem 1.25rem;
}

.form-control-plaintext {
    padding: 0.375rem 0;
    margin-bottom: 0;
    line-height: 1.5;
    color: #212529;
}

.form-label {
    margin-bottom: 0.5rem;
    color: #495057;
}
</style>
