<?php
include_once 'db_connect.php';

$Nombre_Categoria = mysqli_real_escape_string($conn, $_POST['NomMarca']);
$Agregado_Por = mysqli_real_escape_string($conn, $_POST['agregoPor']);
$Sistema = mysqli_real_escape_string($conn, $_POST['sistema']);
$Licencia = mysqli_real_escape_string($conn, $_POST['licencia']);
$Estado = mysqli_real_escape_string($conn, $_POST['estado']);

// Consulta para verificar si ya existe un registro con los mismos valores
$sql = "SELECT Nom_Cat, Licencia FROM Categorias_POS WHERE Nom_Cat='$Nombre_Categoria' AND Licencia='$Licencia'";
$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));

if (mysqli_num_rows($resultset) > 0) {
    echo json_encode(array("statusCode" => 250)); // El registro ya existe
} else {
    // Consulta de inserción para agregar un nuevo registro
    $sql = "INSERT INTO `Categorias_POS`(`Nom_Cat`, `Agregado_Por`, `Sistema`, `Licencia`, `Estado`) 
            VALUES ('$Nombre_Categoria', '$Agregado_Por', '$Sistema', '$Licencia', '$Estado')";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode" => 200)); // Inserción exitosa
    } else {
        echo json_encode(array("statusCode" => 201)); // Error en la inserción
    }
}

mysqli_close($conn);
?>