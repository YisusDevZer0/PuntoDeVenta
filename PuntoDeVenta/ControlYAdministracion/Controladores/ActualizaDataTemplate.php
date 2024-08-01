<?php
include "db_connect.php"; // Asumiendo que este archivo contiene la conexión a la base de datos
// Obtén el ID del usuario del POST
$userId = $_POST['user_id'];

// Actualiza el registro del usuario para indicar que se ha descargado la plantilla
$sql = "UPDATE templates_downloads SET template_downloaded = 1 WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();

// Verifica si la actualización fue exitosa
if ($stmt->affected_rows > 0) {
    echo 'Estado de la plantilla actualizado con éxito.';
} else {
    echo 'No se encontró el usuario o no se realizaron cambios.';
}

// Cierra la conexión
$stmt->close();
$conn->close();
?>
