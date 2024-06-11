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
WHERE si.Fk_Sucursal = ?";

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
        case 'Abierta':
            $estatus_estilo = 'background-color: #008080; color: white;'; // Verde marino
            $estatus_leyenda = 'Abierta';
            break;
        case 'Cerrada':
            $estatus_estilo = 'background-color: red; color: white;'; // Rojo
            $estatus_leyenda = 'Cerrada';
            break;
        default:
            $estatus_estilo = ''; // No se aplica estilo
            $estatus_leyenda = $fila["Estatus"];
            break;
    }

    // Construir el array de datos incluyendo las columnas de la consulta
    $data[] = [
        "IdProdCedis" => $fila["IdProdCedis"],
        "ID_Prod_POS" => $fila["ID_Prod_POS"],
        "NumFactura" => $fila["NumFactura"],
        "Proveedor" => $fila["Proveedor"],
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Fk_Sucursal" => $fila["Fk_Sucursal"],
        "Nombre_Sucursal" => $fila["Nombre_Sucursal"],
        "Contabilizado" => $fila["Contabilizado"],
        "Fecha_Caducidad" => $fila["Fecha_Caducidad"],
        "Lote" => $fila["Lote"],
        "PrecioMaximo" => $fila["PrecioMaximo"],
        "Precio_Venta" => $fila["Precio_Venta"],
        "Precio_C" => $fila["Precio_C"],
        "AgregadoPor" => $fila["AgregadoPor"],
        "AgregadoEl" => $fila["AgregadoEl"],
        "FechaInventario" => $fila["FechaInventario"],
        "Estatus" => "<div style=\"$estatus_estilo; padding: 5px; border-radius: 5px;\">$estatus_leyenda</div>",
        "NumOrden" => $fila["NumOrden"]
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
