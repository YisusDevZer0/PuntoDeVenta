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
    $resultado = $controller->eliminarBitacora($id_bitacora);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Bitácora eliminada exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar la bitácora'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>