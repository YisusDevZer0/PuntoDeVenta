<?php
header('Content-Type: application/json');
include_once "../../Consultas/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaAdminController.php";

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener datos del POST
    $datos = [
        'sucursal_id' => $_POST['sucursal_id'] ?? null,
        'area' => $_POST['area'] ?? '',
        'semana' => $_POST['semana'] ?? '',
        'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
        'fecha_fin' => $_POST['fecha_fin'] ?? '',
        'responsable' => $_POST['responsable'] ?? '',
        'supervisor' => $_POST['supervisor'] ?? '',
        'aux_res' => $_POST['aux_res'] ?? '',
        'observaciones' => $_POST['observaciones'] ?? '',
        'estado' => $_POST['estado'] ?? 'Activa'
    ];
    
    // Validar datos requeridos
    $campos_requeridos = ['area', 'semana', 'fecha_inicio', 'fecha_fin', 'responsable', 'supervisor'];
    foreach ($campos_requeridos as $campo) {
        if (empty($datos[$campo])) {
            throw new Exception("El campo {$campo} es requerido");
        }
    }
    
    // Validar fechas
    if ($datos['fecha_inicio'] > $datos['fecha_fin']) {
        throw new Exception("La fecha de inicio no puede ser posterior a la fecha fin");
    }
    
    // Crear controlador y crear bitácora
    $controller = new BitacoraLimpiezaAdminController($conn);
    $id_bitacora = $controller->crearBitacora($datos);
    
    if ($id_bitacora) {
        echo json_encode([
            'success' => true,
            'message' => 'Bitácora creada exitosamente',
            'id_bitacora' => $id_bitacora
        ]);
    } else {
        throw new Exception('Error al crear la bitácora');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>