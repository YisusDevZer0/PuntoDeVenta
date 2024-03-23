<?php
include "db_connect.php";

        $Marca_ID=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Id_Serv']))));
       $Nom_Marca=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActNomServ']))));
        
      
        $ActualizadoPor=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActUsuarioCServ']))));
        $Sistema=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ActSistemaCServ']))));
      

        $sql = "UPDATE `Marcas_POS` 
        SET `Nom_Marca`='$Nom_Marca',
        `ActualizadoPor`='$ActualizadoPor', 
        `Sistema`='$Sistema' 
        WHERE Marca_ID=$Marca_ID";
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
	mysqli_close($conn);

?>