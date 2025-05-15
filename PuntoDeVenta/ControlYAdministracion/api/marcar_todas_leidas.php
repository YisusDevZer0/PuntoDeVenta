<?php
include "../dbconect.php";
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// ID de sucursal del usuario actual
$sucursalID = isset($_SESSION["ID_Sucursal"]) ? $_SESSION["ID_Sucursal"] : 1;

// Marcar todas como leídas para esta sucursal
$query = "UPDATE Notificaciones SET Leido = 1 WHERE (SucursalID = ? OR SucursalID = 0) AND Leido = 0";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $sucursalID);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Todas las notificaciones marcadas como leídas']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar las notificaciones']);
}
?> 