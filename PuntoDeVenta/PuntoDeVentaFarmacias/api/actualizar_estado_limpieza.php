<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaController.php";

$id_detalle = $_POST['id_detalle'] ?? null;
$campo = $_POST['campo'] ?? '';
$valor = $_POST['valor'] ?? 0;

if (!$id_detalle || empty($campo)) {
    echo json_encode([
        'success' => false,
        'message' => 'Parámetros requeridos faltantes'
    ]);
    exit;
}

// Validar que el campo sea válido
$campos_validos = [
    'lunes_mat', 'lunes_vesp', 'martes_mat', 'martes_vesp',
    'miercoles_mat', 'miercoles_vesp', 'jueves_mat', 'jueves_vesp',
    'viernes_mat', 'viernes_vesp', 'sabado_mat', 'sabado_vesp',
    'domingo_mat', 'domingo_vesp'
];

if (!in_array($campo, $campos_validos)) {
    echo json_encode([
        'success' => false,
        'message' => 'Campo no válido'
    ]);
    exit;
}

try {
    $controller = new BitacoraLimpiezaController($conn);
    $resultado = $controller->actualizarEstadoLimpieza($id_detalle, $campo, $valor);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Estado actualizado exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el estado'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
