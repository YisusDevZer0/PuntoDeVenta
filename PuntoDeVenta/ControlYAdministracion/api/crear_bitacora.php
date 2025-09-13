<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaController.php";

$area = $_POST['area'] ?? '';
$semana = $_POST['semana'] ?? '';
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';
$responsable = $_POST['responsable'] ?? '';
$supervisor = $_POST['supervisor'] ?? '';
$aux_res = $_POST['aux_res'] ?? '';

if (empty($area) || empty($semana) || empty($fecha_inicio) || empty($fecha_fin) || empty($responsable)) {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos obligatorios deben ser completados'
    ]);
    exit;
}

try {
    $controller = new BitacoraLimpiezaController($conn);
    $id_bitacora = $controller->crearBitacora($area, $semana, $fecha_inicio, $fecha_fin, $responsable, $supervisor, $aux_res);
    
    if ($id_bitacora) {
        echo json_encode([
            'success' => true,
            'message' => 'Bitácora creada exitosamente',
            'id_bitacora' => $id_bitacora
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear la bitácora'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>