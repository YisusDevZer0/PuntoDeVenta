<?php
// Endpoint público para el Checador
// Redirige la ejecución al controlador original evitando restricciones de acceso directo

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Salud básico por GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	header('Content-Type: application/json');
	echo json_encode([
		'success' => true,
		'message' => 'Checador API activo',
		'timestamp' => date('c')
	]);
	exit;
}

// Cambiar el directorio de trabajo al del controlador para que sus includes relativos funcionen
$controllerDir = __DIR__ . '/../ControlYAdministracion/Controladores';
if (!is_dir($controllerDir)) {
	header('Content-Type: application/json');
	echo json_encode(['success' => false, 'message' => 'Directorio del controlador no encontrado', 'path' => $controllerDir]);
	exit;
}

chdir($controllerDir);

// Incluir el controlador original (maneja POST internamente y emite JSON)
$controllerPath = $controllerDir . '/ChecadorController.php';
if (!file_exists($controllerPath)) {
	header('Content-Type: application/json');
	echo json_encode(['success' => false, 'message' => 'Archivo ChecadorController.php no encontrado', 'path' => $controllerPath]);
	exit;
}

// Propagar modo de prueba si se desea
if (!isset($_POST['test_mode'])) {
	$_POST['test_mode'] = '1';
}

include $controllerPath;

// Si el include no produjo salida (por ejemplo, método no-POST), responder algo útil
if (!headers_sent()) {
	header('Content-Type: application/json');
}
echo json_encode(['success' => false, 'message' => 'Método no soportado o sin acción']);
?>


