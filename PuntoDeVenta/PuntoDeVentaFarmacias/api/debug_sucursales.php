<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    // Consultar estructura de la tabla Sucursales
    $sql = "DESCRIBE Sucursales";
    $result = $con->query($sql);
    
    $columnas = [];
    while ($row = $result->fetch_assoc()) {
        $columnas[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'columnas' => $columnas
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
