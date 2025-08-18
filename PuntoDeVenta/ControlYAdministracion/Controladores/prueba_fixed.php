<?php
// Prueba del controlador corregido
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Prueba del Controlador Corregido</h1>";

// Datos de prueba con modo de prueba habilitado
$datosPrueba = [
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
print_r($datosPrueba);
echo "</pre>";

// URL del archivo corregido
$url = 'http://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ChecadorController_fixed.php';

echo "<h2>URL del controlador corregido:</h2>";
echo "<p>$url</p>";

echo "<h2>Realizando petición...</h2>";

// Configurar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datosPrueba));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_HEADER, true);

// Ejecutar la petición
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

echo "<h3>Resultado de la petición:</h3>";
echo "<p><strong>Código HTTP:</strong> $httpCode</p>";

if ($error) {
    echo "<p style='color: red;'><strong>Error cURL:</strong> $error</p>";
}

// Separar headers y body
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "<h3>Headers de respuesta:</h3>";
echo "<pre style='background: #f0f0f0; padding: 10px; border: 1px solid #ccc;'>";
echo htmlspecialchars($headers);
echo "</pre>";

echo "<h3>Respuesta del controlador:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
echo htmlspecialchars($body);
echo "</pre>";

// Intentar decodificar JSON
if ($body) {
    $jsonResponse = json_decode($body, true);
    if ($jsonResponse) {
        echo "<h3>Respuesta JSON decodificada:</h3>";
        echo "<pre>";
        print_r($jsonResponse);
        echo "</pre>";
        
        if ($jsonResponse['success']) {
            echo "<p style='color: green; font-weight: bold;'>✅ ¡Éxito! El controlador funcionó correctamente.</p>";
        } else {
            echo "<p style='color: orange; font-weight: bold;'>⚠️ El controlador respondió pero con un error: " . $jsonResponse['message'] . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ La respuesta no es JSON válido</p>";
    }
}

echo "<h2>Análisis del resultado:</h2>";

if ($httpCode == 200) {
    echo "<p style='color: green;'>✅ <strong>Éxito HTTP 200:</strong> El servidor respondió correctamente.</p>";
} elseif ($httpCode == 500) {
    echo "<p style='color: red;'>❌ <strong>Error HTTP 500:</strong> Error interno del servidor.</p>";
    echo "<p>Esto puede indicar que aún hay problemas con el archivo o la configuración.</p>";
} elseif ($httpCode == 404) {
    echo "<p style='color: red;'>❌ <strong>Error 404:</strong> El archivo no se encuentra.</p>";
    echo "<p>Necesitas subir el archivo corregido al servidor.</p>";
} else {
    echo "<p style='color: red;'>❌ <strong>Error HTTP $httpCode:</strong> Respuesta inesperada del servidor.</p>";
}

echo "<h2>Próximos pasos:</h2>";
echo "<ol>";
echo "<li>Si el código HTTP es 200, el controlador está funcionando correctamente</li>";
echo "<li>Si hay errores, revisa los logs del servidor para más detalles</li>";
echo "<li>Una vez que funcione, puedes reemplazar el archivo original con el corregido</li>";
echo "<li>Prueba con diferentes datos para asegurar que todo funciona</li>";
echo "</ol>";

echo "<h2>Para reemplazar el archivo original:</h2>";
echo "<p>Una vez que confirmes que el archivo corregido funciona, puedes:</p>";
echo "<ol>";
echo "<li>Hacer una copia de seguridad del archivo original</li>";
echo "<li>Renombrar <code>ChecadorController_fixed.php</code> a <code>ChecadorController.php</code></li>";
echo "<li>Probar nuevamente con la URL original</li>";
echo "</ol>";

echo "<h2>Prueba completada</h2>";
?>
