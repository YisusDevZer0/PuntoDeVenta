<?php 
date_default_timezone_set("America/Monterrey");

include_once("db_connect.php");

// Consulta SQL para calcular el total de venta del día
$sql = "SELECT SUM(Importe) + SUM(Pagos_tarjeta) AS Total_Venta 
        FROM Ventas_POS 
        WHERE DATE(Fecha_venta) = CURDATE()";

$result = $conn->query($sql);

// Inicializamos la variable para evitar el error
$formattedTotal = "0.00";  // Valor predeterminado en caso de que no haya ventas

if ($result->num_rows > 0) {
    $VentasGenerales = $result->fetch_assoc();
    
    // Asegúrate de que no sea null
    if ($VentasGenerales['Total_Venta'] !== null) {
        // Formatear el número para mostrarlo como pesos mexicanos
        $formattedTotal = number_format($VentasGenerales['Total_Venta'], 2, '.', ',');
    }
} else {
    echo "No se encontraron ventas para el día de hoy.";
}

// Ahora puedes usar $formattedTotal sin el error
echo "MX$ " . $formattedTotal;

// Cerrar la conexión
$conn->close();
?>
