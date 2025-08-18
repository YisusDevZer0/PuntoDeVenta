<?php
// Endpoint mínimo para registrar asistencia directamente
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Cargar conexión a BD
$dbConnect = __DIR__ . '/../ControlYAdministracion/Controladores/db_connect.php';
if (!file_exists($dbConnect)) {
    echo json_encode(['success' => false, 'message' => 'db_connect.php no encontrado']);
    exit;
}
include_once $dbConnect;

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'Sin conexión a la base de datos']);
    exit;
}

// Permitir GET o POST
$action = $_REQUEST['action'] ?? '';
if ($action !== 'registrar_asistencia') {
    echo json_encode(['success' => true, 'message' => 'checador_simple activo']);
    exit;
}

$usuario_id = intval($_REQUEST['usuario_id'] ?? 0);
$tipo = $_REQUEST['tipo'] ?? '';
$latitud = isset($_REQUEST['latitud']) ? floatval($_REQUEST['latitud']) : null;
$longitud = isset($_REQUEST['longitud']) ? floatval($_REQUEST['longitud']) : null;
$timestamp = $_REQUEST['timestamp'] ?? date('Y-m-d H:i:s');

if (!$usuario_id || !$tipo || $latitud === null || $longitud === null) {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
    exit;
}

// Inserción directa con verificación simple misma-fecha+tipo
$fecha_hoy = date('Y-m-d', strtotime($timestamp));

$check = $conn->prepare("SELECT id FROM asistencias WHERE usuario_id = ? AND tipo = ? AND DATE(fecha_hora) = ?");
$check->bind_param('iss', $usuario_id, $tipo, $fecha_hoy);
$check->execute();
$exists = $check->get_result();
if ($exists && $exists->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => "Ya existe un registro de $tipo para hoy"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO asistencias (usuario_id, tipo, latitud, longitud, fecha_hora, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param('isdds', $usuario_id, $tipo, $latitud, $longitud, $timestamp);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => "$tipo registrada exitosamente", 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $stmt->error]);
}
?>


