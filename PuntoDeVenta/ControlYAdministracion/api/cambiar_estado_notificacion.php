<?php
include "../dbconect.php";
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener datos desde POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$estado = isset($_POST['estado']) ? intval($_POST['estado']) : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de notificación inválido']);
    exit;
}

// Cambiar estado
$query = "UPDATE Notificaciones SET Leido = ? WHERE ID_Notificacion = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $estado, $id);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Estado de notificación actualizado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la notificación']);
}
?> 