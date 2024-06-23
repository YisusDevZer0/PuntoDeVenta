<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

$sql = "SELECT 
    Productos_POS.ID_Prod_POS as IdProdCedis, 
    Productos_POS.Cod_Barra, 
    Productos_POS.Nombre_Prod, 
    Productos_POS.Clave_adicional as Clave_interna, 
    Productos_POS.Clave_Levic as Clave_Levic, 
    Productos_POS.Precio_Venta, 
    Productos_POS.Precio_C, 
    Servicios_POS.Nom_Serv as Nom_Serv, 
    Productos_POS.FkMarca as Marca, 
    Productos_POS.Tipo, 
    Productos_POS.FkCategoria as Categoria, 
    Productos_POS.FkPresentacion as Presentacion, 
    Productos_POS.Proveedor1, 
    Productos_POS.AgregadoPor, 
    Productos_POS.AgregadoEl
FROM 
    Productos_POS
LEFT JOIN 
    Servicios_POS ON Servicios_POS.Servicio_ID = Productos_POS.Tipo_Servicio";

$result = mysqli_query($conn, $sql);

$data = [];
while ($fila = $result->fetch_assoc()) {
    // Botones individuales para cada fila
    $acciones = [
        "<button type='button' class='btn btn-warning btn-sm' style='margin-right: 5px;' data-toggle='modal' data-target='#modalEditar" . $fila["IdProdCedis"] . "'><i class='fas fa-pencil-alt'></i></button>",
        "<button type='button' class='btn btn-secondary btn-sm' style='margin-right: 5px;' data-toggle='modal' data-target='#modalHistorial" . $fila["IdProdCedis"] . "'><i class='fas fa-history'></i></button>"
    ];

    // Construir acciones como HTML
    $acciones_html = implode('', $acciones);

    // Agregar datos al array $data
    $data[] = [
        "IdProdCedis" => $fila["IdProdCedis"],
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Clave_interna" => $fila["Clave_interna"],
        "Clave_Levic" => $fila["Clave_Levic"],
        "Precio_C" => $fila["Precio_C"],
        "Precio_Venta" => $fila["Precio_Venta"],
        "Nom_Serv" => $fila["Nom_Serv"],
        "Marca" => $fila["Marca"],
        "Tipo" => $fila["Tipo"],
        "Categoria" => $fila["Categoria"],
        "Presentacion" => $fila["Presentacion"],
        "Proveedor1" => $fila["Proveedor1"],
        "AgregadoPor" => $fila["AgregadoPor"],
        "FechaInventario" => $fila["AgregadoEl"], // Cambiado el nombre para adaptarse
        "Acciones" => $acciones_html,
    ];
}

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data,
];

echo json_encode($results);

$conn->close();
?>
