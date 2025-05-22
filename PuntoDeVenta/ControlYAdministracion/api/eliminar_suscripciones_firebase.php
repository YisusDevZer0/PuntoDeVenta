<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Incluir conexión a la base de datos
include "../dbconect.php";

try {
    // Primero, obtener todas las suscripciones de Firebase
    $query = "SELECT * FROM Suscripciones_Push WHERE Activo = 1";
    $result = $con->query($query);
    
    $eliminadas = 0;
    $errores = [];
    
    while ($row = $result->fetch_assoc()) {
        $datos = json_decode($row['Datos_Suscripcion'], true);
        
        // Verificar si es una suscripción de Firebase
        if (isset($datos['endpoint']) && strpos($datos['endpoint'], 'fcm.googleapis.com') !== false) {
            // Eliminar la suscripción
            $delete_query = "DELETE FROM Suscripciones_Push WHERE id_suscripcion = ?";
            $stmt = $con->prepare($delete_query);
            $stmt->bind_param("i", $row['id_suscripcion']);
            
            if ($stmt->execute()) {
                $eliminadas++;
            } else {
                $errores[] = "Error al eliminar suscripción ID {$row['id_suscripcion']}: " . $stmt->error;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Se eliminaron $eliminadas suscripciones de Firebase",
        'eliminadas' => $eliminadas,
        'errores' => $errores
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 