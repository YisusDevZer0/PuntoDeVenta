<?php
header('Content-Type: application/json');
include("db_connect.php");
include("ControladorUsuario.php");

// Obtener el año y el mes actual
$currentYear = date("Y");
$currentMonth = date("m");

$sql = "SELECT 
    `Venta_POS_ID`, 
    `ID_Prod_POS`, 
    `Identificador_tipo`, 
    `Turno`, 
    `FolioSucursal`, 
    `Folio_Ticket`, 
    `Folio_Ticket_Aleatorio`, 
    `Clave_adicional`, 
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
    `Fk_Caja`, 
    `Lote`, 
    `Motivo_Cancelacion`, 
    `Estatus`, 
    `Sistema`, 
    `AgregadoPor`, 
    `AgregadoEl`, 
    `ID_H_O_D`, 
    `FolioSignoVital`, 
    `TicketAnterior`,
    `Pagos_tarjeta`, 
    CASE 
        WHEN `Pagos_tarjeta` = 'Efectivo y Tarjeta' THEN `CantidadPago`
        ELSE 0 
    END AS `Complemento_Tarjeta`,
    CASE 
        WHEN `Pagos_tarjeta` = 'Efectivo y Credito' THEN `CantidadPago`
        ELSE 0 
    END AS `Complemento_Credito`
FROM 
    `Ventas_POS`
WHERE 
    MONTH(`Fecha_venta`) = $currentMonth
    AND YEAR(`Fecha_venta`) = $currentYear
ORDER BY 
    `Fecha_venta`;  -- Ordenar por fecha";

$result = mysqli_query($conn, $sql);

$c = 0;
$data = []; // Asegúrate de inicializar el array

while ($fila = $result->fetch_assoc()) {
   
    $data[$c]["Turno"] = $fila["Turno"];
    $data[$c]["FolioSucursal"] = $fila["FolioSucursal"];
    $data[$c]["Folio_Ticket"] = $fila["Folio_Ticket"];
    $data[$c]["Folio_Ticket_Aleatorio"] = $fila["Folio_Ticket_Aleatorio"];
    $data[$c]["Clave_adicional"] = $fila["Clave_adicional"];
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
    $data[$c]["Fk_Caja"] = $fila["Fk_Caja"];
    $data[$c]["Lote"] = $fila["Lote"];
    $data[$c]["Motivo_Cancelacion"] = $fila["Motivo_Cancelacion"];
    $data[$c]["Estatus"] = $fila["Estatus"];
    $data[$c]["Sistema"] = $fila["Sistema"];
    $data[$c]["AgregadoPor"] = $fila["AgregadoPor"];
    $data[$c]["AgregadoEl"] = $fila["AgregadoEl"];
    $data[$c]["ID_H_O_D"] = $fila["ID_H_O_D"];
    $data[$c]["FolioSignoVital"] = $fila["FolioSignoVital"];
    $data[$c]["TicketAnterior"] = $fila["TicketAnterior"];
    $data[$c]["Pagos_tarjeta"] = $fila["Pagos_tarjeta"];
    $data[$c]["Complemento_Tarjeta"] = $fila["Complemento_Tarjeta"];
    $data[$c]["Complemento_Credito"] = $fila["Complemento_Credito"];
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
