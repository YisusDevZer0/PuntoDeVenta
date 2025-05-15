<?php
include "../database/db_connect.php";
session_start();

header('Content-Type: application/json');

// ID de sucursal del usuario actual
$sucursalID = isset($_SESSION["ID_Sucursal"]) ? $_SESSION["ID_Sucursal"] : 1;

// Obtener notificaciones no leídas para esta sucursal
$query = "SELECT ID_Notificacion, Tipo, Mensaje, Fecha, SucursalID, 
          TIMESTAMPDIFF(MINUTE, Fecha, NOW()) as MinutosTranscurridos 
          FROM Notificaciones 
          WHERE Leido = 0 
          AND (SucursalID = ? OR SucursalID = 0)
          ORDER BY Fecha DESC 
          LIMIT 20";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sucursalID);
$stmt->execute();
$result = $stmt->get_result();

$notificaciones = [];
while ($row = $result->fetch_assoc()) {
    // Formatear tiempo transcurrido
    $tiempo = "";
    $minutos = $row['MinutosTranscurridos'];
    
    if ($minutos < 60) {
        $tiempo = $minutos . " minutos";
    } else if ($minutos < 1440) {
        $tiempo = floor($minutos / 60) . " horas";
    } else {
        $tiempo = floor($minutos / 1440) . " días";
    }
    
    $row['TiempoTranscurrido'] = $tiempo;
    $notificaciones[] = $row;
}

// Obtener también el total para el contador
$queryTotal = "SELECT COUNT(*) as total FROM Notificaciones 
               WHERE Leido = 0 
               AND (SucursalID = ? OR SucursalID = 0)";
$stmtTotal = $conn->prepare($queryTotal);
$stmtTotal->bind_param("i", $sucursalID);
$stmtTotal->execute();
$total = $stmtTotal->get_result()->fetch_assoc()['total'];

echo json_encode([
    'notificaciones' => $notificaciones,
    'total' => $total
]);
?> 