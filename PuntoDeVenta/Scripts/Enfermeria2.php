<?php
session_start();
include_once("../db_connect.php");
if(isset($_POST['login_button'])) {
	$Correo_electronico = trim($_POST['user_email']);
	$Password = trim($_POST['password']);
	
	
	$sql = "SELECT Personal_Enfermeria.Enfermero_ID,Personal_Enfermeria.Password,Personal_Enfermeria.Correo_Electronico,Personal_Enfermeria.Estatus,
	Personal_Enfermeria.Fk_Usuario,Personal_Enfermeria.ID_H_O_D, Roles_Puestos.ID_rol,Roles_Puestos.Nombre_rol FROM Personal_Enfermeria,Roles_Puestos 
	WHERE  Personal_Enfermeria.Fk_Usuario = Roles_Puestos.ID_rol AND Personal_Enfermeria.Correo_electronico='$Correo_electronico'";
	$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
	$row = mysqli_fetch_assoc($resultset);	
		
	if($row['Password']==$Password and $row['Nombre_rol']=="Enfermería" and $row['Estatus']=="Vigente" ){				
		echo "ok";
		$_SESSION['Enfermeria'] = $row['Enfermero_ID'];
	} 
	if($row['Password']==$Password and $row['Nombre_rol']=="Jefe(a) de enfermería" and $row['Estatus']=="Vigente"){				
		echo "ok";
		$_SESSION['AdminEnfermeria'] = $row['Enfermero_ID'];		
	} 			
}
?>
