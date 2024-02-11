<?php
include_once 'db_connect.php';

if(isset($_POST['NombreSucursal'], $_POST['Direccion'], $_POST['Telefono'], $_POST['PinEquipo'])) {
    $Nombre_Sucursal = $_POST['NombreSucursal'];
    $Direccion = $_POST['Direccion'];
    $Telefono = $_POST['Telefono'];
    $Pin_Equipo = $_POST['PinEquipo'];

    $sql = "INSERT INTO `Sucursales` (`Nombre_Sucursal`, `Direccion`, `Telefono`, `Pin_Equipo`) 
            VALUES ('$Nombre_Sucursal', '$Direccion', '$Telefono', '$Pin_Equipo')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode"=>200, "message"=>"Sucursal insertada correctamente"));
    } else {
        echo json_encode(array("statusCode"=>500, "message"=>"Error al insertar la sucursal: " . mysqli_error($conn)));
    }
} else {
    echo json_encode(array("statusCode"=>400, "message"=>"No se recibieron todas las variables POST esperadas"));
}

mysqli_close($conn);
?>
