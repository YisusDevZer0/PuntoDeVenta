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
    $id_bitacora = $_POST['id_bitacora'] ?? null;
    $elemento = $_POST['elemento'] ?? '';
    
    if (!$id_bitacora || !is_numeric($id_bitacora)) {
        throw new Exception('ID de bitácora inválido');
    }
    
    if (empty(trim($elemento))) {
        throw new Exception('El elemento es requerido');
    }
    
    // Crear controlador y agregar elemento
    $controller = new BitacoraLimpiezaController($conn);
    $agregado = $controller->agregarElementoLimpieza($id_bitacora, $elemento);
    
    if ($agregado) {
        echo json_encode([
            'success' => true,
            'message' => 'Elemento agregado exitosamente'
        ]);
    } else {
        throw new Exception('Error al agregar el elemento');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>