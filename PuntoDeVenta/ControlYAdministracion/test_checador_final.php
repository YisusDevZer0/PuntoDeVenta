<?php
session_start();

// Simulamos una sesión de usuario válida
$_SESSION['Id_PvUser'] = 1;
$_SESSION['Rol'] = 'Admin';

// Incluimos el controlador
require_once 'Controladores/ChecadorController.php';
require_once '../Consultas/db_connect.php';

// Configuramos el header para JSON
header('Content-Type: application/json');

try {
    // Creamos la conexión
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    // Creamos el controlador
    $controller = new ChecadorController($conn);
    
    // Probamos el registro de asistencia
    $resultado = $controller->registrarAsistencia(
        1, // usuario_id
        'entrada', // tipo
        19.4326, // latitud
        -99.1332, // longitud
        date('Y-m-d H:i:s') // timestamp
    );
    
    echo json_encode([
        'test' => 'registrarAsistencia',
        'resultado' => $resultado,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
