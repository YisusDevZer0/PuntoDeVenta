<?php
// Endpoint público para el Checador
// Redirige/ejecuta el controlador original evitando restricciones de acceso directo

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$respondJson = function(array $payload) {
	header('Content-Type: application/json');
	echo json_encode($payload);
	exit;
};

// Cambiar el directorio de trabajo al del controlador para que sus includes relativos funcionen
$controllerDir = __DIR__ . '/../ControlYAdministracion/Controladores';
if (!is_dir($controllerDir)) {
	$respondJson(['success' => false, 'message' => 'Directorio del controlador no encontrado', 'path' => $controllerDir]);
}

chdir($controllerDir);

$controllerPath = $controllerDir . '/ChecadorController.php';
if (!file_exists($controllerPath)) {
	$respondJson(['success' => false, 'message' => 'Archivo ChecadorController.php no encontrado', 'path' => $controllerPath]);
}

// Modo salud por GET simple
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET['action'])) {
	$respondJson([
		'success' => true,
		'message' => 'Checador API activo',
		'timestamp' => date('c')
	]);
}

// Si llega GET con action, procesar como acción (workaround si el hosting bloquea POST)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['action'])) {
	// Incluir controlador y ejecutar la acción manualmente
	include_once $controllerPath;

	// $conn debe quedar disponible por los includes dentro del controlador
	if (!isset($conn) || !$conn) {
		$respondJson(['success' => false, 'message' => 'Conexión a BD no disponible en controlador']);
	}

	$controller = new ChecadorController($conn);
	$action = $_GET['action'];
	$userId = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : 1;

	switch ($action) {
		case 'registrar_asistencia':
			$tipo = $_GET['tipo'] ?? '';
			$latitud = isset($_GET['latitud']) ? floatval($_GET['latitud']) : null;
			$longitud = isset($_GET['longitud']) ? floatval($_GET['longitud']) : null;
			$timestamp = $_GET['timestamp'] ?? date('Y-m-d H:i:s');
			
			if (empty($tipo) || $latitud === null || $longitud === null) {
				$respondJson(['success' => false, 'message' => 'Parámetros incompletos']);
			}
			$resp = $controller->registrarAsistencia($userId, $tipo, $latitud, $longitud, $timestamp);
			$respondJson($resp);
			break;

		case 'obtener_ubicaciones':
			$resp = $controller->obtenerUbicacionesUsuario($userId);
			$respondJson($resp);
			break;

		default:
			$respondJson(['success' => false, 'message' => 'Acción no válida']);
	}
}

// Para POST: incluir el controlador que maneja POST internamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Propagar modo prueba por defecto
	if (!isset($_POST['test_mode'])) {
		$_POST['test_mode'] = '1';
	}
	include $controllerPath; // este archivo emitirá JSON y hará exit
	// Si por alguna razón no hubo salida
	$respondJson(['success' => false, 'message' => 'Sin respuesta del controlador']);
}

// Método no soportado
$respondJson(['success' => false, 'message' => 'Método no soportado']);
?>


