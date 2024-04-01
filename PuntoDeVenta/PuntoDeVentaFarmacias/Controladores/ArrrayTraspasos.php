<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

$sql = "SELECT 
Traspasos_generados.ID_Traspaso_Generado,
Traspasos_generados.Folio_Prod_Stock,
Traspasos_generados.TraspasoRecibidoPor,
Traspasos_generados.TraspasoGeneradoPor,
Traspasos_generados.Num_Orden,
Traspasos_generados.Num_Factura,
Traspasos_generados.TotaldePiezas,
Traspasos_generados.Cod_Barra,
Traspasos_generados.Nombre_Prod,
Traspasos_generados.Fk_sucursal,
Traspasos_generados.Fk_Sucursal_Destino,
Traspasos_generados.Proveedor1,
Traspasos_generados.Proveedor2,
Traspasos_generados.Precio_Venta,
Traspasos_generados.Precio_Compra,
Traspasos_generados.Total_traspaso,
Traspasos_generados.TotalVenta,
Traspasos_generados.Existencias_R,
Traspasos_generados.ProveedorFijo,
Traspasos_generados.Cantidad_Enviada,
Traspasos_generados.Existencias_D_envio,
Traspasos_generados.FechaEntrega,
Traspasos_generados.Estatus,
Traspasos_generados.ID_H_O_D,
Sucursales.ID_Sucursal,
Sucursales.Nombre_Sucursal 
FROM 
Traspasos_generados,
Sucursales
WHERE 
Traspasos_generados.Fk_sucursal = Sucursales.ID_Sucursal 
AND (
    (MONTH(Traspasos_generados.FechaEntrega) = MONTH(CURDATE()) AND YEAR(Traspasos_generados.FechaEntrega) = YEAR(CURDATE()))
    OR
    (MONTH(Traspasos_generados.FechaEntrega) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(Traspasos_generados.FechaEntrega) = YEAR(CURDATE() - INTERVAL 1 MONTH))
)"; 

$result = mysqli_query($conn, $sql);

$data = [];

if ($result && mysqli_num_rows($result) > 0) {
    $c = 0;

    while ($fila = $result->fetch_assoc()) {
        $data[$c]["IDTraspasoGenerado"] = $fila["ID_Traspaso_Generado"];
        $data[$c]["Cod_Barra"] = $fila["Cod_Barra"];
        $data[$c]["NumOrden"] = $fila["Num_Orden"];
        $data[$c]["Nombre_Prod"] = $fila["Nombre_Prod"];
        $data[$c]["ProveedorTraspaso"] = $fila["ProveedorFijo"];
        $data[$c]["Fk_sucursal"] = $fila["Nombre_Sucursal"];
        $data[$c]["Destino"] = $fila["Fk_Sucursal_Destino"];
        $data[$c]["Cantidad"] = $fila["Cantidad_Enviada"];
        $data[$c]["FechaEntrega"] = fechaCastellano($fila["FechaEntrega"]);
        $data[$c]["Estatus"] = fechaCastellano($fila["Estatus"]);
        $data[$c]["Traspasocorrecto"] = ["<a href=https://saludapos.com/POS2/TraspasoOKV2?traspasoid=" . base64_encode($fila["ID_Traspaso_Generado"]) . " type='button' class='btn btn-success  btn-sm '><i class='fas fa-check'></i></a> "];

        $c++;
    }
}

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);
?>
<?php

function fechaCastellano ($fecha) {
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
  return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
}
?>