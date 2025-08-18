<?php
// Script de prueba para el controlador del checador
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Prueba del Controlador del Checador</h1>";

// Simular datos POST
$_POST = [
    'action' => 'registrar_asistencia',
    'tipo' => 'entrada',
    'latitud' => '20.9674',
    'longitud' => '-89.5926',
    'timestamp' => '2025-08-18 02:09:10',
    'test_mode' => '1',
    'usuario_id' => '1'
];

// Simular método POST
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<h2>Datos de prueba:</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Ejecutando controlador...</h2>";

// Incluir el controlador
try {
    include_once "ChecadorController.php";
    echo "<p style='color: green;'>✅ Controlador cargado exitosamente</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error al cargar el controlador: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Prueba completada</h2>";
echo "<p>Revisa los logs del servidor para ver los detalles de la ejecución.</p>";
?>
