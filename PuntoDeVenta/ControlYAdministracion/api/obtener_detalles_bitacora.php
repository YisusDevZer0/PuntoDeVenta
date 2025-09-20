<?php
header('Content-Type: application/json');
include_once "../../Consultas/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaAdminController.php";

try {
    // Verificar método GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener ID de la bitácora
    $id_bitacora = $_GET['id'] ?? null;
    
    if (!$id_bitacora || !is_numeric($id_bitacora)) {
        throw new Exception('ID de bitácora inválido');
    }
    
    // Crear controlador y obtener detalles
    $controller = new BitacoraLimpiezaAdminController($conn);
    $detalles = $controller->obtenerDetallesBitacora($id_bitacora);
    
    if ($detalles) {
        echo json_encode([
            'success' => true,
            'data' => $detalles
        ]);
    } else {
        throw new Exception('Bitácora no encontrada');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>