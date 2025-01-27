
<?php
include "../Controladores/db_connect.php"; // Conexión a la base de datos

// Validación y limpieza de datos
$Folio_Prod_Stock = isset($_POST['Id_Serv']) ? intval($_POST['Id_Serv']) : 0;
$Max_Existencia = isset($_POST['ActNomServ']) ? intval($_POST['ActNomServ']) : 0;
$Min_Existencia = isset($_POST['ActUsuarioCServ']) ? intval($_POST['ActUsuarioCServ']) : 0;
$ActualizadoPor = isset($_POST['ActUsuarioCServ']) ? trim($_POST['ActUsuarioCServ']) : '';
$Sistema = isset($_POST['ActSistemaCServ']) ? trim($_POST['ActSistemaCServ']) : '';

// Verificar si todos los datos requeridos están presentes
if ($Folio_Prod_Stock && $Max_Existencia && $Min_Existencia && $ActualizadoPor && $Sistema) {
    // Consulta preparada para actualizar el registro
    $sql = "UPDATE `Stock_POS` SET `Max_Existencia`=?, `Min_Existencia`=?, `ActualizadoPor`=?, `Sistema`=? WHERE `Folio_Prod_Stock`=?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Enlazar parámetros
        $stmt->bind_param("iissi", $Max_Existencia, $Min_Existencia, $ActualizadoPor, $Sistema, $Folio_Prod_Stock);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            echo json_encode(array("statusCode" => 200, "message" => "Actualización exitosa"));
        } else {
            echo json_encode(array("statusCode" => 201, "error" => "Error al ejecutar la consulta: " . $stmt->error));
        }
        
        // Cerrar la declaración
        $stmt->close();
    } else {
        echo json_encode(array("statusCode" => 201, "error" => "Error en la preparación de la consulta: " . $conn->error));
    }
} else {
    echo json_encode(array("statusCode" => 201, "error" => "Faltan datos requeridos."));
}

// Cerrar conexión
mysqli_close($conn);
?>
