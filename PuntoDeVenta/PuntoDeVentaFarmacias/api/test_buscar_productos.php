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

echo json_encode([
    'success' => true,
    'message' => 'API funcionando',
    'debug' => [
        'POST' => $_POST,
        'GET' => $_GET,
        'session' => $_SESSION,
        'sucursal' => $_SESSION['VentasPos']['Fk_Sucursal'] ?? 'No encontrada'
    ]
]);
?>
