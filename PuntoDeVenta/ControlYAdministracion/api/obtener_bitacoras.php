<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaController.php";

try {
    $controller = new BitacoraLimpiezaController($conn);
    $bitacoras = $controller->obtenerBitacoras();
    
    echo json_encode([
        'success' => true,
        'data' => $bitacoras
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener bitácoras: ' . $e->getMessage()
    ]);
}
?>
