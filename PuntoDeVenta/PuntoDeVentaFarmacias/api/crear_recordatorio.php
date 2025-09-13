<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/RecordatoriosController.php";

$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$fecha_recordatorio = $_POST['fecha_recordatorio'] ?? '';
$prioridad = $_POST['prioridad'] ?? 'media';
$id_usuario = $_POST['id_usuario'] ?? 1; // Por defecto 1, se puede obtener de la sesión

if (empty($titulo) || empty($descripcion) || empty($fecha_recordatorio)) {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son requeridos'
    ]);
    exit;
}

try {
    $controller = new RecordatoriosController($conn);
    $resultado = $controller->crearRecordatorio($titulo, $descripcion, $fecha_recordatorio, $prioridad, $id_usuario);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Recordatorio creado exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear el recordatorio'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
