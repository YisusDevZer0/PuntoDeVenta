<?php
include_once 'db_connect.php';

// Verifica si todas las variables POST esperadas están configuradas
if(isset($_POST['NombreSucursal'], $_POST['Direccion'], $_POST['Telefono'], $_POST['PinEquipo'], $_POST['agrego'], $_POST['licencia'])) {
    
    // Asigna las variables POST a variables locales
    $Nombre_Sucursal = $_POST['NombreSucursal'];
    $Direccion = $_POST['Direccion'];
    $Telefono= $_POST['Telefono'];
    $Pin_Equipo= $_POST['PinEquipo'];
    $Creado = $_POST['agrego'];
    $Licencia = $_POST['licencia'];

    // Consulta SQL para verificar si ya existe una sucursal con el mismo nombre y dirección
    $sql = "SELECT Nombre_Sucursal, Direccion FROM Sucursales WHERE Nombre_Sucursal='$Nombre_Sucursal' AND Direccion='$Direccion'";
    $resultset = mysqli_query($conn, $sql);

    if (!$resultset) {
        // Si hay un error en la consulta SQL, muestra un mensaje de error
        echo json_encode(array("statusCode"=>500, "message"=>"Error en la consulta SQL: " . mysqli_error($conn)));
    } else {
        // Si la consulta se realiza correctamente
        $row = mysqli_fetch_assoc($resultset);
        if($row) {
            // Si se encontró una sucursal con el mismo nombre y dirección, devuelve un código de estado 250
            echo json_encode(array("statusCode"=>250, "message"=>"Ya existe una sucursal con el mismo nombre y dirección."));
        } else {
            // Si no se encontró una sucursal con el mismo nombre y dirección, inserta una nueva sucursal en la base de datos
            $sql = "INSERT INTO `Sucursales`(`Nombre_Sucursal`, `Direccion`, `Licencia`, `Telefono`, `Pin_Equipo`, `Creado`) 
                    VALUES ('$Nombre_Sucursal','$Direccion','$Licencia','$Telefono','$Pin_Equipo','$Creado')";

            if (mysqli_query($conn, $sql)) {
                // Si la inserción se realiza correctamente, devuelve un código de estado 200
                echo json_encode(array("statusCode"=>200, "message"=>"Sucursal insertada correctamente."));
            } else {
                // Si hay un error en la inserción, muestra un mensaje de error
                echo json_encode(array("statusCode"=>500, "message"=>"Error al insertar la sucursal: " . mysqli_error($conn)));
            }
        }
    }

} else {
    // Si no se recibieron todas las variables POST esperadas, muestra un mensaje de error
    echo json_encode(array("statusCode"=>400, "message"=>"No se recibieron todas las variables POST esperadas."));
}

// Cierra la conexión a la base de datos
mysqli_close($conn);
?>
