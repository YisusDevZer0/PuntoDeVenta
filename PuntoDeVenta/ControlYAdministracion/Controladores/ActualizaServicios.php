<?php
include "db_connection.php";

        $Servicio_ID=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Id_Serv']))));
       $Nom_Serv=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActNomServ']))));
        $Estado=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActVigEstServ']))));
      
        $ActualizadoPor=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActUsuarioCServ']))));
        $Sistema=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActSistemaCServ']))));
      

        $sql = "UPDATE `Servicios_POS` 
        SET `Nom_Serv`='$Nom_Serv',
        `Estado`='$Estado',
       
        `ActualizadoPor`='$ActualizadoPor', 
        `Sistema`='$Sistema' 
        WHERE Servicio_ID=$Servicio_ID";
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
	mysqli_close($conn);

?>