<?php 
date_default_timezone_set("America/Monterrey");

include_once("db_connect.php");

// Consulta SQL para calcular el total de venta del día
$sql = "SELECT SUM(Importe) + SUM(Pagos_tarjeta) AS Total_Venta 
        FROM Ventas_POS 
        WHERE DATE(Fecha_venta) = CURDATE()";

$result = $conn->query($sql);

// Verificar y mostrar el resultado
if ($result->num_rows > 0) {
    $VentasGenerales = $result->fetch_assoc();
   
} else {
  
}

// Cerrar la conexión
$conn->close();
?>
