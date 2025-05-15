<?php
header('Content-Type: application/json');
require_once '../../config/conexion.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener y decodificar el cuerpo de la petición
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['userId']) || !isset($data['title'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

try {
    // Obtener la suscripción del usuario desde la base de datos
    $stmt = $conectar->prepare("SELECT push_subscription FROM usuarios WHERE id = ?");
    $stmt->execute([$data['userId']]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subscription || !$subscription['push_subscription']) {
        throw new Exception('Usuario no tiene suscripción push activa');
    }

    // Preparar los datos de la notificación
    $notification = [
        'title' => $data['title'],
        'body' => $data['options']['body'] ?? '',
        'icon' => $data['options']['icon'] ?? '/assets/img/logo.png',
        'badge' => $data['options']['badge'] ?? '/assets/img/logo.png',
        'data' => $data['options']['data'] ?? [],
        'actions' => $data['options']['actions'] ?? []
    ];

    // Enviar la notificación push usando el servicio web push
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: key=' . getenv('FCM_SERVER_KEY'),
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'to' => $subscription['push_subscription'],
        'notification' => $notification
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception('Error al enviar notificación push: ' . $response);
    }

    // Registrar la notificación en la base de datos
    $stmt = $conectar->prepare("
        INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, fecha_creacion)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $data['userId'],
        $data['title'],
        $data['options']['body'] ?? '',
        $data['options']['type'] ?? 'info'
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Notificación enviada correctamente'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
} 