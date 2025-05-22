<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Cargar configuración
$config = require_once 'onesignal_config.php';

try {
    // Obtener datos de la petición
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos requeridos
    if (!isset($data['titulo']) || !isset($data['mensaje'])) {
        throw new Exception("Título y mensaje son requeridos");
    }

    // Preparar los datos de la notificación
    $fields = [
        'app_id' => $config['app_id'],
        'contents' => ['es' => $data['mensaje']],
        'headings' => ['es' => $data['titulo']],
        'included_segments' => ['All'], // Enviar a todos los suscriptores
        'url' => $data['url'] ?? $config['default_url'],
        'chrome_web_icon' => $data['icon'] ?? $config['default_icon'],
        'data' => [
            'tipo' => $data['tipo'] ?? 'sistema',
            'timestamp' => time()
        ]
    ];

    // Si se especifica un usuario específico
    if (isset($data['user_id'])) {
        $fields['include_external_user_ids'] = [$data['user_id']];
        unset($fields['included_segments']);
    }

    // Configurar la petición cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $config['rest_api_key']
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Ejecutar la petición
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        $result = json_decode($response, true);
        echo json_encode([
            'success' => true,
            'message' => 'Notificación enviada correctamente',
            'id' => $result['id'] ?? null,
            'recipients' => $result['recipients'] ?? 0
        ]);
    } else {
        throw new Exception("Error al enviar notificación: " . ($error ?: $response));
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 