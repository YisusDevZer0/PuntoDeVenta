<?php
include "db_connect.php";

// Sanitizar y validar las entradas
$idProdPos = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['ID_Prod_POSAct']))));
$codBarraActualiza = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Cod_BarraActualiza']))));

// Consulta SQL con comillas simples para valores de cadena
$sql = "UPDATE `Productos_POS` 
        SET `codBarraActualiza` = '$codBarraActualiza'
        WHERE `idProdPos` = $idProdPos";

if (mysqli_query($conn, $sql)) {
    echo json_encode(array("statusCode" => 200));
} else {
    echo json_encode(array("statusCode" => 500, "error" => mysqli_error($conn)));
}

mysqli_close($conn);
?>
