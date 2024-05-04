<?php
include "db_connect.php";


        $Gasto_ID=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Id_Serv']))));
     
  

        $sql = "DELETE FROM `TiposDeGastos`  WHERE Gasto_ID=$Gasto_ID";
       
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
    mysqli_close($conn);
    ?>