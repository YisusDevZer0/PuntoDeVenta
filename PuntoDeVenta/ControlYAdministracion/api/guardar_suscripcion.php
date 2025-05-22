<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Incluir conexión a la base de datos
include "../dbconect.php";

try {
    // Obtener datos de la petición
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['subscription']) || !isset($data['user_id'])) {
        throw new Exception("Datos de suscripción o ID de usuario no proporcionados");
    }

    // Detectar dispositivo
    $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
    
    // Preparar la consulta
    $query = "INSERT INTO Suscripciones_Push (UsuarioID, SucursalID, Dispositivo, Datos_Suscripcion, Activo, Fecha_Creacion) 
              VALUES (?, 1, ?, ?, 1, NOW())";
    
    $stmt = $con->prepare($query);
    $datos_json = json_encode($data['subscription']);
    
    $stmt->bind_param("iss", $data['user_id'], $dispositivo, $datos_json);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Suscripción guardada correctamente',
            'id_suscripcion' => $stmt->insert_id
        ]);
    } else {
        throw new Exception("Error al guardar la suscripción: " . $stmt->error);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 