<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar dependencias
require '../../vendor/autoload.php';
include "../dbconect.php";
session_start();

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

header('Content-Type: application/json');

// Verificar permisos (opcional: implementar según tus necesidades)
// if (!tienePermiso('enviar_notificaciones')) {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'No tiene permisos para enviar notificaciones']);
//     exit;
// }

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos
$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);

// Verificar datos requeridos
if (!$data || !isset($data['mensaje'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos. Se requiere al menos un mensaje']);
    exit;
}

// Extraer datos para la notificación
$mensaje = $data['mensaje'];
$tipo = isset($data['tipo']) ? $data['tipo'] : 'general';
$url = isset($data['url']) ? $data['url'] : '';
$sucursalID = isset($data['sucursalID']) ? intval($data['sucursalID']) : 0;
$usuarioID = isset($data['usuarioID']) ? intval($data['usuarioID']) : 0;

// Cargar claves VAPID
$configFile = '../config/vapid_keys.json';
if (!file_exists($configFile)) {
    echo json_encode([
        'success' => false,
        'message' => 'Las claves VAPID no existen. Por favor, ejecute primero generar_vapid_keys.php'
    ]);
    exit;
}

$vapidKeys = json_decode(file_get_contents($configFile), true);

// Configurar WebPush
$auth = [
    'VAPID' => [
        'subject' => $vapidKeys['subject'],
        'publicKey' => $vapidKeys['publicKey'],
        'privateKey' => $vapidKeys['privateKey'],
    ],
];

$webPush = new WebPush($auth);

// Construir consulta para obtener suscripciones relevantes
$query = "SELECT Datos_Suscripcion FROM Suscripciones_Push WHERE Activo = 1";
$params = [];
$types = "";

if ($usuarioID > 0) {
    $query .= " AND UsuarioID = ?";
    $params[] = $usuarioID;
    $types .= "i";
} else if ($sucursalID > 0) {
    $query .= " AND SucursalID = ?";
    $params[] = $sucursalID;
    $types .= "i";
}

$stmt = $con->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Preparar payload de notificación
$payload = json_encode([
    'tipo' => $tipo,
    'mensaje' => $mensaje,
    'url' => $url,
    'timestamp' => time()
]);

// Almacenar la notificación en la base de datos
$insertQuery = "INSERT INTO Notificaciones (Tipo, Mensaje, SucursalID) VALUES (?, ?, ?)";
$stmtInsert = $con->prepare($insertQuery);
$stmtInsert->bind_param("ssi", $tipo, $mensaje, $sucursalID);
$stmtInsert->execute();
$notificacionID = $stmtInsert->insert_id;

// Contadores
$total = 0;
$enviadas = 0;
$fallidas = 0;

// Enviar notificaciones a todas las suscripciones
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total++;
        $subscriptionData = json_decode($row['Datos_Suscripcion'], true);
        
        try {
            $subscription = Subscription::create($subscriptionData);
            $webPush->queueNotification($subscription, $payload);
        } catch (Exception $e) {
            $fallidas++;
            continue; // Continuar con la siguiente suscripción si esta falla
        }
    }
    
    // Enviar notificaciones en cola
    foreach ($webPush->flush() as $report) {
        $endpoint = $report->getRequest()->getUri()->__toString();
        
        if ($report->isSuccess()) {
            $enviadas++;
        } else {
            $fallidas++;
            
            // Marcar suscripciones expiradas/inválidas
            if ($report->getReason() === 'expired' || $report->getReason() === 'invalid') {
                $updateQuery = "UPDATE Suscripciones_Push SET Activo = 0 
                              WHERE JSON_EXTRACT(Datos_Suscripcion, '$.endpoint') = ?";
                $stmt = $con->prepare($updateQuery);
                $stmt->bind_param("s", $endpoint);
                $stmt->execute();
            }
        }
    }
}

// Devolver resultados
echo json_encode([
    'success' => true,
    'notificacionID' => $notificacionID,
    'estadisticas' => [
        'total' => $total,
        'enviadas' => $enviadas,
        'fallidas' => $fallidas
    ],
    'message' => "Notificación enviada a {$enviadas} dispositivos"
]);
?> 