<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['VentasPos'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $sucursal_id = $row['Fk_Sucursal'];
    $usuario_id = $row['Id_PvUser'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Sesión válida',
        'data' => [
            'sucursal_id' => $sucursal_id,
            'usuario_id' => $usuario_id,
            'sucursal_nombre' => $row['Nombre_Sucursal'],
            'usuario_nombre' => $row['Nombre_Apellidos']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
