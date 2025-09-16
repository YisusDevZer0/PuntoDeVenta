<?php
include_once "../Controladores/ControladorUsuario.php";
include_once "../Controladores/BitacoraLimpiezaAdminControllerSimple.php";

// Verificar sesión administrativa
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$controller = new BitacoraLimpiezaAdminControllerSimple($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bitacora = $_POST['id_bitacora'] ?? null;
    
    if (!$id_bitacora) {
        echo json_encode(['success' => false, 'message' => 'ID de bitácora requerido']);
        exit;
    }
    
    $detalles = $controller->obtenerDetallesBitacora($id_bitacora);
    
    if ($detalles) {
        echo json_encode(['success' => true, 'data' => $detalles]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Bitácora no encontrada']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
