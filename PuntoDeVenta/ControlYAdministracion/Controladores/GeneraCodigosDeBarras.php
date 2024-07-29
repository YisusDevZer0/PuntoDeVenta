<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db_connect.php";

$idProdPos = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['ID_Prod_POSAct']))));
$codBarraActualiza = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Cod_BarraActualiza']))));

$sql = "UPDATE `Productos_POS` 
        SET `Cod_Barra` = '$codBarraActualiza'
        WHERE `ID_Prod_POS` = $idProdPos";

if (mysqli_query($conn, $sql)) {
    echo json_encode(array("statusCode" => 200));
} else {
    echo json_encode(array("statusCode" => 500, "error" => mysqli_error($conn)));
}

mysqli_close($conn);
?>
