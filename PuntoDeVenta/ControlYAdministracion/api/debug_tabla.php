<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    // Verificar si la tabla existe
    $sql = "SHOW TABLES LIKE 'caducados_historial'";
    $result = $con->query($sql);
    
    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'La tabla caducados_historial no existe']);
        exit;
    }
    
    // Obtener la estructura de la tabla
    $sql = "DESCRIBE caducados_historial";
    $result = $con->query($sql);
    
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'table_exists' => true,
        'columns' => $columns
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>


