<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
    is.`IdProdCedis`, 
    is.`ID_Prod_POS`, 
    is.`Cod_Barra`, 
    is.`Nombre_Prod`, 
    is.`Contabilizado`, 
    is.`StockEnMomento`, 
    is.`ExistenciasAjuste`, 
    is.`Precio_Venta`, 
    is.`Precio_C`, 
    is.`AgregadoPor`, 
    is.`AgregadoEl`, 
    is.`FechaInventario`,
    is.`Fk_Sucursal`,
    s.`Nombre_Sucursal`,
    (is.`Contabilizado` * is.`Precio_Venta`) AS `Total_Precio_Venta`,
    (is.`Contabilizado` * is.`Precio_C`) AS `Total_Precio_Compra`
FROM 
    `InventariosSucursales` is
LEFT JOIN 
    `Sucursales` s ON is.`Fk_Sucursal` = s.`ID_Sucursal`";

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Construir el array de datos
    $data[] = [
        "ID_Traspaso_Generado" => $fila["IdProdCedis"], // Assuming "IdProdCedis" corresponds to "ID_Traspaso_Generado"
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Precio_Venta" => $fila["Precio_Venta"],
        "Precio_Compra" => $fila["Precio_C"],
        "Cantidad_Enviada" => $fila["Contabilizado"], // Assuming "Contabilizado" corresponds to "Cantidad_Enviada"
        "Existencia" => $fila["StockEnMomento"], // Assuming "StockEnMomento" corresponds to "Existencia"
        "Diferencia" => $fila["ExistenciasAjuste"], // Assuming "ExistenciasAjuste" corresponds to "Diferencia"
        "FechaEntrega" => $fila["FechaInventario"], // Assuming "FechaInventario" corresponds to "FechaEntrega"
        "AgregadoPor" => $fila["AgregadoPor"],
        "AgregadoEl" => $fila["AgregadoEl"],
        "Nombre_Sucursal" => $fila["Nombre_Sucursal"] // Adding the sucursal name to the result
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
