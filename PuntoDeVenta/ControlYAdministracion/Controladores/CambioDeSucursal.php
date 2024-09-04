<?php
include "db_connect.php";

        $Id_PvUser=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['user']))));
     
        $Fk_Sucursal=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Sucursal']))));
        
        

        $sql = "UPDATE `Usuarios_PV` 
        SET 
        `Fk_Sucursal`='$Fk_Sucursal' 
        WHERE Id_PvUser=$Id_PvUser";
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
	mysqli_close($conn);

?>