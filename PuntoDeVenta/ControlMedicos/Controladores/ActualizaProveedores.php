<?php
include "db_connect.php"; // Asumiendo que este archivo contiene la conexión a la base de datos

// Validación y limpieza de datos
$ID_Proveedor = isset($_POST['Id_Serv']) ? intval($_POST['Id_Serv']) : 0;
$Nombre_Proveedor = isset($_POST['ActNomServ']) ? trim($_POST['ActNomServ']) : '';
$Numero_Contacto = isset($_POST['telefonoserv']) ? trim($_POST['telefonoserv']) : '';
$Correo_Electronico = isset($_POST['correoserv']) ? trim($_POST['correoserv']) : '';
$Clave_Proveedor = isset($_POST['clavserv']) ? trim($_POST['clavserv']) : '';
$ActualizadoPor = isset($_POST['ActUsuarioCServ']) ? trim($_POST['ActUsuarioCServ']) : '';

// Verificar si todos los datos requeridos están presentes
if ($ID_Proveedor && $Nombre_Proveedor && $Numero_Contacto && $Correo_Electronico && $Clave_Proveedor && $ActualizadoPor) {
    // Consulta preparada
    $sql = "UPDATE `Proveedores` SET `Nombre_Proveedor`=?, `Numero_Contacto`=?, `Correo_Electronico`=?, `Clave_Proveedor`=?, `ActualizadoPor`=? WHERE `ID_Proveedor`=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Enlazar parámetros
        $stmt->bind_param("sssssi", $Nombre_Proveedor, $Numero_Contacto, $Correo_Electronico, $Clave_Proveedor, $ActualizadoPor, $ID_Proveedor);
        // Ejecutar consulta
        if ($stmt->execute()) {
            echo json_encode(array("statusCode" => 200));
        } else {
            echo json_encode(array("statusCode" => 201, "error" => "Error al ejecutar la consulta: " . $conn->error));
        }
        // Cerrar declaración
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
