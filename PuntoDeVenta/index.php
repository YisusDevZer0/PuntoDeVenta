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
        .loader-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .loader-container img {
            width: 150px; /* Ajusta el ancho del loader */
            height: auto; /* Esto mantiene la proporción de aspecto */
        }
    </style>
</head>
<body>
    <div class="loader-container">
        <img src="https://i.gifer.com/origin/41/414f5a7b4fee1ccf9cf5771270d8dfd4.gif" alt="Loader">
    </div>

    <div class="card" style="display:none;">
        <div class="card-content">
            <span class="card-title">Inicio de sesión</span>
            <!-- Agregar un div para mostrar el mensaje -->
            <div id="mensaje" class="center-align">
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

                    echo $saludo;
                ?>
            </div>
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
            <a href="#" id="btn_ingresar" class="btn waves-effect waves-light red">Ingresar</a>
        </div>
    </div>
    
    <!-- Agrega los scripts de Materialize -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar la tarjeta de inicio de sesión y mostrarla después de un tiempo de carga simulado
            setTimeout(function() {
                document.querySelector('.loader-container').style.display = 'none';
                document.querySelector('.card').style.display = 'block';
            }, 3000); // 3000 milisegundos = 3 segundos

            // Agrega un evento al botón de ingresar para mostrar el mensaje
            document.getElementById('btn_ingresar').addEventListener('click', function() {
                M.toast({html: 'Ingresando...', classes: 'rounded'});
                // Aquí puedes agregar la lógica para enviar el formulario o realizar otras acciones
            });
        });
    </script>
</body>
</html>
