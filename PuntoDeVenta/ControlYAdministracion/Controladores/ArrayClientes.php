
<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";



$sql = "SELECT dp.ID_Data_Paciente, 
dp.Nombre_Paciente, 
dp.Edad, 
dp.Sexo, 
dp.Telefono, 
dp.Fecha_Nacimiento, 
dp.Fk_Sucursal, 
dp.SucursalVisita,
s.ID_Sucursal, 
s.Nombre_Sucursal
FROM Data_Pacientes dp
INNER JOIN Sucursales s ON dp.Fk_Sucursal = s.ID_Sucursal
ORDER BY dp.ID_Data_Paciente DESC;";
 
$result = mysqli_query($conn, $sql);
 
$c=0;
 
while($fila=$result->fetch_assoc()){
 
 $data[$c]["Folio"] = $fila["ID_Data_Paciente"];
 $data[$c]["Nombre_Paciente"] = $fila["Nombre_Paciente"];
 $data[$c]["Sucursalderegistro"] = $fila["Nombre_Sucursal"];
 $data[$c]["SucursalVisita"] = $fila["SucursalVisita"];
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
