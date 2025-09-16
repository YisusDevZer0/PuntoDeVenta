<?php
// Test raw del controlador
session_start();
$_SESSION['ControlMaestro'] = 1;

// Simular POST request
$_POST['action'] = 'registrar_asistencia';
$_POST['tipo'] = 'entrada';
$_POST['latitud'] = '21.02341360';
$_POST['longitud'] = '-89.57087560';
$_POST['timestamp'] = date('Y-m-d H:i:s');

// Capturar output
ob_start();
include 'Controladores/ChecadorController.php';
$output = ob_get_clean();

echo "=== RESPUESTA DEL CONTROLADOR ===\n";
echo $output;
echo "\n\n=== FIN DE RESPUESTA ===\n";

// Verificar si es JSON válido
$json = json_decode($output, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "\n✅ JSON válido\n";
    echo "Success: " . ($json['success'] ? 'true' : 'false') . "\n";
    echo "Message: " . ($json['message'] ?? 'N/A') . "\n";
} else {
    echo "\n❌ JSON inválido\n";
    echo "Error: " . json_last_error_msg() . "\n";
}
?>
