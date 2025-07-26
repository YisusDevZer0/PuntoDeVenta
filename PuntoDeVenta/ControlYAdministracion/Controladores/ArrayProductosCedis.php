<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

$sql = "SELECT 
            CEDIS.IdProdCedis, 
            CEDIS.ID_Prod_POS, 
            CEDIS.Cod_Barra, 
            CEDIS.Clave_adicional AS Clave_interna, 
            CEDIS.Clave_Levic AS Clave_Levic, 
            CEDIS.Nombre_Prod, 
            CEDIS.Precio_Venta, 
            CEDIS.Precio_C, 
            CEDIS.Lote_Med, 
            CEDIS.Fecha_Caducidad, 
            CEDIS.Existencias, 
            Servicios_POS.Nom_Serv AS Tipo_Servicio, 
            CEDIS.Componente_Activo, 
            CEDIS.Tipo, 
            CEDIS.FkCategoria AS Categoria, 
            CEDIS.FkMarca AS Marca, 
            CEDIS.FkPresentacion AS Presentacion, 
            CEDIS.Proveedor1, 
            CEDIS.Proveedor2, 
            CEDIS.RecetaMedica, 
            CEDIS.Estatus, 
            CEDIS.AgregadoPor, 
            CEDIS.AgregadoEl, 
            CEDIS.ActualizadoEl, 
            CEDIS.ActualizadoPor, 
            CEDIS.Contable
        FROM 
            CEDIS
        LEFT JOIN 
            Servicios_POS ON Servicios_POS.Servicio_ID = CEDIS.Tipo_Servicio";

$result = mysqli_query($conn, $sql);

$data = [];
while ($fila = $result->fetch_assoc()) {
    // Determinar el HTML del botón en función del valor de Cod_Barra
    
        // Cod_Barra no está vacío: mostrar botón de editar y consultar cambios
        $acciones_html = '
        <td>
           <a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-primary btn-sm btn-RealizarIngresos" title="Realizar Ingresos"><i class="fa-solid fa-boxes-packing"></i></a>
            <a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-warning btn-sm btn-LotesYCaducidades" title="Lotes Y Caducidades"><i class="far fa-calendar-times"></i></a>
            <a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-danger btn-sm btn-EliminarData" title="Eliminar datos"><i class="fa-solid fa-circle-minus"></i></a>
        </td>';
    

    // Agregar datos al array $data
    $data[] = [
        "IdProdCedis" => $fila["IdProdCedis"],
        
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
   
        "Precio_C" => $fila["Precio_C"],
        "Precio_Venta" => $fila["Precio_Venta"],
        "Lote_Med" => $fila["Lote_Med"],
        "Fecha_Caducidad" => $fila["Fecha_Caducidad"],
        "Existencias" => $fila["Existencias"],
        "Tipo_Servicio" => $fila["Tipo_Servicio"],
      
        "Tipo" => $fila["Tipo"],
        "Categoria" => $fila["Categoria"],
        "Marca" => $fila["Marca"],
        "Presentacion" => $fila["Presentacion"],
        "Proveedor1" => $fila["Proveedor1"],
        "Proveedor2" => $fila["Proveedor2"],
        "FechaInventario" => $fila["AgregadoEl"], // Cambiado el nombre para adaptarse
        "ActualizadoEl" => $fila["ActualizadoEl"], // Añadido en caso de ser necesario
       
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
