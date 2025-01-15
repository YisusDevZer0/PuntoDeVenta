<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("Controladores/db_connect.php");

$id = $_POST['id'] ?? 'Prueba';

$sql = "SELECT * FROM encargos WHERE NumTicket = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conn->error);
}

$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "Datos encontrados.";
    } else {
        echo "Sin resultados.";
    }
} else {
    echo "Error al ejecutar la consulta: " . $stmt->error;
}
