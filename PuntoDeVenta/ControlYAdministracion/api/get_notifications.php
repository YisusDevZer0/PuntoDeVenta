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

// ID de sucursal del usuario actual
$sucursalID = isset($_SESSION["ID_Sucursal"]) ? $_SESSION["ID_Sucursal"] : 1;

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
    
    // Obtener notificaciones no leídas para esta sucursal
    $query = "SELECT n.ID_Notificacion as id,
                     n.Tipo as tipo,
                     n.Mensaje as mensaje,
                     n.Fecha as fecha,
                     n.SucursalID as sucursal_id,
                     n.Leido as leida,
                     COALESCE(s.Nombre_Sucursal, 'Todas las sucursales') AS Nombre_Sucursal,
                     TIMESTAMPDIFF(MINUTE, n.Fecha, NOW()) as minutos_transcurridos
              FROM Notificaciones n
              LEFT JOIN Sucursales s ON n.SucursalID = s.ID_Sucursal
              WHERE n.Leido = 0 
                AND (n.SucursalID = :sucursal_id OR n.SucursalID = 0)
              ORDER BY n.Fecha DESC
              LIMIT 20";

    $stmt = $db->prepare($query);
    $stmt->execute(['sucursal_id' => $sucursalID]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener contador de no leídas
    $queryTotal = "SELECT COUNT(*) as unread_count FROM Notificaciones 
                   WHERE Leido = 0 
                     AND (SucursalID = :sucursal_id OR SucursalID = 0)";
    $stmtTotal = $db->prepare($queryTotal);
    $stmtTotal->execute(['sucursal_id' => $sucursalID]);
    $unread = $stmtTotal->fetch(PDO::FETCH_ASSOC);
    
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