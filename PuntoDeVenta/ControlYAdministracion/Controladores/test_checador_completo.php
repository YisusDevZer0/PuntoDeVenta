<?php
// Script de prueba completo para el controlador del checador
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Prueba Completa del Controlador del Checador</h1>";

// Función para hacer la petición POST
function testController($data) {
    $url = 'http://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ChecadorController.php';
    
    // Configurar cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    // Ejecutar la petición
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error
    ];
}

// Datos de prueba
$testData = [
    'action' => 'registrar_asistencia',
    'tipo' => 'entrada',
    'latitud' => '20.9674',
    'longitud' => '-89.5926',
    'timestamp' => '2025-08-18 02:09:10',
    'test_mode' => '1',
    'usuario_id' => '1'
];

echo "<h2>Datos de prueba:</h2>";
echo "<pre>";
print_r($testData);
echo "</pre>";

echo "<h2>Realizando petición al controlador...</h2>";

// Hacer la petición
$result = testController($testData);

echo "<h3>Resultado de la petición:</h3>";
echo "<p><strong>Código HTTP:</strong> " . $result['http_code'] . "</p>";

if ($result['error']) {
    echo "<p style='color: red;'><strong>Error cURL:</strong> " . $result['error'] . "</p>";
}

echo "<p><strong>Respuesta:</strong></p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
echo htmlspecialchars($result['response']);
echo "</pre>";

// Intentar decodificar JSON si es posible
if ($result['response']) {
    $jsonResponse = json_decode($result['response'], true);
    if ($jsonResponse) {
        echo "<h3>Respuesta JSON decodificada:</h3>";
        echo "<pre>";
        print_r($jsonResponse);
        echo "</pre>";
    }
}

echo "<h2>Prueba completada</h2>";
?>
