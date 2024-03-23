<?php
include_once 'db_connect.php';

$Servicio = mysqli_real_escape_string($conn, $_POST['NombreServicio']);
$Estado = mysqli_real_escape_string($conn, $_POST['estado']);
$Creado = mysqli_real_escape_string($conn, $_POST['agrego']);
$Sistema = mysqli_real_escape_string($conn, $_POST['Sistema']);
$Licencia = mysqli_real_escape_string($conn, $_POST['licencia']);

$sql = "SELECT Nom_Serv, Licencia, Creado FROM Servicios_POS WHERE Nom_Serv='$Servicio' AND Licencia='$Licencia'";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));

if(mysqli_num_rows($resultset) > 0) {
    echo json_encode(array("statusCode"=>250));
} else {
    $sql = "INSERT INTO `Servicios_POS`(`Nom_Serv`, `Estado`, `Agregado_Por`, `Sistema`, `Licencia`) 
    VALUES ('$Servicio','$Estado','$Creado','$Sistema','$Licencia')";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode"=>200));
    } else {
        echo json_encode(array("statusCode"=>201));
    }
}

mysqli_close($conn);
?>
