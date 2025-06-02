<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "Controladores/ControladorUsuario.php";

$sql = "SELECT Folio_Ingreso, Cod_Barra, Nombre_Producto, Fk_sucursal, Existencias_R, ExistenciaFisica, AgregadoPor, AgregadoEl, EnPausa FROM ConteosDiarios WHERE EnPausa = 1 LIMIT 1";

$result = mysqli_query($conn, $sql);

$data = [];

while($fila = $result->fetch_assoc()){
    $data[] = [
        "Folio_Ingreso" => $fila["Folio_Ingreso"],
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Producto" => $fila["Nombre_Producto"],
        "Fk_sucursal" => $fila["Fk_sucursal"],
        "Existencias_R" => $fila["Existencias_R"],
        "ExistenciaFisica" => $fila["ExistenciaFisica"],
        "AgregadoPor" => $fila["AgregadoPor"],
        "AgregadoEl" => $fila["AgregadoEl"],
        "EnPausa" => $fila["EnPausa"]
    ];
}

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);
?>
