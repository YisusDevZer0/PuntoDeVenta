<?php
include "db_connect.php"; // Conexión a la base de datos

// file_put_contents('debug_update.txt', print_r($_POST, true));

// Validación y limpieza de datos del formulario
$Id_PvUser = isset($_POST['Id_Serv']) ? intval($_POST['Id_Serv']) : 0;
$Nombre_Apellidos = isset($_POST['NombreEmpleado']) ? trim($_POST['NombreEmpleado']) : '';
$Password = isset($_POST['Contra']) ? trim($_POST['Contra']) : '';
$Fk_Usuario = isset($_POST['TiposUsuarios']) ? intval($_POST['TiposUsuarios']) : 0;
$Fk_Sucursal = isset($_POST['Sucursal']) ? intval($_POST['Sucursal']) : 0;
$Fecha_Nacimiento = isset($_POST['Fechana']) ? trim($_POST['Fechana']) : '';
$Correo_Electronico = isset($_POST['Correo']) ? trim($_POST['Correo']) : '';
$Telefono = isset($_POST['Telefono']) ? trim($_POST['Telefono']) : '';
$Estatus = isset($_POST['Estado']) ? trim($_POST['Estado']) : '';
$ActualizadoPor = isset($_POST['Actualiza']) ? trim($_POST['Actualiza']) : '';

// Verificar si todos los datos requeridos están presentes
if ($Id_PvUser && $Nombre_Apellidos && $Password && $Correo_Electronico && $Telefono && $Estatus) {
    // Consulta preparada para actualizar los datos del usuario
    $sql = "UPDATE `Usuarios_PV` 
            SET `Nombre_Apellidos`=?, `Password`=?, `Fk_Usuario`=?, `Fk_Sucursal`=?, `Fecha_Nacimiento`=?, `Correo_Electronico`=?, `Telefono`=?, `Estatus`=?, `AgregadoPor`=? 
            WHERE `Id_PvUser`=?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Enlazar parámetros
        $stmt->bind_param("ssiiissssi", $Nombre_Apellidos, $Password, $Fk_Usuario, $Fk_Sucursal, $Fecha_Nacimiento, $Correo_Electronico, $Telefono, $Estatus, $ActualizadoPor, $Id_PvUser);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo json_encode(array("statusCode" => 200, "message" => "Actualización exitosa."));
        } else {
            echo json_encode(array("statusCode" => 201, "error" => "Error al ejecutar la consulta: " . $stmt->error));
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
