<?php
include_once 'db_connect.php';




$Nombre_Apellidos = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Clav']))));
$Password = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['PC']))));
$file_name = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Archivo']))));
$Fk_Usuario = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Tip']))));
$Fk_Sucursal = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Sucursal']))));
$Fecha_Nacimiento = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['fechanac']))));
$Correo_Electronico = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['PV']))));
$Telefono = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Tel']))));
$AgregadoPor = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['AgregaProductosBy']))));
$Estatus = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Estatus']))));
$Licencia = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Licencia']))));



//include database configuration file
$sql = "SELECT Nombre_Apellidos,Fk_Sucursal FROM Usuarios_PV 
    WHERE Nombre_Apellidos='$Nombre_Apellidos' AND Fk_Sucursal='$Fk_Sucursal' ";
$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));

$row = mysqli_fetch_assoc($resultset);

//include database configuration file
if ($row && $row['Nombre_Apellidos'] == $Nombre_Apellidos and $row['Fk_Sucursal'] == $Fk_Sucursal)
{
    echo json_encode(array("statusCode" => 250));
}else {
    $sql = $sql = "INSERT INTO `Usuarios_PV`(`Nombre_Apellidos`, `Password`, `file_name`, `Fk_Usuario`, `Fk_Sucursal`, `Fecha_Nacimiento`, `Correo_Electronico`, `Telefono`, `AgregadoPor`,`Estatus`, `Licencia`) 
    VALUES ('$Nombre_Apellidos','$Password','$file_name','$Fk_Usuario','$FK_Sucursal','$Fecha_Nacimiento','$Correo_Electronico','$Telefono','$AgregadoPor','$Estatus','$Licencia')";


    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode" => 200));
    } else {
        echo json_encode(array("statusCode" => 201));
    }
    mysqli_close($conn);
}