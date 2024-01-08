<?php
session_start();
include_once("../db_connect.php");

if(isset($_POST['login_button'])) {
	if (isset($_POST['action']) && ($_POST['action'] == 'process')) {

		$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify'; 
		$recaptcha_secret = '6LePQs8ZAAAAADkcNib-c93N_rfu1DVLqAaxmSsK'; 
		$recaptcha_response = $_POST['recaptcha_response']; 
		$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response); 
		$recaptcha = json_decode($recaptcha); 
		
		if($recaptcha->score >= 0.7){
		
		  // código para procesar los campos y enviar el form
		
		} else {
		
		  // código para lanzar aviso de error en el envío
		
		}
		
		}
	
	$Correo_electronico = trim($_POST['user_email']);
	$Password = trim($_POST['password']);
	
	
	$sql ="SELECT PersonalPOS.Pos_ID,PersonalPOS.Correo_Electronico,PersonalPOS.Password,
	PersonalPOS.Fk_Usuario,Roles_Puestos.ID_rol,Roles_Puestos.Nombre_rol from PersonalPOS,Roles_Puestos
	where PersonalPOS.Fk_Usuario = Roles_Puestos.ID_rol AND Correo_electronico='$Correo_electronico'";

	$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
	$row = mysqli_fetch_assoc($resultset);	
		
	if($row['Password']==$Password and $row['Nombre_rol']=="Administrador" ){				
		echo "ok";
		$_SESSION['AdminPOS'] = $row['Pos_ID'];
	} 
	if($row['Password']==$Password and $row['Nombre_rol']=="Ventas" ){				
		echo "ok";
		$_SESSION['VentasPos'] = $row['Pos_ID'];		
	} 	
	
}
?>
