<!DOCTYPE html>
<html lang="es">
<head>
    <title>PUNTO DE VENTA</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="Componentes/fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Componentes/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="Componentes/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Componentes/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Componentes/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="Componentes/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Componentes/css/util.css">
    <link rel="stylesheet" type="text/css" href="Componentes/css/main.css">
    <script src="Componentes/sweetalert2@9.js"></script>
<link rel="stylesheet" href="Componentes/bootstrap.min.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/css/mdb.min.css" rel="stylesheet">
<script src="Componentes/jquery.min.js"></script>

  
<script src="Componentes/fonts.js" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="Componentes/Preloader.css">
<!--===============================================================================================-->
<script type="text/javascript" src="Consultas/validation.min.js"></script>
<script type="text/javascript" src="Consultas/POS3.js"></script>

    <style>
        .error {
            color: red;
            margin-left: 5px;
        }
    </style>

</head>
<body style="background-color: #2FDDEE;">

    <!-- Navbar -->
    <!-- Your existing navbar code -->

    <div class="container-login100">
        <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
            <form class="login100-form validate-form" method="post" id="login-form" autocomplete="off">

                <span class="login100-form-title p-b-49">
                    <?php echo $mensaje ?>
                </span>

                <div class="wrap-input100">
                    <span class="label-input100">Correo electrónico</span>
                    <input class="input100" type="email" autocomplete="off" required placeholder="puntoventa@consulta.com" name="user_email" id="user_email" maxlength="50">
                    <span class="focus-input100" data-symbol="&#xf206;"></span>
                </div>

                <div class="wrap-input100">
                    <span class="label-input100">Contraseña</span>
                    <input class="input100" type="password" required placeholder="************" autocomplete="new-password" name="password" id="password">
                    <span class="focus-input100" data-symbol="&#xf190;"></span>
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
                        <button class="login100-form-btn" type="submit" name="login_button" id="login_button" style="background-color: #C80096;">
                            Ingresar
                        </button>
                    </div>
                </div>

            </form>
            <div id="error" class="error"></div>
        </div>
    </div>
    <footer class="page-footer font-small default-color">

<!-- Copyright -->

<b>PUNTO DE VENTA</b> | Version 1.0
</div>
<!-- Copyright -->

</footer>
<!-- Footer -->


<!--===============================================================================================-->

<script src="Componentes/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
<script src="Componentes/vendor/bootstrap/js/popper.js"></script>
<script src="Componentes/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
<script src="Componentes/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
<script src="Componentes/vendor/daterangepicker/moment.min.js"></script>
<script src="Componentes/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
<script src="Componentes/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
<script src="Componentes/js/main.js"></script>
    <!-- Your existing footer code -->

    <!-- Your existing scripts -->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script type="text/javascript">
        // When the checkbox changes state.
        $('#show_password').on('change', function (event) {
            // If the checkbox is checked
            if ($('#show_password').is(':checked')) {
                // Convert the password input to text.
                $('#password').get(0).type = 'text';
            // Otherwise...
            } else {
                // Convert it back to a password.
                $('#password').get(0).type = 'password';
            }
        });

        $('#login-form').attr('autocomplete', 'off');
    </script>

</body>
</html>
