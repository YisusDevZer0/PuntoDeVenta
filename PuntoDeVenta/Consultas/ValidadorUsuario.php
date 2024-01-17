<?php
session_start();
include_once("db_connect.php");

if(isset($_POST['login_button'])) {
	
	
	$Correo_electronico = trim($_POST['user_email']);
	$Password = trim($_POST['password']);
	
	
	$sql = "SELECT Usuarios_PV.Id_PvUser, Usuarios_PV.Correo_Electronico, Usuarios_PV.Password, Usuarios_PV.Estatus,
	Usuarios_PV.Fk_Usuario, Tipos_Usuarios.ID_User, Tipos_Usuarios.TipoUsuario 
FROM Usuarios_PV 
INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User 
WHERE Usuarios_PV.Correo_Electronico = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $Correo_electronico);
mysqli_stmt_execute($stmt);

$resultset = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($resultset);

switch(true) {
    case $row['Password'] == $Password && $row['TipoUsuario'] == "Administrador" && $row['Estatus'] == "Activo":
        echo "ok";
        $_SESSION['AdminPOS'] = $row['Id_PvUser'];
        break;
    case $row['Password'] == $Password && $row['TipoUsuario'] == "Ventas" && $row['Estatus'] == "Activo":
        echo "ok";
        $_SESSION['VentasPos'] = $row['Id_PvUser'];
        break;
    case $row['Password'] == $Password && $row['TipoUsuario'] == "ADM Punto de venta" && $row['Estatus'] == "Activo":
        echo "ok";
        $_SESSION['SuperAdmin'] = $row['Id_PvUser'];
        break;
    // Agrega los demás casos según la lógica que necesites
    default:
        // Manejar otros casos o mostrar un mensaje de error
        echo "Error: Usuario no autorizado";
}
}


