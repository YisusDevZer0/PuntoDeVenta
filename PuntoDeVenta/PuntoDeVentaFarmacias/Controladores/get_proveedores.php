<?php
header('Content-Type: application/json');
include("db_connect.php");

// Consulta para obtener los proveedores
$sql = "SELECT DISTINCT Proveedor FROM Proveedores"; // Ajusta el nombre de la tabla y la columna según tu estructura

// Ejecutar la consulta
$result = $conn->query($sql);

$proveedores = [];
if ($result->num_rows > 0) {
    // Obtener resultados
    while ($row = $result->fetch_assoc()) {
        $proveedores[] = $row['Nombre_Proveedor'];
    }
}

// Devolver los resultados como JSON
echo json_encode($proveedores);

// Cerrar la conexión
$conn->close();
?>
