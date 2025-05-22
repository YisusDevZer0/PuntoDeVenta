<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Incluir conexión a la base de datos
include "../dbconect.php";

try {
    // 1. Verificar conexión a la base de datos
    if (!isset($con) || !$con) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // 2. Verificar si hay suscripciones activas
    $query = "SELECT COUNT(*) as total FROM Suscripciones_Push WHERE Activo = 1";
    $result = $con->query($query);
    $row = $result->fetch_assoc();
    
    if ($row['total'] == 0) {
        throw new Exception("No hay dispositivos suscritos");
    }

    // 3. Obtener una suscripción de prueba
    $query = "SELECT Datos_Suscripcion FROM Suscripciones_Push WHERE Activo = 1 LIMIT 1";
    $result = $con->query($query);
    $subscription = json_decode($result->fetch_assoc()['Datos_Suscripcion'], true);

    // 4. Verificar que la suscripción es válida
    if (!isset($subscription['endpoint'])) {
        throw new Exception("Suscripción inválida");
    }

    // 5. Intentar enviar una notificación simple
    $ch = curl_init($subscription['endpoint']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'title' => 'Prueba Simple',
        'body' => 'Esta es una notificación de prueba',
        'icon' => '/assets/img/logo.png'
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'TTL: 60'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // 6. Devolver resultado
    echo json_encode([
        'success' => true,
        'message' => 'Prueba completada',
        'details' => [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error,
            'endpoint' => $subscription['endpoint']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 