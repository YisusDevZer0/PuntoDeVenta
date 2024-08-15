<?php
// Conexión a la base de datos
include_once("db_connect.php");

// Consulta para productos más vendidos
$mas_vendidos = $conn->query("SELECT Nombre_Prod, SUM(Cantidad_Venta) AS Total_Vendido 
FROM Ventas_POS 
WHERE MONTH(Fecha_venta) = MONTH(CURRENT_DATE) 
AND YEAR(Fecha_venta) = YEAR(CURRENT_DATE) 
GROUP BY Nombre_Prod 
ORDER BY Total_Vendido DESC 
LIMIT 10");

if (!$mas_vendidos) {
    die("Error en la consulta de productos más vendidos: " . $conn->error);
}

$productos_mas_vendidos = [];
while ($row = $mas_vendidos->fetch_assoc()) {
    $productos_mas_vendidos[] = $row;
}

// Consulta para productos no vendidos
$no_vendidos = $conn->query("SELECT p.Nombre_Prod 
    FROM Productos_POS p 
    LEFT JOIN Ventas_POS v 
    ON p.ID_Prod_POS = v.ID_Prod_POS 
    AND MONTH(v.Fecha_venta) = MONTH(CURRENT_DATE) 
    AND YEAR(v.Fecha_venta) = YEAR(CURRENT_DATE) 
    WHERE v.Cantidad_Venta IS NULL");

if (!$no_vendidos) {
    die("Error en la consulta de productos no vendidos: " . $conn->error);
}

$productos_no_vendidos = [];
while ($row = $no_vendidos->fetch_assoc()) {
    $productos_no_vendidos[] = $row['Nombre_Prod'];
}

// Devuelve los datos en formato JSON
header('Content-Type: application/json');
echo json_encode([
    'productos_mas_vendidos' => $productos_mas_vendidos,
    'productos_no_vendidos' => $productos_no_vendidos
]);
?>
