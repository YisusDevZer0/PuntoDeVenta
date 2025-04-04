<?php
include "db_connect.php"; // Asumiendo que este archivo contiene la conexión a la base de datos

// Validación y limpieza de datos
$ID_Caja = isset($_POST['ID_Caja']) ? intval($_POST['ID_Caja']) : 0;
$Asignacion = isset($_POST['Estatus']) ? intval($_POST['Estatus']) : 0;

// Mensajes de depuración


// Verificar si todos los datos requeridos están presentes
if ($ID_Caja && $Asignacion) {
    // Consulta preparada
    $sql = "UPDATE `Cajas` SET `Asignacion`=? WHERE `ID_Caja`=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Enlazar parámetros
        $stmt->bind_param("ii", $Asignacion, $ID_Caja);
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
