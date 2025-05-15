<?php
include "../database/db_connect.php";
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de notificación inválido']);
    exit;
}

$query = "UPDATE Notificaciones SET Leido = 1 WHERE ID_Notificacion = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
}
?> 