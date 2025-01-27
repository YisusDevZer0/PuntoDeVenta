<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Depuración: imprime los datos recibidos para verificar qué se está enviando desde el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode($_POST);
    
}

// Obtener y validar datos del formulario
$Folio_Prod_Stock = isset($_POST['Id_Serv']) ? intval($_POST['Id_Serv']) : 0;
$Max_Existencia = isset($_POST['ActNomServ']) ? trim($_POST['ActNomServ']) : '';
$Min_Existencia = isset($_POST['ActMinServ']) ? trim($_POST['ActMinServ']) : '';
$ActualizadoPor = isset($_POST['ActUsuarioCServ']) ? trim($_POST['ActUsuarioCServ']) : '';

// Verificar si todos los datos requeridos están presentes
if (empty($Folio_Prod_Stock) || empty($Max_Existencia) || empty($Min_Existencia) || empty($ActualizadoPor)) {
    echo json_encode(array("statusCode" => 201, "error" => "Faltan datos requeridos."));
    return;
}

// Preparar la consulta para actualizar la base de datos de manera segura
$sql = "UPDATE `Stock_POS` SET `Max_Existencia`=?, `Min_Existencia`=?, `ActualizadoPor`=? WHERE `Folio_Prod_Stock`=?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iisi", $Max_Existencia, $Min_Existencia, $ActualizadoPor, $Folio_Prod_Stock);

    if ($stmt->execute()) {
        echo json_encode(array("statusCode" => 200, "message" => "Actualización exitosa."));
    } else {
        echo json_encode(array("statusCode" => 201, "error" => "Error al ejecutar la consulta: " . $conn->error));
    }
    $stmt->close();
} else {
    echo json_encode(array("statusCode" => 201, "error" => "Error en la preparación de la consulta: " . $conn->error));
}

// Cerrar conexión
$conn->close();
?>
