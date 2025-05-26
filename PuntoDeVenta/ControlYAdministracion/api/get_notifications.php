<?php
// Conexión directa a la base de datos
$host = 'localhost';
$user = 'u858848268_devpezer0';
$pass = 'F9+nIIOuCh8yI6wu4!08';
$db = 'u858848268_doctorpez';

session_start();

header('Content-Type: application/json');

// Determinar el ID de usuario según la sesión activa
$userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] :
         (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] :
         (isset($_SESSION['Marketing']) ? $_SESSION['Marketing'] : null));

if (!$userId) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

try {
    $db = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener Notificacioneso leídas
    $stmt = $db->prepare("
        SELECT 
            n.ID_Notificacion as id,
            n.Tipo as tipo,
            n.Mensaje as mensaje,
            n.Fecha_Creacion as fecha,
            n.ID_Sucursal as sucursal_id,
            n.Leida as leida,
            TIMESTAMPDIFF(MINUTE, n.Fecha_Creacion, NOW()) as minutos_transcurridos
        FROM Notificaciones n
        WHERE n.ID_Usuario = :user_id
        ORDER BY n.Fecha_Creacion DESC
        LIMIT 10
    ");
    
    $stmt->execute(['user_id' => $userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener contador de no leídas
    $stmt = $db->prepare("
        SELECT COUNT(*) as unread_count
        FROM notificaciones
        WHERE ID_Usuario = :user_id AND Leida = 0
    ");
    
    $stmt->execute(['user_id' => $userId]);
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