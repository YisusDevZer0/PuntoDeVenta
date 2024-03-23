<?php
    include_once 'db_connect.php';

    $Nom_Tipo_Prod = mysqli_real_escape_string($conn, $_POST['NomMarca']);
    $Agregado_Por = mysqli_real_escape_string($conn, $_POST['agregoPor']);
    $Sistema = mysqli_real_escape_string($conn, $_POST['sistema']);
    $Licencia = mysqli_real_escape_string($conn, $_POST['licencia']);
  
    // Consulta para verificar si ya existe un registro con los mismos valores
    $sql = "SELECT Nom_Tipo_Prod, Licencia FROM Tabla_Ejemplo WHERE Nom_Tipo_Prod='$Nom_Tipo_Prod' AND Licencia='$Licencia'";
    $resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
    $row = mysqli_fetch_assoc($resultset);
    
    if(mysqli_num_rows($resultset) > 0) {
        echo json_encode(array("statusCode"=>250)); // El registro ya existe
    } else {
        // Consulta de inserción para agregar un nuevo registro
        $sql = "INSERT INTO `Tabla_Ejemplo`(`Nom_Tipo_Prod`, `Agregado_Por`, `Sistema`, `Licencia`) 
                VALUES ('$Nom_Tipo_Prod', '$Agregado_Por', '$Sistema', '$Licencia')";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(array("statusCode"=>200)); // Inserción exitosa
        } else {
            echo json_encode(array("statusCode"=>201)); // Error en la inserción
        }
        mysqli_close($conn);
    }
?>
