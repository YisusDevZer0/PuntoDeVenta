<?php
session_start();
include_once("../db_connect.php");
if(isset($_POST['login_button'])) {
	$Correo_electronico = trim($_POST['user_email']);
	$Password = trim($_POST['password']);
	
	
	$sql = "SELECT Admin_ID,Nombre_Apellidos,Password,Correo_Electronico,ID_H_O_D FROM  Administracion_Sistema WHERE Correo_electronico='$Correo_electronico'";
	$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
	$row = mysqli_fetch_assoc($resultset);	
		
	if($row['Password']==$Password){				
		echo "ok";
		$_SESSION['AdminG'] = $row['Admin_ID'];
	} else {				
		echo "Usuario o contraseña incorrectos.."; // wrong details 
	}		
}
?>
