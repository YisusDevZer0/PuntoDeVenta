<?php
include_once 'db_connect.php';
date_default_timezone_set('America/Mexico_City');


$Fk_Fondo = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['FkFondo']))));
$Cantidad_Fondo = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Cantidad']))));
$Empleado = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Empleado']))));
$Sucursal = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Sucursal']))));
$Estatus = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Estatus']))));
$CodigoEstatus = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['EstadoColor']))));
$Fecha_Apertura = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Fecha']))));
$Turno = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Turno']))));
$Asignacion = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Asignacion']))));
$Valor_Total_Caja = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['TotalCaja']))));
$Hora_apertura = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['HoraApertura']))));
$Hora_real_apertura = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['HoraRealApertura']))));
$Sistema = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Sistema']))));
$Licencia = $conn->real_escape_string(htmlentities(strip_tags(trim($_POST['Licencia']))));

// Verificar si ya existe un registro con los mismos valores
$sql = "SELECT Empleado,Sucursal,Estatus,Fecha_Apertura,Licencia,Cantidad_Fondo,Turno,Asignacion FROM Cajas_POS
        WHERE Empleado='$Empleado' AND Sucursal='$Sucursal' AND Estatus='$Estatus' AND Fecha_Apertura='$Fecha_Apertura' AND Cantidad_Fondo='$Cantidad_Fondo'AND Turno='$Turno' 
        AND Asignacion='$Asignacion' AND Licencia='$Licencia'";
$resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);

// Verificar si ya existe un registro con los mismos valores
if ($row && $row['Empleado'] == $Empleado && $row['Sucursal'] == $Sucursal && $row['Estatus'] == $Estatus && $row['Fecha_Apertura'] == $Fecha_Apertura
    && $row['Cantidad_Fondo'] == $Cantidad_Fondo && $row['Licencia'] == $Licencia && $row['Turno'] == $Turno && $row['Asignacion'] == $Asignacion) {
    echo json_encode(array("statusCode" => 250));
} else {
    // Insertar nuevo registro
    $sql = "INSERT INTO `Cajas_POS`( `Fk_Fondo`,`Cantidad_Fondo`,`Empleado`,`Sucursal`,`Estatus`,`CodigoEstatus`,`Fecha_Apertura`,`Turno`,`Asignacion`,`Valor_Total_Caja`,`Hora_apertura`,`Hora_real_apertura`,`Sistema`,`Licencia`) 
            VALUES ('$Fk_Fondo','$Cantidad_Fondo','$Empleado','$Sucursal','$Estatus','$CodigoEstatus','$Fecha_Apertura','$Turno','$Asignacion','$Valor_Total_Caja','$Hora_apertura','$Hora_real_apertura','$Sistema','$Licencia')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(array("statusCode" => 200));
    } else {
        echo json_encode(array("statusCode" => 201));
    }
    mysqli_close($conn);
}
?>
