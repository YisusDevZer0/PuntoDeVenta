<?php
header('Content-Type: application/json');
include("db_connection.php");
// Consulta SQL para obtener las fechas
$sql = "SELECT DISTINCT FechaInventario FROM InventariosStocks_Conteos;"; // Reemplaza 'tabla_fechas' con el nombre de tu tabla
$result = $conn->query($sql);

$fechas = array();
while ($row = $result->fetch_assoc()) {
    $fechas[] = $row['fecha'];
}

$conn->close();

echo json_encode($fechas);
?>
