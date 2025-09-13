<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/RecordatoriosController.php";

$estado = $_POST['estado'] ?? 'activo';

try {
    $controller = new RecordatoriosController($conn);
    $recordatorios = $controller->obtenerRecordatorios($estado);
    
    echo json_encode([
        'success' => true,
        'data' => $recordatorios
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener recordatorios: ' . $e->getMessage()
    ]);
}
?>
