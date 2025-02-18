<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🐟 Inicio de sesión | Doctor Pez 🐟</title>

    <!-- Fuentes y estilos -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Pacifico|Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />

    <!-- Iconos -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Noty.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.min.css" />

    <style>
        body {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        .bubbles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .bubble {
            position: absolute;
            bottom: -100px;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: bubble 10s infinite;
        }

        @keyframes bubble {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) scale(1.5);
                opacity: 0;
            }
        }

        .card {
            z-index: 1;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            padding: 30px;
            width: 400px;
            text-align: center;
            backdrop-filter: blur(5px);
        }

        .card-title {
            font-family: 'Pacifico', cursive;
            color: #0072ff;
            margin-bottom: 20px;
        }

        .input-field input {
            border-bottom: 2px solid #00c6ff;
        }

        .btn {
            background-color: #0072ff;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #005bb5;
        }

        .fish-icon {
            font-size: 5rem;
            color: #00c6ff;
            animation: swim 2s infinite alternate ease-in-out;
        }

        @keyframes swim {
            0% { transform: translateX(-20px) rotate(15deg); }
            100% { transform: translateX(20px) rotate(-15deg); }
        }
    </style>
</head>
<body>
    <!-- Burbujeo submarino -->
    <div class="bubbles">
        <div class="bubble" style="left: 10%; animation-duration: 6s;"></div>
        <div class="bubble" style="left: 30%; animation-duration: 7s;"></div>
        <div class="bubble" style="left: 50%; animation-duration: 5s;"></div>
        <div class="bubble" style="left: 70%; animation-duration: 6s;"></div>
        <div class="bubble" style="left: 90%; animation-duration: 7s;"></div>
    </div>

    <!-- Formulario de inicio de sesión -->
    <form method="post" id="login-form" autocomplete="off">
        <div class="card" data-aos="fade-up">
            <div class="center-align">
                <span class="material-icons fish-icon">fish</span>
                <h5 class="card-title">Bienvenido al arrecife 🌊</h5>
                <p class="blue-text text-darken-4">Ingresa tus datos para sumergirte en el sistema.</p>
            </div>
            <div class="input-field">
                <input id="user_email" type="email" class="validate" name="user_email" required>
                <label for="user_email">Correo electrónico</label>
            </div>
            <div class="input-field">
                <input id="password" type="password" class="validate" name="password" required>
                <label for="password">Contraseña</label>
            </div>
            <div class="card-action">
                <button type="submit" class="btn waves-effect waves-light">Sumergirse 🐠</button>
            </div>
        </div>
    </form>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>
        AOS.init();

        $(document).ready(function() {
            $('#login-form').on('submit', function(e) {
                e.preventDefault();

                new Noty({
                    text: '🐟 Validando tus credenciales, por favor espera...',
                    type: 'info',
                    layout: 'topCenter',
                    timeout: 3000,
                    theme: 'metroui'
                }).show();

                $.ajax({
                    type: 'POST',
                    url: 'Consultas/ValidadorUsuario.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.trim() === 'ok') {
                            new Noty({
                                text: '🌊 ¡Bienvenido al arrecife! Redirigiendo... 🐠',
                                type: 'success',
                                layout: 'topCenter',
                                timeout: 2000,
                                theme: 'metroui'
                            }).show();
                            setTimeout(function() {
                                window.location.href = "https://doctorpez.mx/PuntoDeVenta/ControlPOS";
                            }, 2000);
                        } else {
                            new Noty({
                                text: '⚠️ ¡Error! Usuario o contraseña incorrectos. 🦀',
                                type: 'error',
                                layout: 'topCenter',
                                timeout: 3000,
                                theme: 'metroui'
                            }).show();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
