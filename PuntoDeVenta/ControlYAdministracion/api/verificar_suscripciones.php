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
            'id_suscripcion' => $row['ID_Suscripcion'],
            'usuario_id' => $row['UsuarioID'],
            'sucursal_id' => $row['SucursalID'],
            'dispositivo' => $row['Dispositivo'],
            'activo' => $row['Activo'],
            'fecha_creacion' => $row['Fecha_Creacion'],
            'endpoint' => $datos['endpoint'] ?? 'No disponible',
            'tipo' => isset($datos['endpoint']) && strpos($datos['endpoint'], 'fcm.googleapis.com') !== false ? 'Firebase' : 'Web Push',
            'datos_completos' => $datos
        ];
    }

    // Obtener el total de suscripciones activas
    $query_activas = "SELECT COUNT(*) as total FROM Suscripciones_Push WHERE Activo = 1";
    $result_activas = $con->query($query_activas);
    $total_activas = $result_activas->fetch_assoc()['total'];

    echo json_encode([
        'success' => true,
        'total_suscripciones' => count($suscripciones),
        'total_activas' => $total_activas,
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