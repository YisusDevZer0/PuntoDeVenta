<?php
// Probar directamente el controlador del checador
echo "<h2>Prueba Directa del Controlador del Checador</h2>";

// Simular una petición POST al controlador
$postData = [
    'action' => 'registrar_asistencia',
    'tipo' => 'entrada',
    'latitud' => 20.9674,
    'longitud' => -89.5926,
    'timestamp' => date('Y-m-d H:i:s')
];

echo "<h3>Datos a enviar:</h3>";
echo "<pre>" . print_r($postData, true) . "</pre>";

// Crear el contexto de la petición
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($postData)
    ]
]);

// URL del controlador
$controllerUrl = 'Controladores/ChecadorController.php';
$fullUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $controllerUrl;

echo "<h3>URL del controlador:</h3>";
echo "<p>$fullUrl</p>";

// Intentar hacer la petición
echo "<h3>Respuesta del controlador:</h3>";

try {
    $response = file_get_contents($fullUrl, false, $context);
    
    if ($response === false) {
        echo "<p style='color: red;'>❌ Error al conectar con el controlador</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Respuesta recibida del controlador</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        
        // Intentar decodificar JSON
        $jsonResponse = json_decode($response, true);
        if ($jsonResponse) {
            echo "<h4>Respuesta JSON decodificada:</h4>";
            echo "<pre>" . print_r($jsonResponse, true) . "</pre>";
        } else {
            echo "<p style='color: orange;'>⚠️ La respuesta no es JSON válido</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Excepción: " . $e->getMessage() . "</p>";
}

// Probar con cURL si está disponible
echo "<h3>Prueba con cURL:</h3>";

if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $curlResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "<p>HTTP Code: $httpCode</p>";
    
    if ($curlError) {
        echo "<p style='color: red;'>❌ Error cURL: $curlError</p>";
    } else {
        echo "<p style='color: green;'>✅ Respuesta cURL:</p>";
        echo "<pre>" . htmlspecialchars($curlResponse) . "</pre>";
        
        $jsonCurlResponse = json_decode($curlResponse, true);
        if ($jsonCurlResponse) {
            echo "<h4>Respuesta cURL JSON:</h4>";
            echo "<pre>" . print_r($jsonCurlResponse, true) . "</pre>";
        }
    }
} else {
    echo "<p style='color: orange;'>⚠️ cURL no está disponible</p>";
}

echo "<hr>";
echo "<p><a href='Checador.php'>Volver al Checador</a></p>";
?>
