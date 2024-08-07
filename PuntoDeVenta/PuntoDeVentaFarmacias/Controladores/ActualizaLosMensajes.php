<?php
include "db_connect.php"; // Asumiendo que este archivo contiene la conexión a la base de datos

// Validación y limpieza de datos
$ID_Notificacion = isset($_POST['ID_Notificacion']) ? intval($_POST['ID_Notificacion']) : 0;
$Estado = isset($_POST['Estado']) ? intval($_POST['Estado']) : 0;

// Verificar si todos los datos requeridos están presentes
if ($ID_Notificacion && $Estado) {
    // Consulta preparada
    $sql = "UPDATE `Recordatorios_Pendientes` SET `Estado`=? WHERE `ID_Notificacion`=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Enlazar parámetros
        $stmt->bind_param("ii", $Estado, $ID_Notificacion);
        // Ejecutar consulta
        if ($stmt->execute()) {
            echo json_encode(array("statusCode" => 200, "message" => "Registro actualizado con éxito."));
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
