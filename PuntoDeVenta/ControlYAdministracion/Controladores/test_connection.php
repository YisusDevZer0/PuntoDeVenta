<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include_once "db_connect.php";
    
    if (!isset($conn) || !$conn) {
        echo json_encode(['success' => false, 'message' => 'No se pudo conectar a la base de datos']);
        exit;
    }
    
    // Probar una consulta simple
    $test_query = "SELECT 1 as test";
    $result = mysqli_query($conn, $test_query);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Conexión exitosa a la base de datos']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en consulta de prueba: ' . mysqli_error($conn)]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 