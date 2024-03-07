
<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "Controladores/ControladorUsuario.php";




$sql = "SELECT Cajas.ID_Caja, Cajas.Cantidad_Fondo, Cajas.Empleado, Cajas.Sucursal,
 Cajas.Estatus, Cajas.CodigoEstatus, Cajas.Turno, Cajas.Asignacion, Cajas.Fecha_Apertura,
  Cajas.Valor_Total_Caja, Cajas.Licencia, Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
  FROM Cajas, Sucursales WHERE Cajas.Sucursal = Sucursales.ID_Sucursal";
 
$result = mysqli_query($conn, $sql);
 
$c=0;
 
while($fila=$result->fetch_assoc()){
 
 $data[$c]["IdCaja"] = $fila["ID_Caja"];
 $data[$c]["Empleado"] = $fila["Empleado"];
$data[$c]["Cantidad_Fondo"] = $fila["Cantidad_Fondo"];
 $data[$c]["Fecha_Apertura"] = $fila["Fecha_Apertura"];
 $data[$c]["Estatus"] = $fila["Estatus"];
 $data[$c]["Turno"] = $fila["Turno"];
 $data[$c]["Asignacion"] = $fila["Asignacion"];
 $data[$c]["ValorTotalCaja"] = $fila["Valor_Total_Caja"];
    
    
    $c++; 
 
}
 
$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];
 
echo json_encode($results);
?>
