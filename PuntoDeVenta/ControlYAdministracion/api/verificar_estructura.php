<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Incluir conexión a la base de datos
include "../dbconect.php";

try {
    // Obtener estructura de la tabla
    $query = "DESCRIBE Suscripciones_Push";
    $result = $con->query($query);
    
    $columnas = [];
    while ($row = $result->fetch_assoc()) {
        $columnas[] = $row;
    }

    echo json_encode([
        'success' => true,
        'estructura' => $columnas
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 