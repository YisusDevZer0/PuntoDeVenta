<?php
include "db_connect.php"; // Asumiendo que este archivo contiene la conexión a la base de datos

// Validación y limpieza de datos
$Tip_Prod_ID = isset($_POST['Id_Serv']) ? intval($_POST['Id_Serv']) : 0;
$Nom_Tipo_Prod = isset($_POST['ActNomServ']) ? trim($_POST['ActNomServ']) : '';
$ActualizadoPor = isset($_POST['ActUsuarioCServ']) ? trim($_POST['ActUsuarioCServ']) : '';

// Verificar si todos los datos requeridos están presentes
if ($Tip_Prod_ID && $Nom_Tipo_Prod && $ActualizadoPor) {
    // Consulta preparada
    $sql = "UPDATE `TipProd_POS` SET `Nom_Tipo_Prod`=?, `ActualizadoPor`=? WHERE `Tip_Prod_ID`=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Enlazar parámetros
        $stmt->bind_param("ssi", $Nom_Tipo_Prod, $ActualizadoPor, $Tip_Prod_ID);
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
