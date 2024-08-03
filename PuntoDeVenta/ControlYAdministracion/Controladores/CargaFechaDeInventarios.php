<?php
header('Content-Type: application/json');
include("db_connect.php");

// Consulta SQL para obtener las fechas
$sql = "SELECT DISTINCT FechaInventario FROM  InventariosStocks_Conteos;"; // Reemplaza 'InventariosStocks_Conteos' con el nombre de tu tabla
$result = $conn->query($sql);

$fechas = array();
while ($row = $result->fetch_assoc()) {
    $fechas[] = $row['FechaInventario']; // Usa el nombre correcto del campo
}

$conn->close();

echo json_encode($fechas);
?>
