<?php
include "db_connect.php";

        $Gasto_ID=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Id_Serv']))));
       $Nom_Gasto=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActNomServ']))));
        $Estado=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActVigenciaServ']))));
      
        $ActualizadoPor=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActUsuarioCServ']))));
        $Sistema=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActSistemaCServ']))));
      

        $sql = "UPDATE `TiposDeGastos` 
        SET `Nom_Gasto`='$Nom_Gasto',
        `Estado`='$Estado',
       
        `ActualizadoPor`='$ActualizadoPor', 
        `Sistema`='$Sistema' 
        WHERE Gasto_ID=$Gasto_ID";
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
	mysqli_close($conn);

?>