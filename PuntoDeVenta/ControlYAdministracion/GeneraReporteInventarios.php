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
            inv_stocks.`Folio_Prod_Stock`, 
            inv_stocks.`ID_Prod_POS`, 
            inv_stocks.`Cod_Barra`, 
            inv_stocks.`Nombre_Prod`, 
            inv_stocks.`Fk_sucursal` AS `Fk_Sucursal`, 
            inv_stocks.`Precio_Venta`, 
            inv_stocks.`Precio_C`, 
            inv_stocks.`Contabilizado`, 
            inv_stocks.`StockEnMomento`, 
            inv_stocks.`Diferencia`, 
            inv_stocks.`Sistema`, 
            inv_stocks.`AgregadoPor`, 
            inv_stocks.`AgregadoEl`, 
            inv_stocks.`ID_H_O_D`, 
            inv_stocks.`FechaInventario`,
            inv_stocks.`Tipo_Ajuste`,
            inv_stocks.`Anaquel`,
            inv_stocks.`Repisa`,
            (inv_stocks.`Contabilizado` * inv_stocks.`Precio_Venta`) AS `Total_Precio_Venta`,
            (inv_stocks.`Contabilizado` * inv_stocks.`Precio_C`) AS `Total_Precio_Compra`
        FROM 
            `InventariosStocks_Conteos` inv_stocks
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

// Escribir encabezados CSV
fputcsv($output, [
    'Folio_Prod_Stock', 'ID_Prod_POS', 'Cod_Barra', 'Nombre_Prod', 'Fk_Sucursal', 
    'Precio_Venta', 'Precio_C', 'Contabilizado', 'Existencia previa', 'Diferencia', 
    'Sistema', 'AgregadoPor', 'AgregadoEl',  'FechaInventario', 
    'Tipo_Ajuste', 'Anaquel', 'Repisa', 'Total_Precio_Venta', 'Total_Precio_Compra'
]);

// Escribir los datos en el archivo CSV
while ($fila = $result->fetch_assoc()) {
    fputcsv($output, [
        $fila["Folio_Prod_Stock"],
        $fila["ID_Prod_POS"],
        $fila["Cod_Barra"],
        $fila["Nombre_Prod"],
        $fila["Fk_Sucursal"],
        $fila["Precio_Venta"],
        $fila["Precio_C"],
        $fila["Contabilizado"],
        $fila["StockEnMomento"],
        $fila["Diferencia"],
        $fila["Sistema"],
        $fila["AgregadoPor"],
        $fila["AgregadoEl"],
       
        $fila["FechaInventario"],
        $fila["Tipo_Ajuste"],
        $fila["Anaquel"],
        $fila["Repisa"],
        $fila["Total_Precio_Venta"],
        $fila["Total_Precio_Compra"]
    ]);
}

fclose($output);
$stmt->close();
$conn->close();
?>
