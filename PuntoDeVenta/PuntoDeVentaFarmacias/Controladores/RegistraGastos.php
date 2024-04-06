<?php
    include_once 'db_connect.php';

    $NomGasto = mysqli_real_escape_string($conn, $_POST['Nom_Gasto']);
    $Estado = mysqli_real_escape_string($conn, $_POST['Estado']);
    $AgregadoPor = mysqli_real_escape_string($conn, $_POST['Agrego']);
    $Sistema = mysqli_real_escape_string($conn, $_POST['Sistema']);
    $Licencia = mysqli_real_escape_string($conn, $_POST['Licencia']);
  
    // Consulta para verificar si ya existe un registro con los mismos valores
    $sql = "SELECT Nom_Gasto, Licencia FROM GastosPOS WHERE Nom_Gasto='$NomGasto' AND Licencia='$Licencia'";
    $resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
    $row = mysqli_fetch_assoc($resultset);
    
    if(mysqli_num_rows($resultset) > 0) {
        echo json_encode(array("statusCode"=>250)); // El registro ya existe
    } else {
        // Consulta de inserción para agregar un nuevo registro
        $sql = "INSERT INTO `GastosPOS`(`Nom_Gasto`, `Estado`, `Agregado_Por`,  `Sistema`, `Licencia`) 
                VALUES ('$NomGasto', '$Estado', '$AgregadoPor',  '$Sistema', '$Licencia')";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(array("statusCode"=>200)); // Inserción exitosa
        } else {
            echo json_encode(array("statusCode"=>201)); // Error en la inserción
        }
        mysqli_close($conn);
    }
?>
