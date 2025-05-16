<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Incluir librería para web push (si no está instalada, hay que usar "composer require minishlink/web-push")
// Para esta demostración, implementaremos los métodos básicos necesarios

// Leer las claves VAPID
$configFile = __DIR__ . '/../config/vapid_keys.json';
if (!file_exists($configFile)) {
    echo json_encode([
        'success' => false,
        'message' => 'No se encontraron las claves VAPID. Ejecute primero el script crear_vapid_keys.php'
    ]);
    exit;
}

// Obtener datos POST (si los hay)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Utilizamos subscription_id como parámetro si se proporciona
$userId = isset($data['user_id']) ? $data['user_id'] : '1';
$subscriptionFile = __DIR__ . "/../config/subscriptions/subscription_{$userId}.json";

// Verificar si existe la suscripción
if (!file_exists($subscriptionFile)) {
    echo json_encode([
        'success' => false,
        'message' => "No se encontró la suscripción para el usuario ID: {$userId}. Asegúrese de haberse suscrito primero."
    ]);
    exit;
}

try {
    // Cargar datos de claves VAPID y suscripción
    $vapidKeys = json_decode(file_get_contents($configFile), true);
    $subscription = json_decode(file_get_contents($subscriptionFile), true);
    
    // Generar encabezados JWT
    $header = ['alg' => 'ES256', 'typ' => 'JWT'];
    $headerBase64 = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
    
    // Tiempo actual y expiración (1 hora)
    $now = time();
    $expires = $now + 3600;
    
    // Payload del JWT
    $payload = [
        'aud' => getAudienceFromEndpoint($subscription['subscription']['endpoint']),
        'exp' => $expires,
        'sub' => $vapidKeys['subject']
    ];
    $payloadBase64 = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
    
    // Datos de la notificación a enviar
    $notificationPayload = json_encode([
        'title' => 'Notificación de Prueba',
        'message' => 'Esta es una notificación push de prueba desde el servidor.',
        'icon' => './img/notification-icon.png',
        'url' => './test-push.html',
        'timestamp' => time()
    ]);
    
    // Resultado de la simulación (para demostracion)
    $result = [
        'success' => true,
        'message' => 'Notificación de prueba enviada (simulación)',
        'debug' => [
            'endpoint' => $subscription['subscription']['endpoint'],
            'keys' => [
                'auth' => '***', // Omitimos por seguridad
                'p256dh' => '***' // Omitimos por seguridad
            ],
            'payload' => json_decode($notificationPayload),
            'jwt_header' => $header,
            'jwt_payload' => $payload
        ],
        'nota' => 'Esto es una simulación. Para envío real se requiere la librería web-push-php.'
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al enviar notificación: ' . $e->getMessage()
    ]);
}

// Función para extraer la audiencia (dominio) del endpoint
function getAudienceFromEndpoint($endpoint) {
    $parsed = parse_url($endpoint);
    return $parsed['scheme'] . '://' . $parsed['host'];
}
?> 