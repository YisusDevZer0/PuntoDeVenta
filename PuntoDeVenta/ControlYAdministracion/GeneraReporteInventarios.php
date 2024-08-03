<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=inventario_sucursal.csv');

include("Controladores/db_connect.php");

// Verificar si se ha enviado la fecha y si es válida
if (!isset($_GET['fecha']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])) {
    die("Fecha no válida. Parámetro recibido: " . htmlspecialchars($_GET['fecha']));
}

$fecha = $_GET['fecha'];

// Adaptar la consulta para la tabla InventariosStocks_Conteos
$sql = "SELECT 
           
            inv_stocks.`ID_Prod_POS`, 
            inv_stocks.`Cod_Barra`, 
            inv_stocks.`Nombre_Prod`, 
            inv_stocks.`Contabilizado`, 
            inv_stocks.`StockEnMomento`, 
            inv_stocks.`ExistenciasAjuste`, 
            inv_stocks.`Precio_Venta`, 
            inv_stocks.`Precio_C`, 
            inv_stocks.`AgregadoPor`, 
            inv_stocks.`AgregadoEl`, 
            inv_stocks.`FechaInventario`,
            inv_stocks.`Fk_Sucursal`,
            s.`Nombre_Sucursal`,
            (inv_stocks.`Contabilizado` * inv_stocks.`Precio_Venta`) AS `Total_Precio_Venta`,
            (inv_stocks.`Contabilizado` * inv_stocks.`Precio_C`) AS `Total_Precio_Compra`
        FROM 
            `InventariosStocks_Conteos` inv_stocks
        LEFT JOIN 
            `Sucursales` s ON inv_stocks.`Fk_Sucursal` = s.`ID_Sucursal`
        WHERE 
            inv_stocks.`FechaInventario` = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $fecha);
if (!$stmt->execute()) {
    die("Error en la ejecución de la consulta: " . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();
if (!$result) {
    die("Error al obtener los resultados: " . htmlspecialchars($conn->error));
}

$output = fopen('php://output', 'w');
if (!$output) {
    die("Error al abrir el flujo de salida.");
}

fputcsv($output, [
    
     'ID_Prod_POS', 'Cod_Barra', 'Nombre_Prod', 'Contabilizado', 'StockEnMomento', 
    'ExistenciasAjuste', 'Precio_Venta', 'Precio_C', 'AgregadoPor', 'AgregadoEl', 'FechaInventario',
    'Fk_Sucursal', 'Nombre_Sucursal', 'Total_Precio_Venta', 'Total_Precio_Compra'
]);

while ($fila = $result->fetch_assoc()) {
    fputcsv($output, [
        
        $fila["ID_Prod_POS"],
        $fila["Cod_Barra"],
        $fila["Nombre_Prod"],
        $fila["Contabilizado"],
        $fila["StockEnMomento"],
        $fila["ExistenciasAjuste"],
        $fila["Precio_Venta"],
        $fila["Precio_C"],
        $fila["AgregadoPor"],
        $fila["AgregadoEl"],
        $fila["FechaInventario"],
        $fila["Fk_Sucursal"],
        $fila["Nombre_Sucursal"],
        $fila["Total_Precio_Venta"],
        $fila["Total_Precio_Compra"]
    ]);
}

fclose($output);
$stmt->close();
$conn->close();
?>
