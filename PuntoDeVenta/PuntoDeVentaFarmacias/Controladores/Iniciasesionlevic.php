<?php

function iniciarSesion($usuario, $contrasena) {
    $loginUrl = "https://www.levicventas.mx/frm_Catalogo_Levic.aspx";
    
    // Inicializar cURL para obtener la página de inicio de sesión
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    $loginPage = curl_exec($ch);
    
    if ($loginPage === false) {
        echo "Error al obtener la página de inicio de sesión: " . curl_error($ch);
        return false;
    }

    // Extraer el token CSRF o cualquier otro campo necesario desde el HTML
    // Esto puede variar dependiendo del sitio web específico
    // Ejemplo de cómo extraer un token CSRF con una expresión regular:
    // preg_match('/name="__RequestVerificationToken" value="([^"]+)"/', $loginPage, $matches);
    // $csrfToken = $matches[1];

    // Datos de inicio de sesión
    $postData = array(
        'txtLogin' => $usuario,
        'txtPassword' => $contrasena,
        // '__RequestVerificationToken' => $csrfToken, // Agregar el token si es necesario
        'btnLogin' => 'Iniciar sesión'
    );

    // Enviar la solicitud de inicio de sesión
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);

    if ($response === false) {
        echo "Error de cURL: " . curl_error($ch);
        return false;
    }

    // Verificar si el inicio de sesión fue exitoso
    if (strpos($response, 'Inicio de sesión exitoso') !== false) {
        echo "Inicio de sesión exitoso.";
        return true;
    } else {
        echo "No se pudo iniciar sesión.";
        return false;
    }

    curl_close($ch);
}

// Credenciales de inicio de sesión
$usuario = "244426";
$contrasena = "Doctorconsulta01";

// Llamar a la función para iniciar sesión con las credenciales proporcionadas
iniciarSesion($usuario, $contrasena);

?>


