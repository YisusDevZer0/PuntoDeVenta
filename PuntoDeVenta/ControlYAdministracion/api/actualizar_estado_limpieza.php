<?php
header('Content-Type: application/json');
include_once "../../Consultas/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaController.php";

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener datos del POST
    $id_detalle = $_POST['id_detalle'] ?? null;
    $campo = $_POST['campo'] ?? null;
    $valor = $_POST['valor'] ?? null;
    
    if (!$id_detalle || !is_numeric($id_detalle)) {
        throw new Exception('ID de detalle inválido');
    }
    
    if (!$campo) {
        throw new Exception('Campo requerido');
    }
    
    // Validar que el campo sea válido
    $campos_validos = [
        'lunes_mat', 'lunes_vesp', 'martes_mat', 'martes_vesp',
        'miercoles_mat', 'miercoles_vesp', 'jueves_mat', 'jueves_vesp',
        'viernes_mat', 'viernes_vesp', 'sabado_mat', 'sabado_vesp',
        'domingo_mat', 'domingo_vesp'
    ];
    
    if (!in_array($campo, $campos_validos)) {
        throw new Exception('Campo no válido');
    }
    
    // Crear controlador y actualizar estado
    $controller = new BitacoraLimpiezaController($conn);
    $actualizado = $controller->actualizarEstadoLimpieza($id_detalle, $campo, $valor);
    
    if ($actualizado) {
        echo json_encode([
            'success' => true,
            'message' => 'Estado actualizado exitosamente'
        ]);
    } else {
        throw new Exception('Error al actualizar el estado');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>