<?php
include "../database/db_connect.php";
session_start();

header('Content-Type: application/json');

// ID de sucursal del usuario actual
$sucursalID = isset($_SESSION["ID_Sucursal"]) ? $_SESSION["ID_Sucursal"] : 1;

// Obtener todas las notificaciones para esta sucursal
$query = "SELECT n.ID_Notificacion, n.Tipo, n.Mensaje, n.Fecha, n.SucursalID, n.Leido,
          s.Nombre_Sucursal as NombreSucursal
          FROM Notificaciones n
          LEFT JOIN Sucursales s ON n.SucursalID = s.ID_Sucursal
          WHERE n.SucursalID = ? OR n.SucursalID = 0
          ORDER BY n.Fecha DESC 
          LIMIT 100";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sucursalID);
$stmt->execute();
$result = $stmt->get_result();

$notificaciones = [];
while ($row = $result->fetch_assoc()) {
    // Formatear fecha para mejor visualización
    $fechaObj = new DateTime($row['Fecha']);
    $row['FechaFormateada'] = $fechaObj->format('d/m/Y H:i');
    $notificaciones[] = $row;
}

echo json_encode($notificaciones);
?> 