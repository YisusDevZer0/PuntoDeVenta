<?php
header('Content-Type: application/json');
include("db_connect.php");

// Fechas normalizadas a YYYY-MM-DD (coinciden con el filtro DATE() del CSV y con <input type="date">)
$sql = "SELECT DISTINCT DATE(`FechaInventario`) AS d
        FROM `InventariosStocks_Conteos`
        WHERE `FechaInventario` IS NOT NULL
        ORDER BY d DESC";
$result = $conn->query($sql);

$fechas = array();
while ($row = $result->fetch_assoc()) {
    $fechas[] = $row['d'];
}

$conn->close();

echo json_encode($fechas);
?>
