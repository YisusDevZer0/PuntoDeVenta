<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

$sql = "SELECT 
    `ID_Prod_POS`, 
    `Id_Actualizado`, 
    `Cod_Barra`, 
    `Clave_adicional`, 
    `Nombre_Prod`, 
    `Precio_Venta`, 
    `Precio_C`, 
    `Componente_Activo`, 
    `Tipo`, 
    `FkCategoria`, 
    `FkMarca`, 
    `FkPresentacion`, 
    `Proveedor1`, 
    `Proveedor2`, 
    `RecetaMedica`, 
    `Licencia`, 
    `Ivaal16`, 
    `ActualizadoPor`, 
    `ActualizadoEl`, 
    `Contable`
FROM 
    `ActualizacionesMasivasProductosPOS`
WHERE 
    DATE(`ActualizadoEl`) = CURRENT_DATE;
";

$result = mysqli_query($conn, $sql);

$data = [];
while ($fila = $result->fetch_assoc()) {
    // Determinar el HTML del botón en función del valor de Cod_Barra
    if (empty($fila["Cod_Barra"])) {
        // Cod_Barra está vacío: mostrar botón para crear código de barras
        $acciones_html = '
        <td>
            <a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-warning btn-sm btn-ConsultarCambios" title="Consultar Cambios"><i class="far fa-calendar-times"></i></a>
            <a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-primary btn-sm btn-CrearCodBar" title="Crear Código de Barras"><i class="fas fa-barcode"></i></a>
        </td>';
    } else {
        // Cod_Barra no está vacío: mostrar botón de editar y consultar cambios
        $acciones_html = '
        <td>
            <a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-success btn-sm btn-EditarProd" title="Editar Producto"><i class="fas fa-exchange-alt"></i></a>
            <a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-warning btn-sm btn-ConsultarCambios" title="Consultar Cambios"><i class="far fa-calendar-times"></i></a>
             <a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-danger btn-sm btn-EliminarData" title="Eliminar datos"><i class="fa-solid fa-circle-minus"></i></a>
        </td>';
    }

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
