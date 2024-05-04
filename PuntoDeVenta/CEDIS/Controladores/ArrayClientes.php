
<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";



$sql = "SELECT ID_Data_Paciente,Nombre_Paciente,Edad,Sexo,Telefono,Fecha_Nacimiento
FROM Data_Pacientes  ORDER BY ID_Data_Paciente DESC";
 
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
