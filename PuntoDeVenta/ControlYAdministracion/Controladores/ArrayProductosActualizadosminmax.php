<?php
header('Content-Type: application/json');
include("dbconect.php"); // Asegúrate de que este archivo contiene la conexión a la base de datos

$sql = "SELECT 
    id, 
    Folio_Prod_Stock, 
    ID_Prod_POS, 
    Cod_Barra, 
    Nombre_Prod, 
    Fk_sucursal, 
    Nombre_Sucursal, 
    Max_Existencia, 
    Min_Existencia, 
    AgregadoPor, 
    FechaAgregado
FROM 
    ActualizacionMaxMin
ORDER BY FechaAgregado DESC";

$result = mysqli_query($con, $sql);

$data = [];
while ($fila = $result->fetch_assoc()) {
    // Agregar datos al array $data
    $data[] = [
        "id" => $fila["id"],
        "Folio_Prod_Stock" => $fila["Folio_Prod_Stock"],
        "ID_Prod_POS" => $fila["ID_Prod_POS"],
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Fk_sucursal" => $fila["Fk_sucursal"],
        "Nombre_Sucursal" => $fila["Nombre_Sucursal"],
        "Max_Existencia" => $fila["Max_Existencia"],
        "Min_Existencia" => $fila["Min_Existencia"],
        "AgregadoPor" => $fila["AgregadoPor"],
        "FechaAgregado" => $fila["FechaAgregado"]
    ];
}

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data,
];

echo json_encode($results);

$con->close();
?>