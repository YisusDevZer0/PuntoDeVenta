<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set("America/Monterrey");

include_once("db_connect.php");

// Consulta SQL para contar las cajas abiertas excluyendo la sucursal 4
$sql = "SELECT COUNT(*) AS CajasAbiertas 
        FROM Cajas 
        WHERE Estatus = 'Abierta' 
        AND Sucursal != 4";

$result = $conn->query($sql);

// Verificar y mostrar el resultado
if ($result->num_rows > 0) {
    $CajasAbiertas = $result->fetch_assoc();
    
    // Asegúrate de que no sea null
    if ($CajasAbiertas['CajasAbiertas'] !== null) {
        echo "Cajas abiertas: " . $CajasAbiertas['CajasAbiertas'];
    } else {
        echo "No hay cajas abiertas.";
    }
} else {
    echo "No se encontraron resultados.";
}


?> 
