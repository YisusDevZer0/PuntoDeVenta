<?php
include "../dbconect.php";
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

// ID de sucursal del usuario actual
$sucursalID = isset($_SESSION["ID_Sucursal"]) ? $_SESSION["ID_Sucursal"] : 1;

// Obtener solo las notificaciones no leídas para esta sucursal
$query = "SELECT 
            n.ID_Notificacion, 
            n.Tipo, 
            n.Mensaje, 
            n.Fecha_Creacion as Fecha, 
            n.ID_Sucursal as SucursalID, 
            n.Leida as Leido,
            s.Nombre_Sucursal as NombreSucursal,
            TIMESTAMPDIFF(MINUTE, n.Fecha_Creacion, NOW()) as minutos_transcurridos
          FROM notificaciones n
          LEFT JOIN sucursales s ON n.ID_Sucursal = s.ID_Sucursal
          WHERE (n.ID_Sucursal = ? OR n.ID_Sucursal IS NULL)
          AND n.Leida = 0
          AND n.ID_Usuario = ?
          ORDER BY n.Fecha_Creacion DESC 
          LIMIT 100";

$stmt = $con->prepare($query);
$stmt->bind_param("ii", $sucursalID, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$notificaciones = [];
while ($row = $result->fetch_assoc()) {
    // Formatear fecha para mejor visualización
    $fechaObj = new DateTime($row['Fecha']);
    $row['FechaFormateada'] = $fechaObj->format('d/m/Y H:i');
    
    // Formatear tiempo transcurrido
    $minutes = $row['minutos_transcurridos'];
    if ($minutes < 60) {
        $row['TiempoTranscurrido'] = $minutes . ' minuto' . ($minutes != 1 ? 's' : '');
    } elseif ($minutes < 1440) {
        $hours = floor($minutes / 60);
        $row['TiempoTranscurrido'] = $hours . ' hora' . ($hours != 1 ? 's' : '');
    } else {
        $days = floor($minutes / 1440);
        $row['TiempoTranscurrido'] = $days . ' día' . ($days != 1 ? 's' : '');
    }
    
    // Eliminar campo innecesario
    unset($row['minutos_transcurridos']);
    
    $notificaciones[] = $row;
}

echo json_encode([
    'success' => true,
    'notifications' => $notificaciones,
    'total' => count($notificaciones)
]);
?> 