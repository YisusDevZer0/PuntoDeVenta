<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

// Consulta para obtener estadísticas del personal
$stats = array();

// Total de personal activo
$sql_total = "SELECT COUNT(*) as total FROM Usuarios_PV WHERE Estatus = 'Activo'";
$result_total = $conn->query($sql_total);
if ($result_total && $row = $result_total->fetch_assoc()) {
    $stats['totalPersonal'] = $row['total'];
}

// Total de administrativos
$sql_admin = "SELECT COUNT(*) as total FROM Usuarios_PV u 
              INNER JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User 
              WHERE u.Estatus = 'Activo' AND t.TipoUsuario = 'Administrativo'";
$result_admin = $conn->query($sql_admin);
if ($result_admin && $row = $result_admin->fetch_assoc()) {
    $stats['totalAdministrativos'] = $row['total'];
}

// Total de sucursales activas
$sql_sucursales = "SELECT COUNT(DISTINCT s.ID_Sucursal) as total 
                   FROM Sucursales s 
                   INNER JOIN Usuarios_PV u ON s.ID_Sucursal = u.Fk_Sucursal 
                   WHERE s.Sucursal_Activa = 'Si' AND u.Estatus = 'Activo'";
$result_sucursales = $conn->query($sql_sucursales);
if ($result_sucursales && $row = $result_sucursales->fetch_assoc()) {
    $stats['totalSucursales'] = $row['total'];
}

// Personal agregado este mes
$sql_reciente = "SELECT COUNT(*) as total FROM Usuarios_PV 
                 WHERE Estatus = 'Activo' 
                 AND MONTH(AgregadoEl) = MONTH(CURRENT_DATE()) 
                 AND YEAR(AgregadoEl) = YEAR(CURRENT_DATE())";
$result_reciente = $conn->query($sql_reciente);
if ($result_reciente && $row = $result_reciente->fetch_assoc()) {
    $stats['personalReciente'] = $row['total'];
}

echo json_encode($stats);
?> 