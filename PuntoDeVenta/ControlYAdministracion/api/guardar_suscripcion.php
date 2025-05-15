<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if (!$data || !isset($data['subscription'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos de suscripción no proporcionados']);
    exit;
}

// Extraer información del usuario
$userID = isset($data['user_id']) ? intval($data['user_id']) : 0;
$usuarioID = isset($_SESSION["ID_Usuario"]) ? $_SESSION["ID_Usuario"] : $userID;
$sucursalID = isset($_SESSION["ID_Sucursal"]) ? $_SESSION["ID_Sucursal"] : 1;

// Convertir suscripción a formato JSON para almacenamiento
$subscription = json_encode($data['subscription']);

// Verificar si la tabla de suscripciones existe
$checkTable = $con->query("SHOW TABLES LIKE 'Suscripciones_Push'");
if ($checkTable->num_rows === 0) {
    // Crear tabla si no existe
    $createTable = "CREATE TABLE IF NOT EXISTS Suscripciones_Push (
        ID_Suscripcion INT AUTO_INCREMENT PRIMARY KEY,
        UsuarioID INT NOT NULL,
        SucursalID INT NOT NULL,
        Dispositivo VARCHAR(255),
        Datos_Suscripcion TEXT NOT NULL,
        Fecha_Creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        Ultima_Actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        Activo TINYINT(1) DEFAULT 1
    )";
    
    if (!$con->query($createTable)) {
        echo json_encode(['success' => false, 'message' => 'Error al crear tabla de suscripciones: ' . $con->error]);
        exit;
    }
}

// Detectar información del dispositivo
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$dispositivo = "Desconocido";

if (strpos($userAgent, 'Android') !== false) {
    $dispositivo = "Android";
} elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
    $dispositivo = "iOS";
} elseif (strpos($userAgent, 'Windows') !== false) {
    $dispositivo = "Windows";
} elseif (strpos($userAgent, 'Macintosh') !== false) {
    $dispositivo = "Mac";
} elseif (strpos($userAgent, 'Linux') !== false) {
    $dispositivo = "Linux";
}

// Comprobar si ya existe esta suscripción para el usuario
$endpoint = json_decode($subscription, true)['endpoint'];
$checkQuery = "SELECT ID_Suscripcion FROM Suscripciones_Push 
              WHERE UsuarioID = ? 
              AND JSON_EXTRACT(Datos_Suscripcion, '$.endpoint') = ?";
$stmt = $con->prepare($checkQuery);
$stmt->bind_param("is", $usuarioID, $endpoint);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Actualizar suscripción existente
    $suscripcionID = $result->fetch_assoc()['ID_Suscripcion'];
    $updateQuery = "UPDATE Suscripciones_Push 
                   SET Datos_Suscripcion = ?, 
                       Dispositivo = ?,
                       Activo = 1
                   WHERE ID_Suscripcion = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param("ssi", $subscription, $dispositivo, $suscripcionID);
    $result = $stmt->execute();
    
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Suscripción actualizada correctamente' : 'Error al actualizar suscripción: ' . $con->error
    ]);
} else {
    // Insertar nueva suscripción
    $insertQuery = "INSERT INTO Suscripciones_Push 
                  (UsuarioID, SucursalID, Dispositivo, Datos_Suscripcion) 
                  VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($insertQuery);
    $stmt->bind_param("iiss", $usuarioID, $sucursalID, $dispositivo, $subscription);
    $result = $stmt->execute();
    
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Suscripción guardada correctamente' : 'Error al guardar suscripción: ' . $con->error
    ]);
}
?> 