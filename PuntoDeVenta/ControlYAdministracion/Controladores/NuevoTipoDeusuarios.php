<?php
// Incluir la configuración y la conexión a la base de datos
include "Config.php";

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $tipoUsuario = $_POST['tipoUsuario'];
    $licencia = $_POST['licencia'];
    $agrego = $_POST['agrego'];

    // Insertar datos en la base de datos
    $query = "INSERT INTO Tipos_Usuarios (TipoUsuario, Licencia, Agrego) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('sss', $tipoUsuario, $licencia, $agrego);

    if ($stmt->execute()) {
        $respuesta = array('success' => true);
    } else {
        $respuesta = array('success' => false, 'message' => 'Error al insertar en la base de datos: ' . $stmt->error);
    }

    // Cerrar la consulta
    $stmt->close();
} else {
    // No es una solicitud POST válida
    $respuesta = array('success' => false, 'message' => 'Solicitud no válida.');
}

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($respuesta);

// Cerrar la conexión a la base de datos
$conexion->close();
?>
