<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

// Accede directamente a la variable Fk_Sucursal
$sucursal = $row['Fk_Sucursal']; // Asegúrate de que $row esté disponible y tenga el valor correcto

$sql = "SELECT DISTINCT
    Ventas_POS.Venta_POS_ID,
    Ventas_POS.Folio_Ticket,
    Ventas_POS.FolioSucursal,
    Ventas_POS.Fk_Caja,
    Ventas_POS.Identificador_tipo,
    Ventas_POS.Fecha_venta, 
    Ventas_POS.Total_Venta,
    Ventas_POS.Importe,
    Ventas_POS.Total_VentaG,
    Ventas_POS.FormaDePago,
    Ventas_POS.Turno,
    Ventas_POS.FolioSignoVital,
    Ventas_POS.Cliente,
    Cajas.ID_Caja,
    Cajas.Sucursal,
    Cajas.MedicoEnturno,
    Cajas.EnfermeroEnturno,
    Ventas_POS.Cod_Barra,
    Ventas_POS.Clave_adicional,
    Ventas_POS.Nombre_Prod,
    Ventas_POS.Cantidad_Venta,
    Ventas_POS.Fk_sucursal,
    Ventas_POS.AgregadoPor,
    Ventas_POS.AgregadoEl,
    Ventas_POS.Lote,
    Ventas_POS.ID_H_O_D,
    Sucursales.ID_Sucursal, 
    Sucursales.Nombre_Sucursal,
    Servicios_POS.Servicio_ID,
    Servicios_POS.Nom_Serv,
    Ventas_POS.DescuentoAplicado,
    Stock_POS.ID_Prod_POS,
    Stock_POS.Precio_Venta,
    Stock_POS.Precio_C
FROM 
    Ventas_POS
LEFT JOIN 
    Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
LEFT JOIN 
    Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
LEFT JOIN 
    Cajas ON Cajas.ID_Caja = Ventas_POS.Fk_Caja
LEFT JOIN 
    Stock_POS ON Stock_POS.ID_Prod_POS = Ventas_POS.ID_Prod_POS
WHERE 
    YEAR(Ventas_POS.Fecha_venta) = YEAR(CURDATE()) 
    AND MONTH(Ventas_POS.Fecha_venta) = MONTH(CURDATE())
    AND Ventas_POS.Fk_sucursal = '$sucursal'
    AND Ventas_POS.FormaDePago = 'Crédito';"; // Filtro por la forma de pago "Crédito"

$result = mysqli_query($conn, $sql);

$c = 0;

while ($fila = $result->fetch_assoc()) {
    $data[$c]["Cod_Barra"] = $fila["Cod_Barra"];
    $data[$c]["Nombre_Prod"] = $fila["Nombre_Prod"];
    $data[$c]["FolioTicket"] = $fila["FolioSucursal"] . '' . $fila["Folio_Ticket"];
    $data[$c]["Sucursal"] = $fila["Nombre_Sucursal"];
    $data[$c]["Turno"] = $fila["Turno"];
    $data[$c]["Cantidad_Venta"] = $fila["Cantidad_Venta"];
    $data[$c]["Importe"] = $fila["Importe"];
    $data[$c]["Total_Venta"] = $fila["Total_Venta"];
    $data[$c]["Descuento"] = $fila["DescuentoAplicado"];
    $data[$c]["FormaPago"] = $fila["FormaDePago"];
    $data[$c]["Cliente"] = $fila["Cliente"];
    $data[$c]["FolioSignoVital"] = $fila["FolioSignoVital"];
    $data[$c]["NomServ"] = $fila["Nom_Serv"];
    $data[$c]["AgregadoEl"] = date("d/m/Y", strtotime($fila["Fecha_venta"]));
    $data[$c]["AgregadoEnMomento"] = $fila["AgregadoEl"];
    $data[$c]["AgregadoPor"] = $fila["AgregadoPor"];

    $c++;
}

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);
?>
