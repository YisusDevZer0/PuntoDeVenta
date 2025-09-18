<?php
// Configurar headers para JSON
header('Content-Type: application/json');

echo json_encode([
    "error" => false,
    "message" => "Archivo PHP funciona correctamente",
    "data" => ["test" => "OK"]
]);
?>
