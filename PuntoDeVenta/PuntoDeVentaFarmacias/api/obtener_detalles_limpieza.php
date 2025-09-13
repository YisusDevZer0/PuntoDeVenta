<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaController.php";

$id_bitacora = $_POST['id_bitacora'] ?? null;

if (!$id_bitacora) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de bitácora requerido'
    ]);
    exit;
}

try {
    $controller = new BitacoraLimpiezaController($conn);
    $detalles = $controller->obtenerDetallesLimpieza($id_bitacora);
    
    echo json_encode([
        'success' => true,
        'data' => $detalles
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener detalles: ' . $e->getMessage()
    ]);
}
?>
