<?php
    include_once 'db_connection.php';

$Fk_Sucursal=  $conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Sucursal']))));
$Fondo_Caja=  $conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Cantidad']))));
$AgregadoPor=  $conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Agrega']))));
$Sistema=  $conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Sistema']))));
$Estatus=  $conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['EstadoAsignacion']))));
$CodigoEstatus=  $conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Vigencia']))));
$ID_H_O_D=  $conn -> real_escape_string(htmlentities(strip_tags(Trim($_POST['Empresa']))));    
//include database configuration file
    
    $sql = "SELECT Fk_Sucursal,Fondo_Caja,ID_H_O_D FROM Fondos_Cajas WHERE Fk_Sucursal='$Fk_Sucursal' 
    AND ID_H_O_D='$ID_H_O_D' AND Fondo_Caja='$Fondo_Caja'";
    $resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
    $row = mysqli_fetch_assoc($resultset);	
        //include database configuration file
        if($row['Fk_Sucursal']==$Fk_Sucursal AND $row['ID_H_O_D']==$ID_H_O_D  AND $row['Fondo_Caja']==$Fondo_Caja ){				
            echo json_encode(array("statusCode"=>250));
          
        } 
        else{
            $sql = "INSERT INTO `Fondos_Cajas`( `Fk_Sucursal`,`Fondo_Caja`,`Estatus`,`CodigoEstatus`,`AgregadoPor`,`Sistema`,`ID_H_O_D`) 
            VALUES ('$Fk_Sucursal','$Fondo_Caja','$Estatus','$CodigoEstatus','$AgregadoPor','$Sistema','$ID_H_O_D')";
        
            if (mysqli_query($conn, $sql)) {
                echo json_encode(array("statusCode"=>200));
            } 
            else {
                echo json_encode(array("statusCode"=>201));
            }
            mysqli_close($conn);
        }

?>