<?php
// Controlador para registrar participación en sorteo después de una venta exitosa
include_once 'db_connect.php';

header('Content-Type: application/json');

$sorteoId = isset($_POST['sorteo_id']) ? intval($_POST['sorteo_id']) : 0;
$ventaTicket = isset($_POST['venta_ticket']) ? trim($_POST['venta_ticket']) : '';
$clienteId = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
$nombreCliente = isset($_POST['nombre_cliente']) ? trim($_POST['nombre_cliente']) : '';
$telefonoCliente = isset($_POST['telefono_cliente']) ? trim($_POST['telefono_cliente']) : '';
$fechaNacCliente = isset($_POST['fecha_nac_cliente']) ? trim($_POST['fecha_nac_cliente']) : null;
$folioRifa = isset($_POST['folio_rifa']) ? trim($_POST['folio_rifa']) : '';
$sucursal = isset($_POST['sucursal']) ? intval($_POST['sucursal']) : 0;
$registradoPor = isset($_POST['registrado_por']) ? trim($_POST['registrado_por']) : '';
$participa = isset($_POST['participa']) ? intval($_POST['participa']) : 1;

if ($sorteoId == 0 || empty($ventaTicket) || empty($nombreCliente)) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos para registrar participación']);
    exit;
}

// Verificar que el sorteo está activo
$sqlCheck = "SELECT ID_Sorteo FROM Sorteos WHERE ID_Sorteo = ? AND Activo = 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $sorteoId);
$stmtCheck->execute();
if ($stmtCheck->get_result()->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'El sorteo no está activo']);
    $stmtCheck->close();
    mysqli_close($conn);
    exit;
}
$stmtCheck->close();

// Registrar participación
$sql = "INSERT INTO Sorteo_Participaciones 
        (Fk_Sorteo, Fk_Venta_Ticket, Fk_Cliente, Nombre_Cliente, Telefono_Cliente, 
         FechaNacimiento_Cliente, FolioRifa, Fk_Sucursal, Participa, RegistradoPor) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$clienteIdParam = $clienteId > 0 ? $clienteId : null;
$stmt->bind_param("isissssiis", 
    $sorteoId, $ventaTicket, $clienteIdParam, $nombreCliente, 
    $telefonoCliente, $fechaNacCliente, $folioRifa, $sucursal, $participa, $registradoPor);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Participación registrada correctamente',
        'id' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar participación: ' . $conn->error]);
}

$stmt->close();
mysqli_close($conn);
?>
