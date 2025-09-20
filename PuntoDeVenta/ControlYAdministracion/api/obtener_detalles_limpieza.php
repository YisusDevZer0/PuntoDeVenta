<?php
header('Content-Type: application/json');
include_once "../../Consultas/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaController.php";

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener ID de la bitácora
    $id_bitacora = $_POST['id_bitacora'] ?? null;
    
    if (!$id_bitacora || !is_numeric($id_bitacora)) {
        throw new Exception('ID de bitácora inválido');
    }
    
    // Crear controlador y obtener detalles
    $controller = new BitacoraLimpiezaController($conn);
    $detalles = $controller->obtenerDetallesLimpieza($id_bitacora);
    
    echo json_encode([
        'success' => true,
        'data' => $detalles
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>