<?php

function iniciarSesion($usuario, $contrasena) {
    $url = "https://www.levicventas.mx/frm_Catalogo_Levic.aspx";
    $postData = array(
        'txtLogin' => $usuario,
        'txtPassword' => $contrasena,
        'btnLogin' => 'Iniciar sesión'
    );

    // Inicializar cURL
    $ch = curl_init();

    // Establecer opciones de cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Establecer el agente de usuario para simular una solicitud de navegador
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36");

    // Permitir seguir redireccionamientos
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Ejecutar la solicitud cURL
    $response = curl_exec($ch);

    // Manejar errores
    if ($response === false) {
        // Manejar el error de cURL aquí
        echo "Error de cURL: " . curl_error($ch);
    } else {
        // Verificar si la sesión se inició correctamente
        if (strpos($response, 'Inicio de sesión exitoso') !== false) {
            echo "Inicio de sesión exitoso.";
            // Aquí puedes realizar otras operaciones después de iniciar sesión, como realizar búsquedas.
        } else {
            echo "No se pudo iniciar sesión.";
        }
    }

    // Cerrar la sesión cURL
    curl_close($ch);
}

// Credenciales de inicio de sesión
$usuario = "244426";
$contrasena = "tu_contraseña";

// Llamar a la función para iniciar sesión con las credenciales proporcionadas
iniciarSesion($usuario, $contrasena);

?>
