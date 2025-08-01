<?php
// Archivo de prueba simple para verificar la conexión
header('Content-Type: application/json');

try {
    include("Controladores/db_connect.php");
    
    // Prueba de conexión simple
    $sql_test = "SELECT COUNT(*) as total FROM Usuarios_PV";
    $result_test = $conn->query($sql_test);
    
    if ($result_test) {
        $row_test = $result_test->fetch_assoc();
        echo json_encode([
            "success" => true,
            "total_usuarios" => $row_test['total'],
            "message" => "Conexión exitosa"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Error en consulta: " . $conn->error
        ]);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => "Error: " . $e->getMessage()
    ]);
}
?> 