<?php
require_once 'Controladores/db_connect.php';
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
    
    // Obtener notificaciones no leídas
    $stmt = $db->prepare("
        SELECT 
            n.ID_Notificacion as id,
            n.Tipo as tipo,
            n.Mensaje as mensaje,
            n.Fecha_Creacion as fecha,
            n.ID_Sucursal as sucursal_id,
            n.Leida as leida,
            TIMESTAMPDIFF(MINUTE, n.Fecha_Creacion, NOW()) as minutos_transcurridos
        FROM notificaciones n
        WHERE n.ID_Usuario = :user_id
        ORDER BY n.Fecha_Creacion DESC
        LIMIT 10
    ");
    
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener contador de no leídas
    $stmt = $db->prepare("
        SELECT COUNT(*) as unread_count
        FROM notificaciones
        WHERE ID_Usuario = :user_id AND Leida = 0
    ");
    
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $unread = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Formatear tiempo transcurrido
    foreach ($notifications as &$notif) {
        $minutes = $notif['minutos_transcurridos'];
        
        if ($minutes < 60) {
            $notif['tiempo_transcurrido'] = $minutes . ' minuto' . ($minutes != 1 ? 's' : '');
        } elseif ($minutes < 1440) {
            $hours = floor($minutes / 60);
            $notif['tiempo_transcurrido'] = $hours . ' hora' . ($hours != 1 ? 's' : '');
        } else {
            $days = floor($minutes / 1440);
            $notif['tiempo_transcurrido'] = $days . ' día' . ($days != 1 ? 's' : '');
        }
        
        // Eliminar campos innecesarios
        unset($notif['minutos_transcurridos']);
    }
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unread['unread_count']
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener notificaciones: ' . $e->getMessage()
    ]);
} 