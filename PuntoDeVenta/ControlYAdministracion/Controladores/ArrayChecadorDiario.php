<?php
header('Content-Type: application/json');
include("db_connection_Huellas.php");


$sql = "SELECT 
            ID_Registro,
            Nombre_Emp,
            DATE_FORMAT(Fecha_Registro, '%d-%m-%Y') as Fecha_Registro,
            TIME_FORMAT(Hora_Registro, '%H:%i:%s') as Hora_Registro,
            Tipo_Registro,
            Sucursal,
            Turno
        FROM Registros_Checador 
        ORDER BY ID_Registro DESC;";

$result = mysqli_query($conn, $sql);

$c = 0;
$data = array();

while($fila = $result->fetch_assoc()){
    $data[$c]["ID_Registro"] = $fila["ID_Registro"];
    $data[$c]["Nombre_Emp"] = $fila["Nombre_Emp"];
    $data[$c]["Fecha_Registro"] = $fila["Fecha_Registro"];
    $data[$c]["Hora_Registro"] = $fila["Hora_Registro"];
    $data[$c]["Tipo_Registro"] = $fila["Tipo_Registro"];
    $data[$c]["Sucursal"] = $fila["Sucursal"];
    $data[$c]["Turno"] = $fila["Turno"];
    $c++;
}

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);
?> 