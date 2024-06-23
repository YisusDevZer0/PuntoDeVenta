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
    // Construir acciones como un array de botones
    $acciones = [
        "<a href='https://controlfarmacia.com/AdminPOS/AsignacionSucursalesStock?idProd=" . base64_encode($fila["IdProdCedis"]) . "' class='btn-edit dropdown-item'>Asignar en sucursales <i class='fas fa-clinic-medical'></i></a>",
        "<a href='https://controlfarmacia.com/AdminPOS/DistribucionSucursales?Disid=" . base64_encode($fila["IdProdCedis"]) . "' class='btn-VerDistribucion dropdown-item'>Consultar distribución <i class='fas fa-table'></i></a>",
        "<a href='https://controlfarmacia.com/AdminPOS/EdicionDatosProducto?editprod=" . base64_encode($fila["IdProdCedis"]) . "' class='btn-editProd dropdown-item'>Editar datos <i class='fas fa-pencil-alt'></i></a>",
        "<a href='https://controlfarmacia.com/AdminPOS/HistorialProducto?idProd=" . base64_encode($fila["IdProdCedis"]) . "' class='btn-History dropdown-item'>Ver movimientos <i class='fas fa-history'></i></a>",
        "<a href='https://controlfarmacia.com/AdminPOS/MaximoYMinimo?Disid=" . base64_encode($fila["IdProdCedis"]) . "' class='btn-Delete dropdown-item'>Actualizar mínimo y máximo <i class='fas fa-list-ol'></i></a>",
        "<a href='https://controlfarmacia.com/AdminPOS/CambiaProveedor?idProd=" . base64_encode($fila["IdProdCedis"]) . "' class='btn-Delete dropdown-item'>Cambio de proveedores <i class='fas fa-truck-loading'></i></a>"
    ];

    // Construir acciones como HTML
    $acciones_html = "<div class='dropdown'>
                        <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                            Acciones
                        </button>
                        <div class='dropdown-menu'>
                            " . implode('', $acciones) . "
                        </div>
                    </div>";

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
