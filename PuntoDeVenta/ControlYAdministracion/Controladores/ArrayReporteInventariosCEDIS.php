<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
    `IdProdCedis`, 
    `ID_Prod_POS`, 
    `Cod_Barra`, 
    `Nombre_Prod`, 
    `Contabilizado`, 
    `StockEnMomento`, 
    `ExistenciasAjuste`, 
    `Precio_Venta`, 
    `Precio_C`, 
    `AgregadoPor`, 
    `AgregadoEl`, 
    `FechaInventario`,
    (`Contabilizado` * `Precio_Venta`) AS `Total_Precio_Venta`,
    (`Contabilizado` * `Precio_C`) AS `Total_Precio_Compra`
FROM 
    `Cedis_Inventarios`";

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
        "FechaEntrega" => $fila["FechaInventario"], // Assuming "FechaInventario" corresponds to "FechaEntrega"
        "TraspasoGeneradoPor" => "", // Placeholder as it's not in the provided query
        "TraspasoRecibidoPor" => "", // Placeholder as it's not in the provided query
        "Estatus" => "", // Placeholder as it's not in the provided query
        "AgregadoPor" => $fila["AgregadoPor"],
        "AgregadoEl" => $fila["AgregadoEl"],
        "Fecha_recepcion" => "", // Placeholder as it's not in the provided query
        "SucursalDestino" => "" // Placeholder as it's not in the provided query
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
