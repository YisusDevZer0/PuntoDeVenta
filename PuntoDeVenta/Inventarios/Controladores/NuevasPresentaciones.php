<?php
include_once 'db_connect.php';

$NomPresentacion = mysqli_real_escape_string($conn, $_POST['NomPresentacion']);
$AgregadoPor = mysqli_real_escape_string($conn, $_POST['agregoPor']);
$AgregadoEl = mysqli_real_escape_string($conn, $_POST['agregoEl']);
$Sistema = mysqli_real_escape_string($conn, $_POST['sistema']);
$Licencia = mysqli_real_escape_string($conn, $_POST['licencia']);

// Consulta para verificar si ya existe un registro con los mismos valores
$sql = "SELECT Nom_Presentacion, Licencia FROM Presentaciones WHERE Nom_Presentacion='$NomPresentacion' AND Licencia='$Licencia'";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);

if(mysqli_num_rows($resultset) > 0) {
    echo json_encode(array("statusCode"=>250)); // El registro ya existe
} else {
    // Consulta de inserción para agregar un nuevo registro
    $sql = "INSERT INTO `Presentaciones`(`Nom_Presentacion`, `Agregado_Por`,  `Sistema`, `Licencia`) 
            VALUES ('$NomPresentacion', '$AgregadoPor', '$Sistema', '$Licencia')";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode"=>200)); // Inserción exitosa
    } else {
        echo json_encode(array("statusCode"=>201)); // Error en la inserción
    }
    mysqli_close($conn);
}
?>
