<?php
session_start();
header('Content-Type: application/json');

// Ajusta los datos de conexión según tu entorno
$host = 'localhost';
$user = 'u858848268_devpezer0';
$pass = 'F9+nIIOuCh8yI6wu4!08';
$db = 'u858848268_doctorpez';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => true, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

if (!isset($_SESSION['VentasPos'])) {
    echo json_encode(['error' => true, 'message' => 'No autorizado']);
    exit;
}

$idPvUser = $_SESSION['VentasPos'];
$sql = "SELECT Fk_Sucursal FROM Usuarios_PV WHERE Id_PvUser = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $idPvUser);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['error' => true, 'message' => 'Usuario no encontrado']);
    exit;
}
$row = $res->fetch_assoc();
$idSucursal = intval($row['Fk_Sucursal']);
$stmt->close();

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['notification_id'])) {
    echo json_encode(['error' => true, 'message' => 'ID de notificación requerido']);
    exit;
}

$idNotificacion = intval($input['notification_id']);
$sql = "UPDATE notificaciones SET Leida = 1 WHERE ID_Notificacion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $idNotificacion);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => true]); 