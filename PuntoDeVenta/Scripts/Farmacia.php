<?php
session_start();
include_once("../db_connect.php");
if(isset($_POST['login_button'])) {
	$Correo_electronico = trim($_POST['user_email']);
	$Pass_Farmacia = trim($_POST['password']);
	
	
	$sql = "SELECT Encargado_ID,Nombre_Apellidos,Pass_Farmacia,Correo_Electronico,file_name,ID_Sucursal FROM Empleados_Farmacia WHERE Correo_electronico='$Correo_electronico'";
	$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
	$row = mysqli_fetch_assoc($resultset);	
		
	if($row['Pass_Farmacia']==$Pass_Farmacia){				
		echo "ok";
		$_SESSION['Sesion_Farmacia'] = $row['Encargado_ID'];
	} else {				
		echo "Usuario o contraseña incorrectos.."; // wrong details 
	}		
}

?>
