<?php
include "db_connect.php";


        $Tip_Prod_ID=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Id_Serv']))));
     
  

        $sql = "DELETE FROM `TipProd_POS`  WHERE Tip_Prod_ID=$Tip_Prod_ID";
       
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
    mysqli_close($conn);
    ?>