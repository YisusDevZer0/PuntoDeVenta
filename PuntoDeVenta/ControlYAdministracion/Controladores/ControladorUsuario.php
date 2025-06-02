<?php 
date_default_timezone_set("America/Monterrey");
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
	header("Location: Expiro.php");
}
include_once("db_connect.php");

// Determinar el ID de usuario según la sesión activa
$userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

$sql = "SELECT
Usuarios_PV.Id_PvUser,
Usuarios_PV.Nombre_Apellidos,
Usuarios_PV.file_name,
Usuarios_PV.Fk_Usuario,
Usuarios_PV.Fecha_Nacimiento,
Usuarios_PV.Correo_Electronico,
Usuarios_PV.Telefono,
Usuarios_PV.AgregadoPor,
Usuarios_PV.AgregadoEl,
Usuarios_PV.Estatus,
Usuarios_PV.Licencia,
Usuarios_PV.Fk_Sucursal,
Tipos_Usuarios.ID_User,
Tipos_Usuarios.TipoUsuario,
Sucursales.ID_Sucursal,
Sucursales.Nombre_Sucursal
FROM
Usuarios_PV
INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal WHERE
Usuarios_PV.Id_PvUser='".$userId."'";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);

// Guardar Fk_Sucursal en la sesión
if ($row && isset($row['Fk_Sucursal'])) {
    $_SESSION['Fk_Sucursal'] = $row['Fk_Sucursal'];
}

// if ($row['Nombre_Apellidos'] != 'DevZero') {
//   header("Location: https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Mantenimiento.php");
//   exit();
// }
