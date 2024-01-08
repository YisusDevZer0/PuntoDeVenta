<?php
session_start();
include_once("../db_connect.php");

if(isset($_POST['login_button'])) {
	
	
	$Correo_Electronico = trim($_POST['user_email']);
	$Pass_Especialista = trim($_POST['password']);
	
	
	$sql ="SELECT Servicios_Especializados.Especialista_ID,Servicios_Especializados.Correo_Electronico,Servicios_Especializados.Pass_Especialista,
    Servicios_Especializados.Fk_Usuario,Servicios_Especializados.Estatus,Roles_Puestos.ID_rol,Roles_Puestos.Nombre_rol FROM Servicios_Especializados,Roles_Puestos WHERE
    Servicios_Especializados.Fk_Usuario = Roles_Puestos.ID_rol AND Correo_electronico='$Correo_Electronico'";

	$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
	$row = mysqli_fetch_assoc($resultset);	
		
	if($row['Pass_Especialista']==$Pass_Especialista and $row['Nombre_rol']=="T&eacute;cnico radi&oacute;logo" and $row['Estatus']=="Vigente" ){				
		echo "ok";
		$_SESSION['Radiografias'] = $row['Especialista_ID'];
	} 
	if($row['Pass_Especialista']==$Pass_Especialista and $row['Nombre_rol']=="Ultrasonograf&iacute;sta" and $row['Estatus']=="Vigente" ){				
		echo "ok";
		$_SESSION['Ultrasonidos'] = $row['Especialista_ID'];		
	} 	
	
}
?>
