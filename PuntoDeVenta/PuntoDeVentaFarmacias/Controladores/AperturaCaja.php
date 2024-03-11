<?php
include_once 'db_connect.php';
date_default_timezone_set('America/Mexico_City');
$Fk_Fondo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['FkFondo']))));
$Cantidad_Fondo = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Cantidad']))));
$Empleado = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Empleado']))));
$Sucursal = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Sucursal']))));
$Estatus = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Estatus']))));
$CodigoEstatus = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['CodEstatus']))));
$Fecha_Apertura = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Fecha']))));
$Turno = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Turno']))));
$Asignacion = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Asignacion']))));
$Valor_Total_Caja = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['TotalCaja']))));

$Sistema = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Sistema']))));
$Licencia = $conn->real_escape_string(htmlentities(strip_tags(Trim($_POST['Licencia']))));

// Verificar si ya existe un registro con los mismos valores
$sql = "SELECT Empleado,Sucursal,Estatus,Fecha_Apertura,Cantidad_Fondo,Turno,Asignacion FROM Cajas
        WHERE Empleado='$Empleado' AND Sucursal='$Sucursal' AND Estatus='$Estatus' AND Fecha_Apertura='$Fecha_Apertura' AND Cantidad_Fondo='$Cantidad_Fondo'AND Turno='$Turno' 
        AND Asignacion='$Asignacion' ";
$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);

// Verificar si ya existe un registro con los mismos valores
if ($row && $row['Empleado'] == $Empleado && $row['Sucursal'] == $Sucursal && $row['Estatus'] == $Estatus && $row['Fecha_Apertura'] == $Fecha_Apertura
    && $row['Cantidad_Fondo'] == $Cantidad_Fondo && $row['Turno'] == $Turno && $row['Asignacion'] == $Asignacion) {
    echo json_encode(array("statusCode" => 250));
} else {
    // Insertar nuevo registro
    $sql = "INSERT INTO `Cajas`( `Fk_Fondo`,`Cantidad_Fondo`,`Empleado`,`Sucursal`,`Estatus`,`CodigoEstatus`,`Fecha_Apertura`,`Turno`,`Asignacion`,`Valor_Total_Caja`,`Sistema`,`Licencia`) 
            VALUES ('$Fk_Fondo','$Cantidad_Fondo','$Empleado','$Sucursal','$Estatus','$CodigoEstatus','$Fecha_Apertura','$Turno','$Asignacion','$Valor_Total_Caja','$Sistema','$Licencia')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode" => 200));
    } else {
        echo json_encode(array("statusCode" => 201));
    }
    mysqli_close($conn);
}
?>
