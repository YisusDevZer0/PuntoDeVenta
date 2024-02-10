<?php
    include_once 'db_connect.php';

    $TipoUsuario = $_POST['tipoUsuario'];
    $Licencia = $_POST['licencia'];
    $Creado = $_POST['agrego'];
  
//include database configuration file
    
    $sql = "SELECT TipoUsuario,Licencia,Creado FROM Tipos_Usuarios WHERE TipoUsuario='$TipoUsuario' AND Licencia='$Licencia'";
    $resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
    $row = mysqli_fetch_assoc($resultset);	
        //include database configuration file
        if($row['TipoUsuario']==$TipoUsuario  AND $row['Licencia']==$Licencia){				
            echo json_encode(array("statusCode"=>250));
          
        } 
        else{
            $sql = "INSERT INTO `Tipos_Usuarios`( `TipoUsuario`,`Licencia`,`Creado`) 
            VALUES ('$TipoUsuario','$Licencia','$Creado')";
        
            if (mysqli_query($conn, $sql)) {
                echo json_encode(array("statusCode"=>200));
            } 
            else {
                echo json_encode(array("statusCode"=>201));
            }
            mysqli_close($conn);
        }

?>