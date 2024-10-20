<?php 
date_default_timezone_set("America/Monterrey");

include_once("db_connect.php");

// Inicializar la variable antes de la consulta
$CajasAbiertas = 0;

// Consulta SQL para contar las cajas abiertas excluyendo la sucursal 4
$sql = "SELECT COUNT(*) AS CajasAbiertas 
        FROM Cajas 
        WHERE Estatus = 'Abierta' 
        AND Sucursal != 4";

$result = $conn->query($sql);

// Verificar y asignar el resultado
if ($result->num_rows > 0) {
    $CajasAbiertasResult = $result->fetch_assoc();
    
    // Asegurarse de que no sea null y asignar el valor
    if ($CajasAbiertasResult['CajasAbiertas'] !== null) {
        $CajasAbiertas = $CajasAbiertasResult['CajasAbiertas'];
    }
} else {
    echo "No se encontraron resultados.";
}

// Cerrar la conexión
$conn->close();
?>

<!-- Mostrar el resultado en HTML -->