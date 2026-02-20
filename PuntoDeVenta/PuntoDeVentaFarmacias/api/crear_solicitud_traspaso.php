<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['VentasPos'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

$sucursal_solicitante = (int) $row['Fk_Sucursal'];
$sucursal_solicitada = isset($_POST['Fk_sucursal_solicitada']) ? (int) $_POST['Fk_sucursal_solicitada'] : 0;
$id_prod = isset($_POST['ID_Prod_POS']) ? (int) $_POST['ID_Prod_POS'] : 0;
$cod_barra = isset($_POST['Cod_Barra']) ? trim($_POST['Cod_Barra']) : '';
$nombre_prod = isset($_POST['Nombre_Prod']) ? trim($_POST['Nombre_Prod']) : '';
$cantidad = isset($_POST['Cantidad_solicitada']) ? (int) $_POST['Cantidad_solicitada'] : 1;

if ($sucursal_solicitada <= 0 || $sucursal_solicitante <= 0) {
    echo json_encode(['success' => false, 'message' => 'Faltan sucursal de origen o destino.']);
    exit();
}
if ($sucursal_solicitada == $sucursal_solicitante) {
    echo json_encode(['success' => false, 'message' => 'No puede solicitar traspaso a la misma sucursal.']);
    exit();
}
if ($cantidad < 1) {
    echo json_encode(['success' => false, 'message' => 'La cantidad debe ser al menos 1.']);
    exit();
}

$solicitado_por = $row['Nombre_Apellidos'] ?? '';
$id_hod = $row['Licencia'] ?? '';

$sql = "INSERT INTO Solicitudes_Traspaso_Sucursales 
    (Fk_sucursal_solicitante, Fk_sucursal_solicitada, ID_Prod_POS, Cod_Barra, Nombre_Prod, Cantidad_solicitada, Estatus, Solicitado_por, ID_H_O_D) 
    VALUES (?, ?, ?, ?, ?, ?, 'Pendiente', ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
    exit();
}
$stmt->bind_param("iiississ", $sucursal_solicitante, $sucursal_solicitada, $id_prod, $cod_barra, $nombre_prod, $cantidad, $solicitado_por, $id_hod);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Solicitud registrada. La sucursal y el administrador la verán en Solicitudes entre sucursales.', 'ID_Solicitud' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la solicitud.']);
}
$stmt->close();
