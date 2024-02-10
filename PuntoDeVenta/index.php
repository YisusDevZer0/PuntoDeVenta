<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUNTO DE VENTA</title>
    <!-- Agrega tus estilos personalizados aquí -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2FDDEE;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container-login100 {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            border-radius: 10px;
            padding: 50px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        .login100-form-title {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            z-index: 1;
        }

        .wrap-input100 {
            position: relative;
            margin-bottom: 30px;
        }

        .label-input100 {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        .input100 {
            width: calc(100% - 30px);
            padding: 15px;
            border: none;
            border-bottom: 2px solid #ccc;
            font-size: 16px;
            background-color: transparent;
            transition: border-color 0.3s;
        }

        .input100:focus {
            border-color: #C80096;
            outline: none;
        }

        .checkbox {
            margin-bottom: 20px;
        }

        .checkbox label {
            color: #666;
            font-size: 14px;
            cursor: pointer;
        }

        .checkbox input {
            margin-right: 5px;
            vertical-align: middle;
        }

        .container-login100-form-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
        }

        .login100-form-btn {
            width: 100%;
            padding: 15px;
            background-color: #C80096;
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login100-form-btn:hover {
            background-color: #960056;
        }

        .bg-login100 {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #C80096;
            z-index: 0;
            transition: transform 0.8s ease-in-out;
            transform: translateX(-100%);
        }

        .container-login100:hover .bg-login100 {
            transform: translateX(0);
        }

        .bg-login100::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 200%;
            height: 200%;
            background-image: radial-gradient(circle closest-side, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
            z-index: 1;
            transition: opacity 0.8s ease-in-out;
            opacity: 0;
        }

        .container-login100:hover .bg-login100::before {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container-login100">
        <form class="login100-form validate-form" method="post" id="login-form" autocomplete="off">
            <span class="login100-form-title">
                <?php echo $saludo; ?><br>
                <?php echo $mensaje_aleatorio; ?>
            </span>

            <div class="wrap-input100">
                <span class="label-input100">Correo electrónico</span>
                <input class="input100" type="email" autocomplete="off" required placeholder="puntoventa@consulta.com" name="user_email" id="user_email" maxlength="50">
            </div>

            <div class="wrap-input100">
                <span class="label-input100">Contraseña</span>
                <input class="input100" type="password" required placeholder="************" autocomplete="new-password" name="password" id="password"  maxlength="10">
            </div>

            <div class="checkbox">
                <label>
                    <input id="show_password" type="checkbox"> Mostrar contraseña
                </label>
            </div>

            <div class="container-login100-form-btn">
                <button class="login100-form-btn" type="submit"  name="login_button" id="login_button">
                    Ingresar
                </button>
            </div>

            <div class="bg-login100"></div>
        </form>
        <div id="error"></div>
    </div>

    <!-- Agrega tus scripts aquí -->

    <script>
        // Script para mostrar/ocultar contraseña
        $('#show_password').on('change', function(event) {
            if ($('#show_password').is(':checked')) {
                $('#password').get(0).type = 'text';
            } else {
                $('#password').get(0).type = 'password';
            }
        });

        $('#login-form').attr('autocomplete', 'off');
    </script>
</body>
</html>
