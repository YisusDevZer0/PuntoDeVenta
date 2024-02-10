<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUNTO DE VENTA</title>
    <!-- Agrega los estilos de Material Design -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 400px;
            padding: 20px;
        }
        .card-content {
            padding: 20px;
        }
        .card-action {
            background-color: #C80096;
            text-align: center;
        }
        .btn {
            background-color: #C80096;
        }
        .btn:hover {
            background-color: #960056;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-content">
            <span class="card-title">Inicio de sesión</span>
            <!-- Agregar un div para mostrar el mensaje -->
            <div id="mensaje" class="center-align"></div>
            <div class="input-field">
                <input id="user_email" type="email" class="validate">
                <label for="user_email">Correo electrónico</label>
            </div>
            <div class="input-field">
                <input id="password" type="password" class="validate">
                <label for="password">Contraseña</label>
            </div>
        </div>
        <div class="card-action">
            <a href="#" id="btn_ingresar" class="btn waves-effect waves-light">Ingresar</a>
        </div>
    </div>
    
    <!-- Agrega los scripts de Materialize y el script PHP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <?php
    // Configuración de saludos según la hora del día
    $saludos = array(
        "Buenos días",
        "Buenas tardes",
        "Buenas noches"
    );

    // Obtener la hora actual del servidor
    $hora_actual = date("H");

    // Determinar el saludo según la hora del día
    if ($hora_actual >= 5 && $hora_actual < 12) {
        $saludo = $saludos[0]; // Mañana
    } elseif ($hora_actual >= 12 && $hora_actual < 18) {
        $saludo = $saludos[1]; // Tarde
    } else {
        $saludo = $saludos[2]; // Noche
    }

    // Configuración de mensajes de éxito y ventas en español e inglés
    $mensajes_exito_ventas_es = array(
        "¡Bienvenido de nuevo! 🚀 Prepárate para alcanzar nuevas alturas de éxito.",
        "Te damos la bienvenida. El éxito y las ventas te esperan en cada paso que tomes. 💼",
        // Otros mensajes en español...
    );

    $mensajes_exito_ventas_en = array(
        "Welcome back! 🚀 Get ready to reach new heights of success.",
        "Welcome aboard. Success and sales await you with every step you take. 💼",
        // Otros mensajes en inglés...
    );

    // Función para obtener el mensaje en el idioma seleccionado
    function obtener_mensaje_localizado($idioma) {
        global $mensajes_exito_ventas_es, $mensajes_exito_ventas_en;
        switch ($idioma) {
            case 'es':
                return $mensajes_exito_ventas_es[array_rand($mensajes_exito_ventas_es)];
                break;
            case 'en':
                return $mensajes_exito_ventas_en[array_rand($mensajes_exito_ventas_en)];
                break;
            // Otros casos para más idiomas...
            default:
                return "Idioma no compatible";
        }
    }

    // Obtener el mensaje en el idioma seleccionado (por ejemplo, español 'es' o inglés 'en')
    $idioma_seleccionado = "es"; // Puedes obtener este valor dinámicamente según la configuración del usuario
    $mensaje_localizado = obtener_mensaje_localizado($idioma_seleccionado);
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Muestra el mensaje de saludo dinámico
            M.toast({html: '<?php echo $saludo; ?>', classes: 'rounded'});
            // Muestra el mensaje de éxito dinámico en el formulario
            var mensajeDiv = document.getElementById('mensaje');
            mensajeDiv.innerHTML = '<?php echo $mensaje_localizado; ?>';
            
            // Agrega un evento al botón de ingresar para mostrar el mensaje
            document.getElementById('btn_ingresar').addEventListener('click', function() {
                M.toast({html: 'Ingresando...', classes: 'rounded'});
                // Aquí puedes agregar la lógica para enviar el formulario o realizar otras acciones
            });
        });
    </script>
</body>
</html>
