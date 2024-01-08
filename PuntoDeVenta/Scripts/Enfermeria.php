<?php
session_start();
include_once("../db_connect.php");
if(isset($_POST['login_button'])) {
	$Correo_electronico = trim($_POST['user_email']);
	$Password = trim($_POST['password']);
	
	
	$sql = "SELECT Enfermero_ID,Nombre_Apellidos,Pass_Enfermero,Correo_Electronico,ID_H_O_D FROM Area_Enfermeria  WHERE Correo_electronico='$Correo_electronico'";
	$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
	$row = mysqli_fetch_assoc($resultset);	
		
	if($row['Pass_Enfermero']==$Password){				
		echo "ok";
		$_SESSION['Enfermeria'] = $row['Enfermero_ID'];
	} else {				
		echo "Usuario o contraseña incorrectos.."; // wrong details 
	}		
}
?>
