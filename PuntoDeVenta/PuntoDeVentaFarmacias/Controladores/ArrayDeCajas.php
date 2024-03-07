
<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "Controladores/ControladorUsuario.php";



$sql = "SELECT Cajas.ID_Caja, Cajas.Cantidad_Fondo, Cajas.Empleado, Cajas.Sucursal,
 Cajas.Estatus, Cajas.CodigoEstatus, Cajas.Turno, Cajas.Asignacion, Cajas.Fecha_Apertura,
  Cajas.Valor_Total_Caja, Cajas.Licencia, Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
  FROM Cajas, Sucursales WHERE Cajas.Sucursal = Sucursales.ID_Sucursal
AND Cajas.Fecha_Apertura = '$fechaActual'  -- Usa la variable de fecha
AND Cajas.Sucursal='".$row['Fk_Sucursal']."'
AND Cajas.Empleado='".$row['Nombre_Apellidos']."'
AND Cajas.Licencia='".$row['Licencia']."'";
 
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
