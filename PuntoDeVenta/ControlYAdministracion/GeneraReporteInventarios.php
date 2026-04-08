<?php
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment;filename=inventario_sucursal.csv');

include("Controladores/db_connect.php");

if (!isset($_GET['fecha']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])) {
    die("Fecha no válida. Parámetro recibido: " . htmlspecialchars($_GET['fecha'] ?? ''));
}

$fecha = $_GET['fecha'];

// Fechas del modal: InventariosStocks_Conteos.
// Precios: igual que búsqueda POS (FarmacitasCore): COALESCE(Stock_POS, Productos_POS).
// InventariosSucursales: un registro por (código, sucursal, día) vía subconsulta (evita JOIN por fecha que no calza).
$sql = "SELECT 
    inv_suc.`IdProdCedis`,
    inv_stocks.`Folio_Prod_Stock`,
    inv_stocks.`ID_Prod_POS`,
    inv_stocks.`Cod_Barra`,
    inv_stocks.`Nombre_Prod`,
    COALESCE(
        inv_suc.`Precio_Venta`,
        inv_stocks.`Precio_Venta`,
        sp.`Precio_Venta`,
        pp.`Precio_Venta`,
        pp_cod.`Precio_Venta`
    ) AS `Precio_Venta`,
    COALESCE(
        inv_suc.`Precio_C`,
        inv_stocks.`Precio_C`,
        sp.`Precio_C`,
        pp.`Precio_C`,
        pp_cod.`Precio_C`
    ) AS `Precio_Compra`,
    inv_stocks.`Contabilizado`,
    inv_stocks.`StockEnMomento`,
    COALESCE(inv_suc.`ExistenciasAjuste`, inv_stocks.`Diferencia`) AS `Ajuste_Realizado`,
    inv_stocks.`AgregadoPor`,
    inv_stocks.`AgregadoEl`,
    inv_stocks.`FechaInventario`,
    inv_stocks.`Fk_sucursal` AS `Fk_Sucursal`,
    s.`Nombre_Sucursal`,
    inv_stocks.`Sistema`,
    inv_stocks.`Tipo_Ajuste`,
    inv_stocks.`Anaquel`,
    inv_stocks.`Repisa`,
    (inv_stocks.`Contabilizado` * COALESCE(
        inv_suc.`Precio_Venta`,
        inv_stocks.`Precio_Venta`,
        sp.`Precio_Venta`,
        pp.`Precio_Venta`,
        pp_cod.`Precio_Venta`
    )) AS `Total_Precio_Venta`,
    (inv_stocks.`Contabilizado` * COALESCE(
        inv_suc.`Precio_C`,
        inv_stocks.`Precio_C`,
        sp.`Precio_C`,
        pp.`Precio_C`,
        pp_cod.`Precio_C`
    )) AS `Total_Precio_Compra`
FROM `InventariosStocks_Conteos` inv_stocks
LEFT JOIN `InventariosSucursales` inv_suc
    ON inv_suc.`IdProdCedis` = (
        SELECT i2.`IdProdCedis`
        FROM `InventariosSucursales` i2
        WHERE TRIM(i2.`Cod_Barra`) = TRIM(inv_stocks.`Cod_Barra`)
          AND i2.`Fk_Sucursal` = inv_stocks.`Fk_sucursal`
          AND DATE(i2.`FechaInventario`) = DATE(inv_stocks.`FechaInventario`)
        ORDER BY i2.`IdProdCedis` DESC
        LIMIT 1
    )
LEFT JOIN `Stock_POS` sp
    ON sp.`ID_Prod_POS` = inv_stocks.`ID_Prod_POS`
    AND sp.`Fk_sucursal` = inv_stocks.`Fk_sucursal`
LEFT JOIN `Productos_POS` pp
    ON pp.`ID_Prod_POS` = inv_stocks.`ID_Prod_POS`
LEFT JOIN `Productos_POS` pp_cod
    ON TRIM(pp_cod.`Cod_Barra`) = TRIM(inv_stocks.`Cod_Barra`)
LEFT JOIN `Sucursales` s ON inv_stocks.`Fk_sucursal` = s.`ID_Sucursal`
WHERE DATE(inv_stocks.`FechaInventario`) = ?";

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

fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, [
    'IdProdCedis',
    'Folio_Prod_Stock',
    'ID_Prod_POS',
    'Cod_Barra',
    'Nombre_Prod',
    'Precio_Venta',
    'Precio_Compra',
    'Contabilizado',
    'Existencia_Previa',
    'Ajuste_Realizado',
    'FechaInventario',
    'AgregadoPor',
    'AgregadoEl',
    'Fk_Sucursal',
    'Nombre_Sucursal',
    'Sistema',
    'Tipo_Ajuste',
    'Anaquel',
    'Repisa',
    'Total_Precio_Venta',
    'Total_Precio_Compra',
], ',', '"', '\\');

while ($fila = $result->fetch_assoc()) {
    fputcsv($output, [
        $fila['IdProdCedis'],
        $fila['Folio_Prod_Stock'],
        $fila['ID_Prod_POS'],
        $fila['Cod_Barra'],
        $fila['Nombre_Prod'],
        $fila['Precio_Venta'],
        $fila['Precio_Compra'],
        $fila['Contabilizado'],
        $fila['StockEnMomento'],
        $fila['Ajuste_Realizado'],
        $fila['FechaInventario'],
        $fila['AgregadoPor'],
        $fila['AgregadoEl'],
        $fila['Fk_Sucursal'],
        $fila['Nombre_Sucursal'],
        $fila['Sistema'],
        $fila['Tipo_Ajuste'],
        $fila['Anaquel'],
        $fila['Repisa'],
        $fila['Total_Precio_Venta'],
        $fila['Total_Precio_Compra'],
    ], ',', '"', '\\');
}

fclose($output);
$stmt->close();
$conn->close();
