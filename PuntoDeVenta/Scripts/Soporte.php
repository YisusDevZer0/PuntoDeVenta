<?php
include_once '../db_connecte.php';

 

    
$Sistema =  $conn -> real_escape_string(htmlentities(strip_tags(trim($_POST['Sistema']))));
$Nombres =  $conn -> real_escape_string(htmlentities(strip_tags(trim($_POST['nombres']))));
$Correo = $conn -> real_escape_string(htmlentities(strip_tags(trim($_POST['correo']))));
$Tel = $conn -> real_escape_string(htmlentities(strip_tags(trim($_POST['telefono']))));
$Empresa = $conn -> real_escape_string(htmlentities(strip_tags(trim($_POST['empresa']))));
$Reporte_Solicitud= $conn -> real_escape_string(htmlentities(strip_tags(trim($_POST['descripcion']))));

    
    //include database configuration file
    
    //insert form data in the database
    $sql = "INSERT INTO `Solicitudes_Reportes`( `Sistema`, `Nombres`,`Correo`,`Tel`,`Empresa`,`Reporte_Solicitud`) 
	VALUES ('$Sistema','$Nombres','$Correo','$Tel','$Empresa','$Reporte_Solicitud')";

    if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
	mysqli_close($conn);
?>
