<?php
include "db_connect.php";
// Realiza la consulta SQL para obtener los datos
$query = "SELECT ID_Sucursal ,Nombre_Sucursal  FROM Sucursales"; // Reemplaza 'tabla' por el nombre de tu tabla

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error al obtener datos de la base de datos: " . mysqli_error($conn));
}

// Prepara un array para almacenar los resultados
$datos = array();

// Recorre los resultados y agrega cada fila al array
while ($fila = mysqli_fetch_assoc($result)) {
    $datos[] = $fila;
}

// Cierra la conexión a la base de datos
mysqli_close($conn);

// Devuelve los datos como un JSON
echo json_encode($datos);
?>
