<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
    si.IdProdCedis, 
    si.ID_Prod_POS, 
    si.NumFactura, 
    si.Proveedor, 
    si.Cod_Barra, 
    si.Nombre_Prod, 
    si.Fk_Sucursal, 
    s.Nombre_Sucursal,
    si.Contabilizado, 
    si.Fecha_Caducidad, 
    si.Lote, 
    si.PrecioMaximo, 
    si.Precio_Venta, 
    si.Precio_C, 
    si.AgregadoPor, 
    si.AgregadoEl, 
    si.FechaInventario, 
    si.Estatus, 
    si.NumOrden
FROM 
    Solicitudes_Ingresos si
JOIN 
    Sucursales s ON si.Fk_Sucursal = s.ID_Sucursal
WHERE 
    si.Estatus <> 'Autorizado' AND 
si.Fk_Sucursal = ?";

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Obtener el valor de Fk_Sucursal
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

// Vincular parámetro
$stmt->bind_param("s", $fk_sucursal);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Definir el estilo y el texto según el valor de estatus
    $estatus_estilo = '';
    $estatus_leyenda = '';
    switch ($fila["Estatus"]) {
        case 'Ingresado':
            $estatus_estilo = 'background-color: #008080; color: white;'; // Verde marino
            $estatus_leyenda = 'Ingresado';
            break;
        case 'Pendiente':
            $estatus_estilo = 'background-color: red; color: white;'; // Rojo
            $estatus_leyenda = 'Pendiente';
            break;
        default:
            $estatus_estilo = ''; // No se aplica estilo
            $estatus_leyenda = $fila["Estatus"];
            break;
    }
    $realizar_corte = '<td><a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-success btn-sm btn-AutorizaIngreso" style="color:white;"><i class="fa-solid fa-check"></i></a></td>';
    $EditarDatosDeIngreso = '<td><a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-primary btn-sm btn-EditaIngreso" style="color:white;"><i class="fa-solid fa-file-pen"></i></a></td>';
    $EliminarIngreso = '<td><a data-id="' . $fila["IdProdCedis"] . '" class="btn btn-danger btn-sm btn-EliminaIngreso" style="color:white;"><i class="fa-solid fa-trash"></i></a></td>';
    // Construir el array de datos incluyendo las columnas de la consulta
    $data[] = [
        "IdProdCedis" => $fila["IdProdCedis"],
       
        "NumFactura" => $fila["NumFactura"],
        // "NumOrden" => $fila["NumOrden"],
        "Proveedor" => $fila["Proveedor"],
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Contabilizado" => $fila["Contabilizado"],
    //    "Estatus" => "<div style=\"$estatus_estilo; padding: 5px; border-radius: 5px;\">$estatus_leyenda</div>",
        "AgregadoPor" => $fila["AgregadoPor"],
        "FechaInventario" => $fila["FechaInventario"],
        "RealizarCorte" => $realizar_corte,
        "EditarIngreso" => $EditarDatosDeIngreso,
        "EliminarIngreso" => $EliminarIngreso,
        
    ];
}

// Cerrar la declaración
$stmt->close();

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

// Imprimir la respuesta JSON
echo json_encode($results);

// Cerrar conexión
$conn->close();
?>
