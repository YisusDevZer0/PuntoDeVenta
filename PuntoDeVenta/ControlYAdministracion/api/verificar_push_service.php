<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Función para verificar conexión a los servicios de Push
function verificarServiciosPush() {
    $resultados = [];
    
    // Lista de servicios de push que podrían ser utilizados por diferentes navegadores
    $serviciosPush = [
        'https://fcm.googleapis.com' => 'Firebase Cloud Messaging (Chrome)',
        'https://updates.push.services.mozilla.com' => 'Mozilla Push Service (Firefox)',
        'https://push.api.com' => 'Apple Push Service (Safari, si está disponible)'
    ];
    
    // Verificar conectividad a cada servicio
    foreach ($serviciosPush as $url => $descripcion) {
        $resultados[$descripcion] = verificarConexion($url);
    }
    
    return $resultados;
}

// Función para verificar conectividad a un servidor
function verificarConexion($url) {
    $start = microtime(true);
    $resultado = ['accesible' => false, 'tiempo' => 0, 'error' => null];
    
    // Configuración de cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout de 5 segundos
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desactivar verificación SSL para pruebas
    
    // Ejecutar la solicitud
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    // Calcular tiempo de respuesta
    $resultado['tiempo'] = round((microtime(true) - $start) * 1000, 2) . ' ms';
    
    // Analizar resultados
    if ($error) {
        $resultado['error'] = $error;
    } else {
        // Considerar códigos 2xx, 3xx como exitosos
        $resultado['accesible'] = ($httpCode >= 200 && $httpCode < 400);
        $resultado['http_code'] = $httpCode;
    }
    
    curl_close($ch);
    return $resultado;
}

// Verificar información del sistema
$info = [
    'php_version' => PHP_VERSION,
    'os' => PHP_OS,
    'ssl_support' => extension_loaded('openssl'),
    'curl_support' => extension_loaded('curl'),
    'firewall_info' => 'No disponible (requiere información del sistema operativo)'
];

// Verificar servicios de push
$resultadosPush = verificarServiciosPush();

// Devolver resultados
echo json_encode([
    'success' => true,
    'info_sistema' => $info,
    'resultados_push' => $resultadosPush,
    'recomendaciones' => [
        'Si los servicios de push no son accesibles, podría haber:',
        '1. Problemas de red o firewall',
        '2. Restricciones de seguridad del navegador',
        '3. Bloqueo por parte del antivirus'
    ]
], JSON_PRETTY_PRINT);
?> 