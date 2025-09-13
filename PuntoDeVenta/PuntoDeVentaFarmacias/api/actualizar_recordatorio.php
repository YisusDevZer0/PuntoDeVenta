<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/RecordatoriosController.php";

$id_recordatorio = $_POST['id_recordatorio'] ?? null;
$estado = $_POST['estado'] ?? '';

if (!$id_recordatorio || empty($estado)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de recordatorio y estado son requeridos'
    ]);
    exit;
}

$estados_validos = ['activo', 'completado', 'cancelado'];
if (!in_array($estado, $estados_validos)) {
    echo json_encode([
        'success' => false,
        'message' => 'Estado no válido'
    ]);
    exit;
}

try {
    $controller = new RecordatoriosController($conn);
    $resultado = $controller->actualizarEstadoRecordatorio($id_recordatorio, $estado);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Recordatorio actualizado exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el recordatorio'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
