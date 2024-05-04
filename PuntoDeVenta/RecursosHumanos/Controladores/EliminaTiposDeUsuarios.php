<?php
include "db_connect.php";


        $ID_User=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Id_Serv']))));
     
  

        $sql = "DELETE FROM `Tipos_Usuarios`  WHERE ID_User=$ID_User";
       
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
    mysqli_close($conn);
    ?>