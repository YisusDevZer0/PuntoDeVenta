<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

function fechaCastellano($fecha) {
    $fecha = substr($fecha, 0, 10);
    $numeroDia = date('d', strtotime($fecha));
    $dia = date('l', strtotime($fecha));
    $mes = date('F', strtotime($fecha));
    $anio = date('Y', strtotime($fecha));
    $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
    $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $nombredia = str_replace($dias_EN, $dias_ES, $dia);
    $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    return $nombredia . " " . $numeroDia . " de " . $nombreMes . " de " . $anio;
}

$sql = "SELECT
Ventas_POS.Folio_Ticket,
Ventas_POS.FolioSucursal,
Ventas_POS.Fk_Caja,
Ventas_POS.Venta_POS_ID,
Ventas_POS.Identificador_tipo,
Ventas_POS.Cod_Barra,
Ventas_POS.Clave_adicional,
Ventas_POS.Nombre_Prod,
Ventas_POS.Cantidad_Venta,
Ventas_POS.Fk_sucursal,
Ventas_POS.AgregadoPor,
Ventas_POS.AgregadoEl,
Ventas_POS.Total_Venta,
Ventas_POS.Lote,
Ventas_POS.ID_H_O_D,
Sucursales.ID_Sucursal,
Sucursales.Nombre_Sucursal
FROM
Ventas_POS
JOIN
Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal
WHERE
MONTH(Ventas_POS.AgregadoEl) = MONTH(CURRENT_DATE) 
AND YEAR(Ventas_POS.AgregadoEl) = YEAR(CURRENT_DATE)
GROUP BY
Ventas_POS.Folio_Ticket,
Ventas_POS.FolioSucursal
ORDER BY
Ventas_POS.AgregadoEl DESC;";  // Ordena por fecha y hora más reciente

$result = mysqli_query($conn, $sql);

$c = 0;

$data = []; // Inicializa el array para evitar errores si no hay resultados

while ($fila = $result->fetch_assoc()) {
    $data[$c]["NumberTicket"] = $fila["Folio_Ticket"];
    $data[$c]["FolioSucursal"] = $fila["FolioSucursal"];
    $data[$c]["Fecha"] = fechaCastellano($fila["AgregadoEl"]);
    $data[$c]["Hora"] = date("g:i:s a", strtotime($fila["AgregadoEl"]));
    $data[$c]["Vendedor"] = $fila["AgregadoPor"];
    $data[$c]["Desglose"] = '
    <td>
    <a data-id="' . $fila["Folio_Ticket"] . '-' . $fila["FolioSucursal"] . '" class="btn btn-success btn-sm btn-desglose dropdown-item" style="background-color: #ef7980!important; color:white"><i class="fas fa-receipt"></i></a>
    </td>';
    $data[$c]["Reimpresion"] = '
    <td>
    <a data-id="' . $fila["Folio_Ticket"] . '" class=" btn-primary btn-sm btn-Reimpresion" style="background-color: #ef7980 !important; color:white"><i class="fas fa-print"></i></a>
    </td>';
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
