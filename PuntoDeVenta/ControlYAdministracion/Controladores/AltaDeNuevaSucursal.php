<?php
    include_once 'db_connect.php';

    $Nombre_Sucursal = $_POST['NombreSucursal'];
    $Direccion = $_POST['Direccion'];
    $Telefono= $_POST['Telefono'];
    $Pin_Equipo= $_POST['PinEquipo'];
    $Creado = $_POST['agrego'];
    $Licencia = $_POST['licencia'];
  
//include database configuration file
    
    $sql = "SELECT Nombre_Sucursal,Direccion,Creado FROM Sucursales WHERE Nombre_Sucursal='$Nombre_Sucursal' AND Direccion='$Direccion'";
    $resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
    $row = mysqli_fetch_assoc($resultset);	
        //include database configuration file
        if($row['Nombre_Sucursal']==$Nombre_Sucursal  AND $row['Direccion']==$Direccion){				
            echo json_encode(array("statusCode"=>250));
          
        } 
        else{
            $sql = "INSERT INTO `Sucursales`( `Nombre_Sucursal`,`Direccion`,`Licencia`,`Telefono`,`Pin_Equipo`,`Creado`) 
            VALUES ('$Nombre_Sucursal','$Direccion','$Licencia','$Telefono','$Pin_Equipo','$Creado's)";
        
            if (mysqli_query($conn, $sql)) {
                echo json_encode(array("statusCode"=>200));
            } 
            else {
                echo json_encode(array("statusCode"=>201));
            }
            mysqli_close($conn);
        }

?>