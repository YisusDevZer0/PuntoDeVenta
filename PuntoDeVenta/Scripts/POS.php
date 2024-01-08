<?php
session_start();
include_once("../db_connect.php");
if(isset($_POST['login_button'])) {
	$Correo_electronico = trim($_POST['user_email']);
	$Password = trim($_POST['password']);
	$Fk_Usuario= $_POST['nivel'];
	
	$sql = "SELECT Pos_ID,Nombre_Apellidos,Password,Correo_Electronico,ID_H_O_D,Fk_Usuario 
	FROM PersonalPOS WHERE Fk_Usuario='$Fk_Usuario' AND Correo_electronico='$Correo_electronico'";
	$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
	$row = mysqli_fetch_assoc($resultset);	
		
	if($row['Password']==$Password and $row['Fk_Usuario']=="6" ){				
		echo "ok";
		$_SESSION['AdminPOS'] = $row['Pos_ID'];
	} 
	if($row['Password']==$Password and $row['Fk_Usuario']=="7" ){				
		echo "ok";
		$_SESSION['VentasPos'] = $row['Pos_ID'];		
	} 	
	
}
?>
