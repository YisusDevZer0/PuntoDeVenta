<?php
// Simular una petición POST para probar ArrayTareas.php
$_POST['accion'] = '';

// Capturar la salida
ob_start();
include "Controladores/ArrayTareas.php";
$output = ob_get_clean();

echo "<h1>Prueba de ArrayTareas.php</h1>";
echo "<h2>Salida:</h2>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Intentar decodificar como JSON
$json = json_decode($output, true);
if ($json) {
    echo "<h2>JSON Decodificado:</h2>";
    echo "<pre>" . print_r($json, true) . "</pre>";
} else {
    echo "<h2>Error al decodificar JSON:</h2>";
    echo "JSON Error: " . json_last_error_msg() . "<br>";
}
?>
