
<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "Controladores/ControladorUsuario.php";



$sql = "SELECT 
Cajas_POS.ID_Caja,
Cajas_POS.Cantidad_Fondo,
Cajas_POS.Empleado,
Cajas_POS.Sucursal,
Cajas_POS.Estatus,
Cajas_POS.CodigoEstatus,
Cajas_POS.Turno,
Cajas_POS.Asignacion,
Cajas_POS.Fecha_Apertura,
Cajas_POS.Valor_Total_Caja,
Cajas_POS.ID_H_O_D,
Sucursales.ID_SucursalC,
Sucursales.Nombre_Sucursal 
FROM 
Cajas_POS, SucursalesCorre 
WHERE 
Cajas_POS.Sucursal = Sucursales.ID_SucursalC 
AND Cajas_POS.Fecha_Apertura = '$fechaActual'  -- Usa la variable de fecha
AND Cajas_POS.Sucursal='".$row['Fk_Sucursal']."'
AND Cajas_POS.Empleado='".$row['Nombre_Apellidos']."'
AND Cajas_POS.ID_H_O_D='".$row['Licencia']."'";
 
$result = mysqli_query($conn, $sql);
 
$c=0;
 
while($fila=$result->fetch_assoc()){
 
 $data[$c]["Folio"] = $fila["ID_Data_Paciente"];
 $data[$c]["Nombre_Paciente"] = $fila["Nombre_Paciente"];
$data[$c]["Edad"] = $fila["Edad"];
 $data[$c]["Sexo"] = $fila["Sexo"];
 $data[$c]["Telefono"] = $fila["Telefono"];
 $data[$c]["Fecha_Nacimiento"] = $fila["Fecha_Nacimiento"];
   

    
    
    $c++; 
 
}
 
$results = ["sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data ];
 
echo json_encode($results);
?>
