<?php
require_once '../config/dbconect.php';
session_start();

header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;

if (!$notification_id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de notificación no proporcionado'
    ]);
    exit;
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar que la notificación pertenece al usuario
    $stmt = $db->prepare("
        SELECT ID_Notificacion 
        FROM notificaciones 
        WHERE ID_Notificacion = :notification_id 
        AND ID_Usuario = :user_id
    ");
    
    $stmt->execute([
        'notification_id' => $notification_id,
        'user_id' => $_SESSION['user_id']
    ]);
    
    if (!$stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Notificación no encontrada o no autorizada'
        ]);
        exit;
    }
    
    // Marcar como leída
    $stmt = $db->prepare("
        UPDATE notificaciones 
        SET Leida = 1 
        WHERE ID_Notificacion = :notification_id
    ");
    
    $stmt->execute(['notification_id' => $notification_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Notificación marcada como leída'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al marcar notificación como leída: ' . $e->getMessage()
    ]);
} 