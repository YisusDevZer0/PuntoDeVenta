

<?php
include "db_connect.php";

        $idProdPos=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ID_Prod_POSAct']))));
       $codBarraActualiza=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Cod_BarraActualiza']))));
        

        $sql = "UPDATE `Productos_POS` 
        SET `codBarraActualiza`='$codBarraActualiza'
        WHERE idProdPos=$idProdPos";
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
	mysqli_close($conn);

?>