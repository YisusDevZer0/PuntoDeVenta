<?php
include_once 'db_connect.php';

$ConceptoCategoria = mysqli_real_escape_string($conn, $_POST['Concepto_Categoria']);
$ImporteTotal = mysqli_real_escape_string($conn, $_POST['Importe_Total']);
$Empleado = mysqli_real_escape_string($conn, $_POST['Empleado']);
$FkSucursal = mysqli_real_escape_string($conn, $_POST['Fk_sucursal']);
$FkCaja = mysqli_real_escape_string($conn, $_POST['Fk_Caja']);
$Recibe = mysqli_real_escape_string($conn, $_POST['Recibe']);
$Sistema = mysqli_real_escape_string($conn, $_POST['Sistema']);
$AgregadoPor = mysqli_real_escape_string($conn, $_POST['Empleado']);
$Licencia = mysqli_real_escape_string($conn, $_POST['Licencia']);

// Consulta para verificar si ya existe un registro con los mismos valores
$sql = "SELECT Nom_Gasto, Licencia FROM GastosPOS WHERE Nom_Gasto='$NomGasto' AND Licencia='$Licencia'";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);

if(mysqli_num_rows($resultset) > 0) {
    echo json_encode(array("statusCode"=>250)); // El registro ya existe
} else {
    // Consulta de inserción para agregar un nuevo registro
    $sql = "INSERT INTO `GastosPOS`(`Nom_Gasto`, `Estado`, `Concepto_Categoria`, `Importe_Total`, `Empleado`, `Fk_sucursal`, `Fk_Caja`, `Recibe`, `Sistema`, `Agregado_Por`, `Licencia`) 
            VALUES ('$NomGasto', '$Estado', '$ConceptoCategoria', '$ImporteTotal', '$Empleado', '$FkSucursal', '$FkCaja', '$Recibe', '$Sistema', '$AgregadoPor', '$Licencia')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode"=>200)); // Inserción exitosa
    } else {
        echo json_encode(array("statusCode"=>201)); // Error en la inserción
    }
    mysqli_close($conn);
}
?>
