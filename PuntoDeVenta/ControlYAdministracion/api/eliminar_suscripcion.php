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
    
    if (!isset($data['user_id'])) {
        throw new Exception("ID de usuario no proporcionado");
    }

    // Eliminar suscripción
    $query = "DELETE FROM Suscripciones_Push WHERE User_ID = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $data['user_id']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Suscripción eliminada correctamente'
        ]);
    } else {
        throw new Exception("Error al eliminar la suscripción: " . $stmt->error);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 