<?php
include "db_connect.php"; // Archivo que contiene la conexión a la base de datos

// Validación y limpieza de datos
$folioProdStock = isset($_POST['Id_Serv']) ? intval($_POST['Id_Serv']) : 0;
$existenciasR = isset($_POST['resultado_ajuste']) ? intval($_POST['resultado_ajuste']) : null;
$justificacion = isset($_POST['justificacion']) ? trim($_POST['justificacion']) : '';

if ($folioProdStock && $existenciasR !== null && !empty($justificacion)) {
    // Consulta preparada para actualizar los datos
    $sql = "UPDATE `Stock_POS` SET `Existencias_R` = ?, `JustificacionAjuste` = ? WHERE `Folio_Prod_Stock` = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Enlazar los parámetros
        $stmt->bind_param("isi", $existenciasR, $justificacion, $folioProdStock);

        // Ejecutar consulta
        if ($stmt->execute()) {
            echo json_encode(array("statusCode" => 200, "message" => "Actualización realizada con éxito."));
        } else {
            echo json_encode(array("statusCode" => 201, "error" => "Error al ejecutar la consulta: " . $conn->error));
        }

        // Cerrar declaración
        $stmt->close();
    } else {
        echo json_encode(array("statusCode" => 201, "error" => "Error en la preparación de la consulta: " . $conn->error));
    }
} else {
    echo json_encode(array("statusCode" => 201, "error" => "Faltan datos requeridos o son inválidos."));
}

// Cerrar conexión
mysqli_close($conn);
?>
