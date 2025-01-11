<?php
// Archivo: obtener_sucursales.php
include 'db_connect.php'; // Asegúrate de incluir la conexión a tu base de datos

$query = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales";
$resultado = $conn->query($query);

$sucursales = [];
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $sucursales[] = $fila;
    }
}

echo json_encode($sucursales); // Retorna los resultados como JSON
?>
