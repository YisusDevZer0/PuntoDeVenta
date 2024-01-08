<?php
session_start();
include_once("../db_connect.php");

if(isset($_POST['login_button'])) {
	
	
	$Correo_electronico = trim($_POST['user_email']);
	$Password = trim($_POST['password']);
	
	
	$sql ="SELECT Personal_Agenda.PersonalAgenda_ID,Personal_Agenda.Correo_Electronico,Personal_Agenda.Password,Personal_Agenda.Estatus,
	Personal_Agenda.Fk_Usuario,Roles_Puestos.ID_rol,Roles_Puestos.Nombre_rol from Personal_Agenda,Roles_Puestos
	where Personal_Agenda.Fk_Usuario = Roles_Puestos.ID_rol AND Correo_electronico='$Correo_electronico'";

	$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
	$row = mysqli_fetch_assoc($resultset);	
		
	if($row['Password']==$Password and $row['Nombre_rol']=="ADM agenda" and $row['Estatus']=="Vigente" ){				
		echo "ok";
		$_SESSION['AdminAgenda'] = $row['PersonalAgenda_ID'];
	} 
	if($row['Password']==$Password and $row['Nombre_rol']=="Call Center" and $row['Estatus']=="Vigente" ){				
		echo "ok";
		$_SESSION['AgendaCallCenter'] = $row['PersonalAgenda_ID'];		
	} 	
	if($row['Password']==$Password and $row['Nombre_rol']=="Ginec&oacute;logo" and $row['Estatus']=="Vigente" ){				
		echo "ok";
		$_SESSION['AgendaDePavel'] = $row['PersonalAgenda_ID'];		
	}
}
?>
