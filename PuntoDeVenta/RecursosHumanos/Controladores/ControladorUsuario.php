<?php 
date_default_timezone_set("America/Monterrey");
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if(!isset($_SESSION['AdministradorRH'])){
	header("Location: Expiro.php");
}
include_once("db_connect.php");
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
Tipos_Usuarios.ID_User,
Tipos_Usuarios.TipoUsuario,
Sucursales.ID_Sucursal,
Sucursales.Nombre_Sucursal
FROM
Usuarios_PV
INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal WHERE
Usuarios_PV.Id_PvUser='".$_SESSION['AdministradorRH']."'";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);
