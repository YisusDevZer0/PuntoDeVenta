
<?php
include "db_connect.php";
                $InfoState="Entregado";
        $ID_Traspaso_Generado=$conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['ID_Traspaso_Generado']))));
       $TraspasoRecibidoPor= $conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['NombreRecibio']))));
        $Estatus= $conn -> real_escape_string(htmlentities(strip_tags(Trim($InfoState))));
        $Fecha_recepcion= $conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['FechaRecepcion']))));
        $sql = "UPDATE `Traspasos_generados` 
        SET `Estatus`='$Estatus',
        `TraspasoRecibidoPor`='$TraspasoRecibidoPor',
        `Fecha_recepcion`='$Fecha_recepcion'
        WHERE ID_Traspaso_Generado=$ID_Traspaso_Generado";
       if (mysqli_query($conn, $sql)) {
		echo json_encode(array("statusCode"=>200));
	} 
	else {
		echo json_encode(array("statusCode"=>201));
	}
	mysqli_close($conn);

?>