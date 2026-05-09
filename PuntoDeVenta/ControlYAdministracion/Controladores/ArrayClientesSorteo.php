<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

$sql = "SELECT ID_Data_Paciente, Nombre_Paciente, Telefono, Fecha_Nacimiento, Edad, 
        Correo, SucursalVisita, Ingresadoen, Sistema
        FROM Data_Pacientes 
        ORDER BY ID_Data_Paciente DESC
        LIMIT 500";

$result = mysqli_query($conn, $sql);

$c = 0;
$data = [];

while ($fila = $result->fetch_assoc()) {
    $data[$c]["ID"] = $fila["ID_Data_Paciente"];
    $data[$c]["Nombre"] = $fila["Nombre_Paciente"];
    $data[$c]["Telefono"] = $fila["Telefono"] ? $fila["Telefono"] : '-';
    $data[$c]["FechaNac"] = $fila["Fecha_Nacimiento"] ? $fila["Fecha_Nacimiento"] : '-';
    $data[$c]["Edad"] = $fila["Edad"] ? $fila["Edad"] : '-';
    $data[$c]["Correo"] = $fila["Correo"] ? $fila["Correo"] : '-';
    $data[$c]["Sucursal"] = $fila["SucursalVisita"] ? $fila["SucursalVisita"] : '-';
    $data[$c]["Registrado"] = $fila["Ingresadoen"] ? $fila["Ingresadoen"] : '-';
    $data[$c]["Sistema"] = $fila["Sistema"] ? '<span class="badge bg-secondary">'.$fila["Sistema"].'</span>' : '-';
    
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
