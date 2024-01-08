<?php
// Verificar si se reciben datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el nombre de usuario y contraseña enviados por el formulario
    $userName = $_POST["userName"];
    $password = $_POST["pass"];

    // Verificar la autenticación (esto es un ejemplo básico, debes mejorar la seguridad)
    if ($userName == "admin" && $password == "admin_password") {
        // Usuario y contraseña son válidos para el administrador
        $response = array("success" => true, "message" => "Bienvenido administrador");
    } elseif ($userName == "vendedor" && $password == "vendedor_password") {
        // Usuario y contraseña son válidos para el vendedor
        $response = array("success" => true, "message" => "Bienvenido vendedor");
    } else {
        // Usuario o contraseña incorrectos
        $response = array("success" => false, "message" => "Credenciales incorrectas");
    }

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
