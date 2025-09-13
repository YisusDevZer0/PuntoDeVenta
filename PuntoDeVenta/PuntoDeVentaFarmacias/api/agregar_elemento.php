<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaController.php";

$id_bitacora = $_POST['id_bitacora'] ?? null;
$elemento = $_POST['elemento'] ?? '';

if (!$id_bitacora || empty($elemento)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de bitácora y elemento son requeridos'
    ]);
    exit;
}

try {
    $controller = new BitacoraLimpiezaController($conn);
    $resultado = $controller->agregarElementoLimpieza($id_bitacora, $elemento);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Elemento agregado exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al agregar el elemento'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
