<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Intentar incluir la conexión DB con manejo de errores
try {
    include "../dbconect.php";
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error en la conexión a la base de datos: ' . $e->getMessage()
    ]);
    exit;
}

header('Content-Type: application/json');

// Verificar que la conexión existe
if (!isset($con) || $con === null) {
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo establecer conexión con la base de datos'
    ]);
    exit;
}

// Verificar que se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No se proporcionó un ID de notificación'
    ]);
    exit;
}

$id = intval($_GET['id']);

try {
    // Actualizar el estado de la notificación
    $query = "UPDATE Notificaciones SET Leido = 1 WHERE ID_Notificacion = ?";
    $stmt = $con->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $con->error);
    }
    
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Notificación marcada como leída correctamente'
        ]);
    } else {
        throw new Exception("Error al actualizar la notificación: " . $stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 