<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a base de datos
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

// Verificar existencia de claves VAPID
$configFile = '../config/vapid_keys.json';
if (!file_exists($configFile)) {
    echo json_encode([
        'success' => false,
        'message' => 'Las claves VAPID no existen. Por favor, ejecute primero generar_vapid_keys.php'
    ]);
    exit;
}

// Cargar claves VAPID
$vapidKeys = json_decode(file_get_contents($configFile), true);

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

// Preparar las suscripciones para un futuro envío externo
$suscripciones = [];

// Obtener suscripciones
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total++;
        $subscriptionData = json_decode($row['Datos_Suscripcion'], true);
        $suscripciones[] = $subscriptionData;
    }
}

// Devolver resultados
// En esta versión simplificada, no podemos enviar directamente las notificaciones push
// pero almacenamos los datos en la base de datos y tenemos las suscripciones listas
echo json_encode([
    'success' => true,
    'notificacionID' => $notificacionID,
    'estadisticas' => [
        'total' => $total,
        'enviadas' => 0, // No se pueden enviar sin la librería
        'fallidas' => 0
    ],
    'message' => "Notificación almacenada. Se requiere un servicio externo para enviar notificaciones push."
]);

// Nota: Para implementar completamente el envío de notificaciones push sin la librería WebPush,
// necesitarías implementar el protocolo de cifrado ECDH-ES con AES-GCM manualmente,
// lo cual está fuera del alcance de esta implementación básica.
?> 