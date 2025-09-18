<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Configurar headers para JSON
header('Content-Type: application/json');

// Incluir la conexión a la base de datos
include_once "../Controladores/db_connect.php";

// Verificar si la conexión existe
if (!isset($conn)) {
    echo json_encode([
        "error" => true,
        "message" => "Variable \$conn no está definida",
        "data" => []
    ]);
    exit;
}

// Verificar conexión
if (!$conn) {
    echo json_encode([
        "error" => true,
        "message" => "Error de conexión a la base de datos",
        "data" => []
    ]);
    exit;
}

// Probar consulta simple
$sql = "SELECT 1 as test";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "error" => true,
        "message" => "Error en consulta de prueba: " . $conn->error,
        "data" => []
    ]);
    exit;
}

// Si llegamos aquí, todo está bien
echo json_encode([
    "error" => false,
    "message" => "Conexión exitosa",
    "data" => ["test" => "OK"]
]);
?>
