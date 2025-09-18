<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Configurar headers para JSON
header('Content-Type: application/json');

echo json_encode([
    "error" => false,
    "message" => "Archivo PHP funciona correctamente",
    "data" => ["test" => "OK"]
]);
?>
