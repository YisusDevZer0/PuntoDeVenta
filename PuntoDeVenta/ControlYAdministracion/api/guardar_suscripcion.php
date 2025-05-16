<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Verificar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Se requiere método POST'
    ]);
    exit;
}

// Obtener los datos enviados
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verificar los datos
if (!$data || !isset($data['subscription'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos de suscripción inválidos'
    ]);
    exit;
}

// Obtener el ID de usuario
$userId = isset($data['user_id']) ? $data['user_id'] : 0;

// Directorio y archivo para guardar suscripciones
$subscriptionsDir = __DIR__ . '/../config/subscriptions';
$subscriptionFile = $subscriptionsDir . '/subscription_' . $userId . '.json';

// Crear directorio si no existe
if (!file_exists($subscriptionsDir)) {
    mkdir($subscriptionsDir, 0755, true);
}

// Guardar la suscripción
try {
    // Datos adicionales
    $subscriptionData = [
        'subscription' => $data['subscription'],
        'user_id' => $userId,
        'created' => date('Y-m-d H:i:s'),
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown',
        'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown'
    ];
    
    // Guardar en archivo
    file_put_contents($subscriptionFile, json_encode($subscriptionData, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'success' => true,
        'message' => 'Suscripción guardada correctamente'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la suscripción: ' . $e->getMessage()
    ]);
}
?> 