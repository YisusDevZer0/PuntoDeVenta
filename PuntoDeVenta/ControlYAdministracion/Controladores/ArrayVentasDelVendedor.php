<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

$sql = "SELECT 
    V.AgregadoPor AS Vendedor,
    S.Nom_Serv AS Servicio,
    IFNULL(SUM(V.Importe), 0) AS Total_Vendido,
    DATE(V.Fecha_venta) AS Fecha_Venta 
FROM 
    Ventas_POS V
    LEFT JOIN Servicios_POS S ON V.Identificador_tipo = S.Servicio_ID
    LEFT JOIN Sucursales SU ON V.Fk_sucursal = SU.ID_Sucursal 
    LEFT JOIN Cajas C ON C.ID_Caja = V.Fk_Caja
    LEFT JOIN Stock_POS ST ON ST.ID_Prod_POS = V.ID_Prod_POS
WHERE 
    YEAR(V.Fecha_venta) = YEAR(CURDATE()) 
    AND MONTH(V.Fecha_venta) = MONTH(CURDATE()) 
GROUP BY 
    V.AgregadoPor,
    S.Nom_Serv,
    DATE(V.Fecha_venta)  
ORDER BY `Fecha_Venta` ASC";

$result = mysqli_query($conn, $sql);

$c = 0;

while ($fila = $result->fetch_assoc()) {
    $data[$c]["Cod_Barra"] = $fila["Vendedor"];
    $data[$c]["Nombre_Prod"] = $fila["Servicio"];
    $data[$c]["PrecioCompra"] = $fila["Total_Vendido"];
    $data[$c]["PrecioVenta"] = $fila["Fecha_Venta"];
   

    $c++; 
}

$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];

echo json_encode($results);
?>
