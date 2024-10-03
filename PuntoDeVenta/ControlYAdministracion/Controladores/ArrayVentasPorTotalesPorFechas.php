<?php
header('Content-Type: application/json');
include("db_connect.php");
include("ControladorUsuario.php");

// Obtener las variables enviadas
$mes = $_POST['Mes'];  // Asume que estas variables vienen en una petición POST
$anual = $_POST['anual'];
$sucursal = $_POST['Sucursal'];

// Crear las fechas de inicio y fin basadas en las variables enviadas
$fechaInicio = $anual . '-' . $mes . '-01';  // Formato YYYY-MM-01
$fechaFin = date("Y-m-t", strtotime($fechaInicio));  // Último día del mes

// Debug: Verificar fechas
var_dump($fechaInicio, $fechaFin); 

$sql = "SELECT 
    Ventas_POS.Turno, 
    Ventas_POS.Folio_Ticket, 
    Ventas_POS.Cod_Barra, 
    Ventas_POS.Nombre_Prod, 
    Ventas_POS.Cantidad_Venta, 
    Ventas_POS.Fk_sucursal, 
    Ventas_POS.Total_Venta, 
    Ventas_POS.Importe, 
    Ventas_POS.Total_VentaG, 
    Ventas_POS.DescuentoAplicado, 
    Ventas_POS.FormaDePago, 
    Ventas_POS.CantidadPago, 
    Ventas_POS.Cambio, 
    Ventas_POS.Cliente, 
    Ventas_POS.Fecha_venta, 
    Ventas_POS.AgregadoPor, 
    Ventas_POS.AgregadoEl, 
    Ventas_POS.Pagos_tarjeta,
    
    CASE 
        WHEN Ventas_POS.FormaDePago = 'Efectivo' THEN Ventas_POS.Importe 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Importe - Ventas_POS.Pagos_tarjeta 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Importe
        ELSE 0 
    END AS PagoEfectivo,
    
    CASE 
        WHEN Ventas_POS.FormaDePago = 'Tarjeta' THEN Ventas_POS.Importe 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Tarjeta' THEN Ventas_POS.Pagos_tarjeta 
        ELSE 0 
    END AS PagoTarjeta,
    
    CASE 
        WHEN Ventas_POS.FormaDePago = 'Crédito' THEN Ventas_POS.Importe 
        WHEN Ventas_POS.FormaDePago = 'Efectivo y Crédito' THEN Ventas_POS.Importe 
        ELSE 0 
    END AS PagoCredito,

    Servicios_POS.Servicio_ID,
    Servicios_POS.Nom_Serv,
    
    Sucursales.ID_Sucursal,
    Sucursales.Nombre_Sucursal
FROM 
    Ventas_POS
    LEFT JOIN Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID  
    LEFT JOIN Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal
WHERE 
    Ventas_POS.Fecha_venta BETWEEN '$fechaInicio' AND '$fechaFin' AND
    Ventas_POS.Fk_sucursal = $sucursal
ORDER BY 
    Ventas_POS.Fecha_venta DESC";  // Ordenar por fecha en orden descendente

$result = mysqli_query($conn, $sql);

// Validar si hubo error en la consulta
if ($result === false) {
    die("Error en la consulta: " . mysqli_error($conn));
}

// Validar si se encontraron resultados
if (mysqli_num_rows($result) == 0) {
    die("No se encontraron resultados.");
}

$c = 0;
$data = []; // Inicializar el array

while ($fila = $result->fetch_assoc()) {
    $data[$c]["Turno"] = $fila["Turno"];
    $data[$c]["Folio_Ticket"] = $fila["Folio_Ticket"];
    $data[$c]["Cod_Barra"] = $fila["Cod_Barra"];
    $data[$c]["Nombre_Prod"] = $fila["Nombre_Prod"];
    $data[$c]["Cantidad_Venta"] = $fila["Cantidad_Venta"];
    $data[$c]["Nombre_Sucursal"] = $fila["Nombre_Sucursal"];  // Agregar Nombre de la Sucursal
    $data[$c]["Total_Venta"] = $fila["Total_Venta"];
    $data[$c]["Importe"] = $fila["Importe"];
    $data[$c]["Importetarjeta"] = $fila["PagoTarjeta"];
    $data[$c]["Importecredito"] = $fila["PagoCredito"];
    $data[$c]["Total_VentaG"] = $fila["Total_VentaG"];
    $data[$c]["DescuentoAplicado"] = $fila["DescuentoAplicado"];
    $data[$c]["FormaDePago"] = $fila["FormaDePago"];
    $data[$c]["Cambio"] = $fila["Cambio"];
    $data[$c]["Cliente"] = $fila["Cliente"];
    $data[$c]["Fecha_venta"] = $fila["Fecha_venta"];
    $data[$c]["AgregadoPor"] = $fila["AgregadoPor"];
    $data[$c]["AgregadoEl"] = $fila["AgregadoEl"];
  
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
