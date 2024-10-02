<?php
header('Content-Type: application/json');
include("db_connect.php");
include("ControladorUsuario.php");

// Obtener el año y el mes actual
$currentYear = date("Y");
$currentMonth = date("m");

$sql = "SELECT 
    `Turno`, 
    `Folio_Ticket`, 
    `Cod_Barra`, 
    `Nombre_Prod`, 
    `Cantidad_Venta`, 
    `Fk_sucursal`, 
    `Total_Venta`, 
    `Importe`, 
    `Total_VentaG`, 
    `DescuentoAplicado`, 
    `FormaDePago`, 
    `CantidadPago`, 
    `Cambio`, 
    `Cliente`, 
    `Fecha_venta`, 
    `AgregadoPor`, 
    `AgregadoEl`,
    `Pagos_tarjeta`
FROM 
    `Ventas_POS`
ORDER BY 
    `Fecha_venta` DESC";  // Ordenar por fecha en orden descendente

$result = mysqli_query($conn, $sql);

$c = 0;
$data = []; // Inicializar el array

while ($fila = $result->fetch_assoc()) {
    $data[$c]["Turno"] = $fila["Turno"];
    $data[$c]["Folio_Ticket"] = $fila["Folio_Ticket"];
    $data[$c]["Cod_Barra"] = $fila["Cod_Barra"];
    $data[$c]["Nombre_Prod"] = $fila["Nombre_Prod"];
    $data[$c]["Cantidad_Venta"] = $fila["Cantidad_Venta"];
    $data[$c]["Fk_sucursal"] = $fila["Fk_sucursal"];
    $data[$c]["Total_Venta"] = $fila["Total_Venta"];
    $data[$c]["Importe"] = $fila["Importe"];
    $data[$c]["Total_VentaG"] = $fila["Total_VentaG"];
    $data[$c]["DescuentoAplicado"] = $fila["DescuentoAplicado"];
    $data[$c]["FormaDePago"] = $fila["FormaDePago"];
    $data[$c]["CantidadPago"] = $fila["CantidadPago"];
    $data[$c]["Cambio"] = $fila["Cambio"];
    $data[$c]["Cliente"] = $fila["Cliente"];
    $data[$c]["Fecha_venta"] = $fila["Fecha_venta"];
    $data[$c]["AgregadoPor"] = $fila["AgregadoPor"];
    $data[$c]["AgregadoEl"] = $fila["AgregadoEl"];
    $data[$c]["Pagos_tarjeta"] = $fila["Pagos_tarjeta"];
    $c++; 
}

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);

// Cerrar conexión
$conn->close();
?>
