<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    include_once "../dbconect.php";
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexión exitosa',
        'database' => 'u858848268_doctorpez'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
