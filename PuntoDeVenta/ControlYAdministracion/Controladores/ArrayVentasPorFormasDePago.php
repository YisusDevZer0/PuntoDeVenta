<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

$sql = "SELECT 
    V.FormaDePago AS Metodo_Pago,
    SU.Nombre_Sucursal AS Sucursal,
    DATE(V.Fecha_venta) AS Fecha_Venta,
    V.Turno AS Turno,
    SUM(V.Importe) AS Total_Importe
FROM 
    Ventas_POS V
    LEFT JOIN Sucursales SU ON V.Fk_sucursal = SU.ID_Sucursal
GROUP BY 
    V.FormaDePago, SU.Nombre_Sucursal, DATE(V.Fecha_venta), V.Turno
ORDER BY 
    Total_Importe DESC;
";

$result = mysqli_query($conn, $sql);

$c = 0;

while ($fila = $result->fetch_assoc()) {
 
    $data[$c]["Fecha"] = date("d/m/Y", $fila["Fecha_venta"]);
    $data[$c]["Sucursal"] = $fila["Sucursal"];
    $data[$c]["Metodopago"] = $fila["Metodo_Pago"];
    $data[$c]["Turno"] = $fila["Turno"];
    $data[$c]["Total"] = $fila["Total_Importe"];
    $c++; 
}

$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];

echo json_encode($results);
?>
