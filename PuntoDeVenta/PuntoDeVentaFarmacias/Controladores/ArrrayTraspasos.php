<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Escapar la variable para evitar inyección SQL
$fk_sucursal = mysqli_real_escape_string($conn, $row['Fk_Sucursal']);

// Modificar la consulta para usar la variable
$sql = "SELECT 
    Traspasos_generados.ID_Traspaso_Generado,
    Traspasos_generados.TraspasoRecibidoPor,
    Traspasos_generados.TraspasoGeneradoPor,
    Traspasos_generados.Num_Orden,
    Traspasos_generados.Num_Factura,
    Traspasos_generados.TotaldePiezas,
    Traspasos_generados.Cod_Barra,
    Traspasos_generados.Nombre_Prod,
    Traspasos_generados.Fk_SucDestino,
    Traspasos_generados.Precio_Venta,
    Traspasos_generados.Precio_Compra,
    Traspasos_generados.Cantidad_Enviada,
    Traspasos_generados.FechaEntrega,
    Traspasos_generados.Estatus,
    Traspasos_generados.ID_H_O_D,
    Sucursales.ID_Sucursal,
    Sucursales.Nombre_Sucursal
FROM 
    Traspasos_generados,
    Sucursales
WHERE 
    Traspasos_generados.Fk_SucDestino = Sucursales.ID_Sucursal
    AND Traspasos_generados.Fk_SucDestino = '$fk_sucursal' -- Usar la variable en la consulta
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
        $data[$c]["Cantidad"] = $fila["Cantidad_Enviada"];
        $data[$c]["FechaEntrega"] = fechaCastellano($fila["FechaEntrega"]);
        $data[$c]["Estatus"] = fechaCastellano($fila["Estatus"]);
        $data[$c]["Traspasocorrecto"] = ["<a data-id='$fila[ID_Traspaso_Generado]' class='btn btn-success btn-sm btn-AceptarTraspaso'><i class='fa-solid fa-circle-check'></i></a>"];
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
