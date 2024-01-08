<?php
session_start();
include_once("../db_connect.php");
if(isset($_POST['login_button'])) {
	$Correo_electronico = trim($_POST['user_email']);
	$Pass_Especialista = trim($_POST['password']);
	
	
	$sql = "SELECT Especialista_ID,Nombre_Apellidos,Pass_Especialista,Correo_Electronico FROM Servicios_Especializados WHERE Correo_electronico='$Correo_electronico'";
	$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
	$row = mysqli_fetch_assoc($resultset);	
		
	if($row['Pass_Especialista']==$Pass_Especialista){				
		echo "ok";
		$_SESSION['Especialista'] = $row['Especialista_ID'];
	} else {				
		echo "Usuario o contraseña incorrectos.."; // wrong details 
	}		
}
?>
