<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
    ci.`IdProdCedis`, 
    ci.`ID_Prod_POS`, 
    ci.`Cod_Barra`, 
    ci.`Nombre_Prod`, 
    ci.`Contabilizado`, 
    ci.`StockEnMomento`, 
    ci.`ExistenciasAjuste`, 
    ci.`Precio_Venta`, 
    ci.`Precio_C`, 
    ci.`AgregadoPor`, 
    ci.`AgregadoEl`, 
    ci.`FechaInventario`,
    ci.`Fk_Sucursal`,
    s.`Nombre_Sucursal`,
    (ci.`Contabilizado` * ci.`Precio_Venta`) AS `Total_Precio_Venta`,
    (ci.`Contabilizado` * ci.`Precio_C`) AS `Total_Precio_Compra`
FROM 
    `Cedis_Inventarios` ci
LEFT JOIN 
    `Sucursales` s ON ci.`Fk_Sucursal` = s.`ID_Sucursal`";

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
       "Nombre_Sucursal" => $fila["Nombre_Sucursal"], // Adding the sucursal name to the result
        "AgregadoPor" => $fila["AgregadoPor"],
        "AgregadoEl" => $fila["AgregadoEl"]
        
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
