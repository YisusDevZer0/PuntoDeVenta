<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Incluir conexión a la base de datos
include "../dbconect.php";

// Cargar claves VAPID
$vapidKeysFile = '../config/vapid_keys.json';
if (!file_exists($vapidKeysFile)) {
    // Si no existen las claves, generarlas
    require_once 'generar_vapid_keys.php';
}

$vapidKeys = json_decode(file_get_contents($vapidKeysFile), true);

try {
    // 1. Verificar conexión a la base de datos
    if (!isset($con) || !$con) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // 2. Verificar si hay suscripciones activas
    $query = "SELECT COUNT(*) as total FROM Suscripciones_Push WHERE Activo = 1";
    $result = $con->query($query);
    $row = $result->fetch_assoc();
    
    if ($row['total'] == 0) {
        throw new Exception("No hay dispositivos suscritos");
    }

    // 3. Obtener una suscripción de prueba
    $query = "SELECT Datos_Suscripcion FROM Suscripciones_Push WHERE Activo = 1 LIMIT 1";
    $result = $con->query($query);
    $subscription = json_decode($result->fetch_assoc()['Datos_Suscripcion'], true);

    // 4. Verificar que la suscripción es válida
    if (!isset($subscription['endpoint']) || !isset($subscription['keys']['p256dh']) || !isset($subscription['keys']['auth'])) {
        throw new Exception("Suscripción inválida o incompleta");
    }

    // 5. Preparar los datos de la notificación
    $payload = json_encode([
        'title' => 'Prueba Simple',
        'body' => 'Esta es una notificación de prueba',
        'icon' => '/assets/img/logo.png',
        'url' => 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/'
    ]);

    // 6. Obtener el endpoint y las claves de la suscripción
    $endpoint = $subscription['endpoint'];
    $p256dh = $subscription['keys']['p256dh'];
    $auth = $subscription['keys']['auth'];

    // 7. Preparar los headers de autenticación VAPID
    $audience = parse_url($endpoint, PHP_URL_SCHEME) . '://' . parse_url($endpoint, PHP_URL_HOST);
    $expiration = time() + 43200; // 12 horas

    $header = [
        'typ' => 'JWT',
        'alg' => 'ES256'
    ];

    $payload_jwt = [
        'aud' => $audience,
        'exp' => $expiration,
        'sub' => $vapidKeys['subject']
    ];

    // 8. Intentar enviar la notificación
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'TTL: 86400',
        'Authorization: Bearer ' . base64_encode(json_encode($header)) . '.' . 
                      base64_encode(json_encode($payload_jwt)) . '.' . 
                      base64_encode($vapidKeys['privateKey']),
        'Crypto-Key: p256ecdsa=' . $vapidKeys['publicKey']
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // 9. Devolver resultado
    echo json_encode([
        'success' => true,
        'message' => 'Prueba completada',
        'details' => [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error,
            'endpoint' => $endpoint,
            'vapid_public_key' => $vapidKeys['publicKey']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 