<?php
require_once '../Controladores/db_connect.php';
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

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Insertar notificación de prueba
    $stmt = $db->prepare("
        INSERT INTO notificaciones (ID_Usuario, Tipo, Mensaje, ID_Sucursal)
        VALUES (:user_id, 'sistema', 'Esta es una notificación de prueba', :sucursal_id)
    ");
    
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'sucursal_id' => $_SESSION['sucursal_id'] ?? null
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Notificación de prueba creada correctamente',
        'notification_id' => $db->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear notificación de prueba: ' . $e->getMessage()
    ]);
} 