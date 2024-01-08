<?php
include "https://doctorpez.mx/PuntoDeVenta/Config/Conexion.php";
// Verificar si se reciben datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el correo electrónico y contraseña enviados por el formulario
    $correoElectronico = $_POST["userName"];
    $password = $_POST["passwordd"];

   

    // Preparar la consulta para seleccionar el usuario por correo electrónico y contraseña
    $stmt = $con->prepare("SELECT Correo_Electronico, Password, Fk_Usuario FROM Usuarios_PV WHERE Correo_Electronico = ? AND Password = ?");
    $stmt->bind_param("ss", $correoElectronico, $password);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado de la consulta
    $result = $stmt->get_result();

    // Verificar si se encontró un usuario
    if ($result->num_rows == 1) {
        // Usuario y contraseña son válidos
        $response = array("success" => true, "message" => "Inicio de sesión exitoso");

        // Puedes obtener más información del usuario si es necesario
        $userData = $result->fetch_assoc();
        $userId = $userData['Fk_Usuario'];
    } else {
        // Usuario o contraseña incorrectos
        $response = array("success" => false, "message" => "Credenciales incorrectas");
    }

    // Cerrar la conexión y liberar los recursos
    $stmt->close();
    $mysqli->close();

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Si la solicitud no es por método POST, devolver un mensaje de error
    $response = array("success" => false, "message" => "Método no permitido");
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
