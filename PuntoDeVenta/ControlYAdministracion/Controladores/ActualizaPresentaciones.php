<?php
include "db_connect.php"; // Asumiendo que este archivo contiene la conexión a la base de datos

// Validación y limpieza de datos
$Presentacion_ID = isset($_POST['Id_Serv']) ? intval($_POST['Id_Serv']) : 0;
$Nom_Presentacion = isset($_POST['ActNomServ']) ? trim($_POST['ActNomServ']) : '';

$ActualizadoPor = isset($_POST['ActUsuarioCServ']) ? trim($_POST['ActUsuarioCServ']) : '';
$Sistema = isset($_POST['ActSistemaCServ']) ? trim($_POST['ActSistemaCServ']) : '';
$Licencia = isset($_POST['LicenciaServ']) ? trim($_POST['LicenciaServ']) : '';

// Verificar si todos los datos requeridos están presentes
if ($Presentacion_ID && $Nom_Presentacion && $ActualizadoPor && $Sistema && $Licencia) {
    // Consulta preparada
    $sql = "UPDATE `Presentaciones` SET `Nom_Presentacion`=?,  `ActualizadoPor`=?, `Sistema`=?, `Licencia`=? WHERE `Presentacion_ID`=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Enlazar parámetros
        $stmt->bind_param("ssssi", $Nom_Presentacion,  $ActualizadoPor, $Sistema, $Licencia, $Presentacion_ID);
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
