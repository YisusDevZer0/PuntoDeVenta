<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

$sql = "SELECT 
    DATE(V.Fecha_venta) AS Fecha_Venta,
    SU.Nombre_Sucursal AS Sucursal,
    V.Turno AS Turno,
    SUM(V.Importe) AS Total_Venta
FROM 
    Ventas_POS V
    LEFT JOIN Sucursales SU ON V.Fk_sucursal = SU.ID_Sucursal
GROUP BY 
    DATE(V.Fecha_venta), SU.Nombre_Sucursal, V.Turno
ORDER BY 
    Fecha_Venta ASC, V.Turno ASC;
;


";

$result = mysqli_query($conn, $sql);

$c = 0;

while ($fila = $result->fetch_assoc()) {
 
    $data[$c]["Fecha"] = date("d/m/Y", $fila["Fecha_venta"]);
    $data[$c]["Sucursal"] = $fila["Sucursal"];
    $data[$c]["Total"] = $fila["Total_Venta"];
    $data[$c]["Turno"] = $fila["Turno"];
    $c++; 
}

$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];

echo json_encode($results);
?>
