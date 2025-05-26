<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'punto_de_venta';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => true, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

if (!isset($_SESSION['ID_Sucursal']) || !isset($_SESSION['ID_Usuario'])) {
    echo json_encode(['error' => true, 'message' => 'No autorizado']);
    exit;
}

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