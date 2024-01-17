<?php
session_start();
include_once("../db_connect.php");

if(isset($_POST['login_button'])) {
	
	
	$Correo_electronico = trim($_POST['user_email']);
	$Password = trim($_POST['password']);
	
	
	$sql = "SELECT PersonalPOS.Pos_ID, PersonalPOS.Correo_Electronico, PersonalPOS.Password, PersonalPOS.Estatus,
        PersonalPOS.Fk_Usuario, Roles_Puestos.ID_rol, Roles_Puestos.Nombre_rol 
        FROM PersonalPOS, Roles_Puestos
        WHERE PersonalPOS.Fk_Usuario = Roles_Puestos.ID_rol AND Correo_electronico = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $Correo_electronico);
mysqli_stmt_execute($stmt);

$resultset = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($resultset);

	switch($row){
	case $row['Password']==$Password and $row['Nombre_rol']=="Administrador" and $row['Estatus']=="Vigente" ;				
		echo "ok";
		$_SESSION['AdminPOS'] = $row['Pos_ID'];
	break;
	case $row['Password']==$Password and $row['Nombre_rol']=="Ventas" and $row['Estatus']=="Vigente"; 			
		echo "ok";
		$_SESSION['VentasPos'] = $row['Pos_ID'];	
		break;	
		case $row['Password']==$Password and $row['Nombre_rol']=="ADM Punto de venta" and $row['Estatus']=="Vigente";		
			echo "ok";
			$_SESSION['SuperAdmin'] = $row['Pos_ID'];		
			break;
		case $row['Password']==$Password and $row['Nombre_rol']=="Logística y compras" and $row['Estatus']=="Vigente";			
			echo "ok";
			$_SESSION['LogisticaPOS'] = $row['Pos_ID'];		
		break;
		case $row['Password']==$Password and $row['Nombre_rol']=="Administrador CEDIS" and $row['Estatus']=="Vigente";				
			echo "ok";
			$_SESSION['ResponsableCedis'] = $row['Pos_ID'];		
		break;
		case $row['Password']==$Password and $row['Nombre_rol']=="Encargado de inventarios" and $row['Estatus']=="Vigente";				
			echo "ok";
			$_SESSION['ResponsableInventarios'] = $row['Pos_ID'];	
			break;	
			case $row['Password']==$Password and $row['Nombre_rol']=="Responsable de farmacias" and $row['Estatus']=="Vigente";				
			echo "ok";
			$_SESSION['ResponsableDeFarmacias'] = $row['Pos_ID'];	
			break;	
			case $row['Password']==$Password and $row['Nombre_rol']=="Jefe de odontología" and $row['Estatus']=="Vigente";				
			echo "ok";
			$_SESSION['CoordinadorDental'] = $row['Pos_ID'];	
			break;	
			case $row['Password']==$Password and $row['Nombre_rol']=="Supervisor" and $row['Estatus']=="Vigente";				
			echo "ok";
			$_SESSION['Supervisor'] = $row['Pos_ID'];	 
			break;	
			case $row['Password']==$Password and $row['Nombre_rol']=="Jefatura de enfermeria" and $row['Estatus']=="Vigente";				
			echo "ok";
			$_SESSION['JefeEnfermeros'] = $row['Pos_ID'];	 
			break;	
			
		} 	
	}

	?>
