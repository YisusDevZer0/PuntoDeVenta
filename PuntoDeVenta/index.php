<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUNTO DE VENTA</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Agrega esto en el encabezado de tu HTML antes de incluir tus scripts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container-login100">
        <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
            <form class="login100-form validate-form" method="post" id="login-form" autocomplete="off">
                <span class="login100-form-title p-b-49">
                    <?php echo $saludo; ?>
                    <?php echo $mensaje_aleatorio; ?>
                </span>

                <div class="wrap-input100">
                    <span class="label-input100">Correo electrónico</span>
                    <input class="input100" type="email" autocomplete="off" required placeholder="puntoventa@consulta.com" name="user_email" id="user_email" maxlength="50">
                    <span class="focus-input100" data-symbol="&#xf206;"></span>
                </div>

                <div class="wrap-input100">
                    <span class="label-input100">Contraseña</span>
                    <div class="password-input">
                        <input class="input100" type="password" required placeholder="************" autocomplete="new-password" name="password" id="password"  maxlength="10">
                        <span class="focus-input100" data-symbol="&#xf190;"></span>
                    </div>
                </div>
                <br>
                <div class="checkbox">
                    <label>
                        <input id="show_password" type="checkbox"> Mostrar contraseña
                    </label>
                </div>

                <div class="container-login100-form-btn">
                    <div class="wrap-login100-form-btn">
                        <div class="login100-form-bgbtn"></div>
                        <button class="login100-form-btn" type="submit"  name="login_button" id="login_button">
                            Ingresar
                        </button>
                    </div>
                </div>
            </form>
            <div id="error"></div>
        </div>
    </div>

    <!-- Agrega el resto de tus scripts justo antes de cerrar el cuerpo </body> -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.js"></script>
    <script src="Componentes/jquery.min.js"></script>
    <script src="Componentes/fonts.js" crossorigin="anonymous"></script>
    <script src="Componentes/js/validation.min.js"></script>
    <script src="Componentes/js/Validaciondelogins.js"></script>
    <script src="Componentes/vendor/animsition/js/animsition.min.js"></script>
    <script src="Componentes/vendor/bootstrap/js/popper.js"></script>
    <script src="Componentes/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="Componentes/vendor/select2/select2.min.js"></script>
    <script src="Componentes/vendor/daterangepicker/moment.min.js"></script>
    <script src="Componentes/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="Componentes/vendor/countdowntime/countdowntime.js"></script>
    <script src="Componentes/js/main.js"></script>

    <!-- Finalmente, el script personalizado -->
    <script>
        $(window).load(function() {
            $(".loader").fadeOut(1000);
        });

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
