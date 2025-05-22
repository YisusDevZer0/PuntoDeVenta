<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Incluir conexión a la base de datos
include "../dbconect.php";

try {
    // Obtener todas las suscripciones
    $query = "SELECT * FROM Suscripciones_Push";
    $result = $con->query($query);
    
    $suscripciones = [];
    while ($row = $result->fetch_assoc()) {
        $datos = json_decode($row['Datos_Suscripcion'], true);
        $suscripciones[] = [
            'id' => $row['ID'],
            'user_id' => $row['User_ID'],
            'activo' => $row['Activo'],
            'fecha_creacion' => $row['Fecha_Creacion'],
            'endpoint' => $datos['endpoint'] ?? 'No disponible',
            'tipo' => isset($datos['endpoint']) && strpos($datos['endpoint'], 'fcm.googleapis.com') !== false ? 'Firebase' : 'Web Push'
        ];
    }

    echo json_encode([
        'success' => true,
        'total_suscripciones' => count($suscripciones),
        'suscripciones' => $suscripciones
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 