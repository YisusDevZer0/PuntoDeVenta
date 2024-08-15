<?php
// Conexión a la base de datos
include_once("db_connect.php");

// Consulta para productos más vendidos
$mas_vendidos = $conexion->query("
    SELECT Nombre_Prod, SUM(Cantidad_Venta) AS Total_Vendido 
    FROM Ventas_POS 
    WHERE MONTH(Fecha_venta) = MONTH(CURDATE()) 
    AND YEAR(Fecha_venta) = YEAR(CURDATE()) 
    GROUP BY Nombre_Prod 
    ORDER BY Total_Vendido DESC 
    LIMIT 10;
");

$productos_mas_vendidos = [];
while ($row = $mas_vendidos->fetch_assoc()) {
    $productos_mas_vendidos[] = $row;
}

// Consulta para productos no vendidos
$no_vendidos = $conexion->query("
    SELECT p.Nombre_Prod 
    FROM Productos_POS p 
    LEFT JOIN Ventas_POS v 
    ON p.ID_Prod_POS = v.ID_Prod_POS 
    AND MONTH(v.Fecha_venta) = MONTH(CURDATE()) 
    AND YEAR(v.Fecha_venta) = YEAR(CURDATE()) 
    WHERE v.Cantidad_Venta IS NULL;
");

$productos_no_vendidos = [];
while ($row = $no_vendidos->fetch_assoc()) {
    $productos_no_vendidos[] = $row['Nombre_Prod'];
}
?>
