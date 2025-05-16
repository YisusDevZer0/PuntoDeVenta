<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a la base de datos
include "../dbconect.php";
session_start();

header('Content-Type: application/json');

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos
$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);

// Validar datos
if (!$data || !isset($data['mensaje'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit;
}

// Extraer parámetros
$mensaje = isset($data['mensaje']) ? $data['mensaje'] : 'Nueva notificación';
$tipo = isset($data['tipo']) ? $data['tipo'] : 'sistema';
$url = isset($data['url']) ? $data['url'] : '';
$usuarioID = isset($data['usuario_id']) ? intval($data['usuario_id']) : 0;
$sucursalID = isset($data['sucursal_id']) ? intval($data['sucursal_id']) : 0;

// Opciones para filtrar suscripciones
$where = "Activo = 1";
$params = [];
$types = '';

if ($usuarioID > 0) {
    $where .= " AND UsuarioID = ?";
    $params[] = $usuarioID;
    $types .= 'i';
}

if ($sucursalID > 0) {
    $where .= " AND SucursalID = ?";
    $params[] = $sucursalID;
    $types .= 'i';
}

// Consultar suscripciones
$query = "SELECT Datos_Suscripcion FROM Suscripciones_Push WHERE $where";
$stmt = $con->prepare($query);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta: ' . $con->error]);
    exit;
}

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

// Obtener suscripciones
$suscripciones = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total++;
        $suscripciones[] = json_decode($row['Datos_Suscripcion'], true);
    }
}

// Si no hay librería WebPush, intentamos una implementación simple
// Nota: Esto es una solución alternativa, lo ideal es instalar la librería WebPush
function enviarNotificacionWebPush($endpoint, $p256dh, $auth, $payload, $vapidKeys) {
    try {
        // Headers específicos requeridos por la API Web Push
        $headers = [
            'Content-Type' => 'application/json',
            'TTL' => '60', // Tiempo de vida en segundos
            'Urgency' => 'high'
        ];
        
        // Preparar la solicitud
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Ejecutar la solicitud
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $statusCode >= 200 && $statusCode < 300;
    } catch (Exception $e) {
        error_log("Error enviando notificación push: " . $e->getMessage());
        return false;
    }
}

// Cargar claves VAPID
$vapidKeysFile = '../config/vapid_keys.json';
if (file_exists($vapidKeysFile)) {
    $vapidKeys = json_decode(file_get_contents($vapidKeysFile), true);
    
    // Intentar enviar notificaciones
    foreach ($suscripciones as $subscription) {
        $endpoint = $subscription['endpoint'];
        
        // Datos de cifrado del cliente
        $p256dh = isset($subscription['keys']['p256dh']) ? $subscription['keys']['p256dh'] : '';
        $auth = isset($subscription['keys']['auth']) ? $subscription['keys']['auth'] : '';
        
        // Intentar enviar la notificación
        $success = enviarNotificacionWebPush($endpoint, $p256dh, $auth, $payload, $vapidKeys);
        
        if ($success) {
            $enviadas++;
        } else {
            $fallidas++;
        }
    }
} else {
    // No hay claves VAPID
    echo json_encode([
        'success' => false,
        'message' => 'Las claves VAPID no existen. Ejecute primero generar_vapid_keys.php',
        'notificacionID' => $notificacionID
    ]);
    exit;
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
    'message' => "Notificación almacenada y enviada a $enviadas dispositivos."
]);
?> 