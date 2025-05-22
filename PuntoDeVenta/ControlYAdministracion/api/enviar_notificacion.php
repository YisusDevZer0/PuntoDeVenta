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
    throw new Exception("No se encontraron las claves VAPID");
}

$vapidKeys = json_decode(file_get_contents($vapidKeysFile), true);

try {
    // Obtener datos de la petición
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos requeridos
    if (!isset($data['titulo']) || !isset($data['mensaje'])) {
        throw new Exception("Título y mensaje son requeridos");
    }

    // Obtener suscripciones activas
    $query = "SELECT * FROM Suscripciones_Push WHERE Activo = 1";
    $result = $con->query($query);
    
    $enviadas = 0;
    $errores = [];
    
    while ($row = $result->fetch_assoc()) {
        $subscription = json_decode($row['Datos_Suscripcion'], true);
        
        // Verificar que sea una suscripción Web Push válida
        if (!isset($subscription['endpoint']) || 
            !isset($subscription['keys']['p256dh']) || 
            !isset($subscription['keys']['auth']) ||
            strpos($subscription['endpoint'], 'fcm.googleapis.com') !== false) {
            continue;
        }

        // Preparar payload de la notificación
        $payload = json_encode([
            'title' => $data['titulo'],
            'body' => $data['mensaje'],
            'icon' => '/assets/img/logo.png',
            'badge' => '/assets/img/logo.png',
            'data' => [
                'url' => $data['url'] ?? '/',
                'timestamp' => time(),
                'tipo' => $data['tipo'] ?? 'sistema'
            ],
            'actions' => [
                [
                    'action' => 'open',
                    'title' => 'Ver detalles'
                ],
                [
                    'action' => 'close',
                    'title' => 'Cerrar'
                ]
            ]
        ]);

        // Preparar headers de autenticación VAPID
        $audience = parse_url($subscription['endpoint'], PHP_URL_SCHEME) . '://' . parse_url($subscription['endpoint'], PHP_URL_HOST);
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

        // Enviar notificación
        $ch = curl_init($subscription['endpoint']);
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

        if ($httpCode >= 200 && $httpCode < 300) {
            $enviadas++;
        } else {
            // Si la suscripción ya no es válida, marcarla como inactiva
            if ($httpCode === 410) {
                $update_query = "UPDATE Suscripciones_Push SET Activo = 0 WHERE ID_Suscripcion = ?";
                $stmt = $con->prepare($update_query);
                $stmt->bind_param("i", $row['ID_Suscripcion']);
                $stmt->execute();
            }
            
            $errores[] = [
                'id_suscripcion' => $row['ID_Suscripcion'],
                'endpoint' => $subscription['endpoint'],
                'http_code' => $httpCode,
                'error' => $error,
                'response' => $response
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Se enviaron $enviadas notificaciones",
        'enviadas' => $enviadas,
        'errores' => $errores
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 