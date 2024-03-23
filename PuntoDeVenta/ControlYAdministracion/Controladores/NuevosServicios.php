<?php
    include_once 'db_connect.php';

    $Servicio = $_POST['NombreServicio'];
    $Estado = $_POST['estado'];
    $Creado = $_POST['agrego'];
    $Sistema = $_POST['Sistema'];
    $Licencia = $_POST['licencia'];
    
  
//include database configuration file
    
    $sql = "SELECT Nom_Serv,Licencia,Creado FROM Servicios_POS WHERE Nom_Serv='$Servicio' AND Licencia='$Licencia'";
    $resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
    $row = mysqli_fetch_assoc($resultset);	
        //include database configuration file
        if($row['Nom_Serv']==$Servicio  AND $row['Licencia']==$Licencia){				
            echo json_encode(array("statusCode"=>250));
          
        } 
        else{
            $sql = "INSERT INTO `Servicios_POS`( Nom_Serv`, `Estado`, `Agregado_Por`,  `Sistema`, `Licencia`) 
            VALUES ('$Servicio','$Estado','$Creado','$Sistema','$Licencia')";
        
            if (mysqli_query($conn, $sql)) {
                echo json_encode(array("statusCode"=>200));
            } 
            else {
                echo json_encode(array("statusCode"=>201));
            }
            mysqli_close($conn);
        }

?>