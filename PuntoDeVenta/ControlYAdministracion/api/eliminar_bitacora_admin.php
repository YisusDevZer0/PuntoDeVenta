<?php
include_once "../Controladores/ControladorUsuario.php";
include_once "../Controladores/BitacoraLimpiezaAdminController.php";

// Verificar sesión administrativa
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$controller = new BitacoraLimpiezaAdminController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bitacora = $_POST['id_bitacora'] ?? null;
    
    if (!$id_bitacora) {
        echo json_encode(['success' => false, 'message' => 'ID de bitácora requerido']);
        exit;
    }
    
    $resultado = $controller->eliminarBitacoraAdmin($id_bitacora);
    
    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Bitácora eliminada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la bitácora']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
