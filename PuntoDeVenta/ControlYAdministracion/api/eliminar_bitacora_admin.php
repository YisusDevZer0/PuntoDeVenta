<?php
header('Content-Type: application/json');
include_once "../../Consultas/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaAdminController.php";

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener ID de la bitácora
    $id_bitacora = $_POST['id'] ?? null;
    
    if (!$id_bitacora || !is_numeric($id_bitacora)) {
        throw new Exception('ID de bitácora inválido');
    }
    
    // Crear controlador y eliminar bitácora
    $controller = new BitacoraLimpiezaAdminController($conn);
    $eliminada = $controller->eliminarBitacora($id_bitacora);
    
    if ($eliminada) {
        echo json_encode([
            'success' => true,
            'message' => 'Bitácora eliminada exitosamente'
        ]);
    } else {
        throw new Exception('Error al eliminar la bitácora');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>