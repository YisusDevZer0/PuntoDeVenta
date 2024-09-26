<?php
header('Content-Type: application/json');
include("db_connect.php");
include ("ControladorUsuario.php");

// Obtener el año y el mes actual
$currentYear = date("Y");
$currentMonth = date("m");

$sql = "SELECT 
    DATE(V.Fecha_venta) AS Fecha_Venta,
    SU.Nombre_Sucursal AS Sucursal,
    V.Turno AS Turno,
    SUM(V.Importe) AS Total_Venta
FROM 
    Ventas_POS V
    LEFT JOIN Sucursales SU ON V.Fk_sucursal = SU.ID_Sucursal
WHERE 
    MONTH(V.Fecha_venta) = $currentMonth AND YEAR(V.Fecha_venta) = $currentYear
GROUP BY 
    DATE(V.Fecha_venta), SU.Nombre_Sucursal, V.Turno
ORDER BY 
    Fecha_Venta DESC, SU.Nombre_Sucursal ASC, V.Turno ASC;";

$result = mysqli_query($conn, $sql);

$c = 0;
$data = []; // Asegúrate de inicializar el array

while ($fila = $result->fetch_assoc()) {
    $data[$c]["Fecha"] = $fila["Fecha_Venta"];
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
