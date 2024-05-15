
<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";



$sql = "SELECT Stock_registrosNuevos.Folio_Ingreso,Stock_registrosNuevos.ID_Prod_POS,Stock_registrosNuevos.Fk_sucursal,Stock_registrosNuevos.Factura,Stock_registrosNuevos.Precio_compra,
Stock_registrosNuevos.Existencias_R,Stock_registrosNuevos.Total_Factura,Stock_registrosNuevos.ExistenciaPrev,Stock_registrosNuevos.Recibido,Stock_registrosNuevos.AgregadoPor,Stock_registrosNuevos.AgregadoEl, Sucursales.ID_Sucursal,Sucursales.Nombre_Sucursal,Productos_POS.ID_Prod_POS,Productos_POS.Cod_Barra,Productos_POS.Nombre_Prod FROM
Productos_POS,Sucursales,Stock_registrosNuevos WHERE Stock_registrosNuevos.Fk_sucursal = Sucursales.ID_Sucursal and 
Stock_registrosNuevos.ID_Prod_POS = Productos_POS.ID_Prod_POS;";
 
$result = mysqli_query($conn, $sql);
 
$c=0;
 
while($fila=$result->fetch_assoc()){
 
    $data[$c]["Folio_Ingreso"] = $fila["Folio_Ingreso"];
    $data[$c]["Factura"] = $fila["Factura"];
    $data[$c]["Cod_Barra"] = $fila["Cod_Barra"];
    $data[$c]["Nombre_Prod"] = $fila["Nombre_Prod"];
    $data[$c]["Precio_Compra"] = $fila["Precio_compra"];
    $data[$c]["TotalFactura"] = $fila["Total_Factura"];
    $data[$c]["Existencias_R"] = $fila["Existencias_R"];
    $data[$c]["ExistenciaPrev"] = $fila["ExistenciaPrev"];
    $data[$c]["Recibido"] = $fila["Recibido"];
    $data[$c]["Sucursal"] = $fila["Nombre_Sucursal"];
    $data[$c]["AgregadoPor"] = $fila["AgregadoPor"];
    $data[$c]["AgregadoEl"] = $fila["AgregadoEl"];
    $c++; 
 
}
 
$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];
 
echo json_encode($results);
?>
