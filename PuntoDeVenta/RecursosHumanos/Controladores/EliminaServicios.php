<?php
include "db_connect.php";


        $Servicio_ID=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Id_Serv']))));
     
  

        $sql = "DELETE FROM `Servicios_POS`  WHERE Servicio_ID=$Servicio_ID";
       
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
    mysqli_close($conn);
    ?>