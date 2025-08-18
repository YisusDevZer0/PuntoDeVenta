<?php
// Prueba exacta del controlador del checador con los datos proporcionados
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Prueba Exacta del Controlador del Checador</h1>";

// Datos exactos proporcionados en la consulta
$datosPrueba = [
    'action' => 'registrar_asistencia',
    'tipo' => 'entrada',
    'latitud' => '20.9674',
    'longitud' => '-89.5926',
    'timestamp' => '2025-08-18 02:09:10'
];

echo "<h2>Datos a enviar:</h2>";
echo "<pre>";
print_r($datosPrueba);
echo "</pre>";

$url = 'http://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ChecadorController.php';

echo "<h2>URL del controlador:</h2>";
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
    } else {
        echo "<p style='color: orange;'>⚠️ La respuesta no es JSON válido</p>";
    }
}

echo "<h2>Análisis del problema:</h2>";

if ($httpCode == 404) {
    echo "<p style='color: red;'>❌ <strong>Error 404:</strong> El archivo no se encuentra en la URL especificada.</p>";
    echo "<p>Posibles causas:</p>";
    echo "<ul>";
    echo "<li>La ruta del archivo es incorrecta</li>";
    echo "<li>El archivo no existe en el servidor</li>";
    echo "<li>Problemas de configuración del servidor web</li>";
    echo "</ul>";
} elseif ($httpCode == 301) {
    echo "<p style='color: orange;'>⚠️ <strong>Redirección 301:</strong> El documento ha sido movido permanentemente.</p>";
    echo "<p>El servidor está redirigiendo la petición. Esto puede indicar:</p>";
    echo "<ul>";
    echo "<li>Cambios en la estructura de directorios</li>";
    echo "<li>Configuración de redirecciones en el servidor</li>";
    echo "<li>Problemas con la URL base</li>";
    echo "</ul>";
} elseif ($httpCode == 200) {
    echo "<p style='color: green;'>✅ <strong>Éxito:</strong> El controlador respondió correctamente.</p>";
} else {
    echo "<p style='color: red;'>❌ <strong>Error HTTP $httpCode:</strong> Respuesta inesperada del servidor.</p>";
}

echo "<h2>Recomendaciones:</h2>";
echo "<ol>";
echo "<li>Verificar que el archivo existe en la ruta correcta del servidor</li>";
echo "<li>Revisar la configuración del servidor web (Apache/Nginx)</li>";
echo "<li>Verificar los logs del servidor para más detalles</li>";
echo "<li>Probar con una URL relativa en lugar de absoluta</li>";
echo "<li>Verificar que el servidor web puede ejecutar archivos PHP</li>";
echo "</ol>";

echo "<h2>Prueba completada</h2>";
?>
