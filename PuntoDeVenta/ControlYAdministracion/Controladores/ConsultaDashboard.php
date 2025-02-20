<?php
include_once("db_connect.php"); // Abrir la conexión una sola vez

// Consulta para contar cajas abiertas
$sqlCajas = "SELECT COUNT(*) AS CajasAbiertas FROM Cajas WHERE Estatus = 'Abierta' AND Sucursal != 4";
$resultCajas = $conn->query($sqlCajas);
$CajasAbiertas = 0; // Inicializamos la variable

if ($resultCajas && $resultCajas->num_rows > 0) {
    $cajasData = $resultCajas->fetch_assoc();
    $CajasAbiertas = $cajasData['CajasAbiertas'] ?? 0;
}

// Consulta para calcular total de ventas
$sqlVentas = "SELECT SUM(Importe) + SUM(Pagos_tarjeta) AS Total_Venta FROM Ventas_POS WHERE DATE(Fecha_venta) = CURDATE()";
$resultVentas = $conn->query($sqlVentas);
$formattedTotal = "0.00"; // Valor predeterminado

if ($resultVentas && $resultVentas->num_rows > 0) {
    $ventasData = $resultVentas->fetch_assoc();
    $formattedTotal = number_format($ventasData['Total_Venta'], 2, '.', ',') ?? "0.00";
}



// Cerrar la conexión
$conn->close();
?>
