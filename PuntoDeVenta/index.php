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

// Configuración de mensajes de éxito y ventas con iconos
$mensajes_exito_ventas = array(
  "¡Bienvenido de nuevo! 🚀 Prepárate para alcanzar nuevas alturas de éxito.",
  "Te damos la bienvenida. El éxito y las ventas te esperan en cada paso que tomes. 💼",
  "Hoy es otro día para lograr grandes ventas. ¡Vamos por ello! 💪",
  "¡Hola campeón! Este es tu momento para brillar y cerrar esas ventas. 🌟",
  "Bienvenido de vuelta. Estamos emocionados por tus éxitos venideros y ventas increíbles. 🎉",
  "¡Cada nuevo día es una oportunidad para superarte a ti mismo! 🌈",
  "La única forma de hacer un gran trabajo es amar lo que haces. 💙",
  "¡Nunca subestimes el poder de la persistencia y la dedicación! 🚀",
  "El éxito es la suma de pequeños esfuerzos repetidos día tras día. 💪",
  "No hay atajos para el éxito, pero cada pequeño paso cuenta. 🏃‍♂️",
  "La clave del éxito está en actuar con determinación y confianza. 🗝️",
  "¡Tú eres capaz de lograr cosas asombrosas! Cree en ti mismo. 🌟",
  "Cada desafío es una oportunidad para crecer. ¡Acepta el desafío! 🌱",
  "La perseverancia no es una carrera larga; es muchas carreras cortas, una tras otra. 🏁",
  "No te preocupes por los errores, son oportunidades para aprender y mejorar. 🛠️",
  "El éxito es la suma de pequeños esfuerzos repetidos día tras día. 💼",
  "Nunca es tarde para ser quien podrías haber sido. 🌅",
  "La diferencia entre un sueño y un objetivo es un plan y un plazo. 🎯",
  "El éxito no es la clave de la felicidad. La felicidad es la clave del éxito. 😊",
);

// Obtener un mensaje aleatorio
$mensaje_aleatorio = $mensajes_exito_ventas[array_rand($mensajes_exito_ventas)];
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<title>PUNTO DE VENTA</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Agrega esto en el encabezado de tu HTML antes de incluir tus scripts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.js"></script>

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
    
<link rel="stylesheet" href="Componentes/bootstrap.min.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/css/mdb.min.css" rel="stylesheet">
<script src="Componentes/jquery.min.js"></script>

  
<script src="Componentes/fonts.js" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="Componentes/Preloader.css">
<!--===============================================================================================-->
<script type="text/javascript" src="js/validation.min.js"></script>
<script type="text/javascript" src="js/Validaciondelogins.js"></script>

</head>
<body style="background-color: #2FDDEE;">
   <style>
        .error {
  color: red;
  margin-left: 5px; 
  
}

    </style>

<!--Navbar -->

<!--/.Navbar -->


        
		<div class="container-login100" >
			<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
    
				<form class="login100-form validate-form" method="post" id="login-form" autocomplete="off">
        <style>
        .login100-form-title {
            font-size: 18px !important; /* Ajusta el tamaño de fuente según tus preferencias */
        }
    </style>
					<span class="login100-form-title p-b-49">
          <?php echo $saludo; ?>
    <?php echo $mensaje_aleatorio; ?>
					</span>

					<div class="wrap-input100 " >
						<span class="label-input100">Correo electronico</span>
						<input class="input100" input type="email" autocomplete="off" required placeholder="puntoventa@consulta.com" name="user_email" id="user_email" maxlength="50">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
					</div>

					<div class="wrap-input100 ">
						<span class="label-input100">Contraseña</span>
						<input class="input100" type="password" required placeholder="************" autocomplete="new-password" name="password" id="password"  maxlength="10">
                       
						<span class="focus-input100" data-symbol="&#xf190;"></span>
                        
                    </div>
                    <br>
                    <div class="checkbox">
    <label>
    <input id="show_password" type="checkbox" /> Mostrar contraseña
    </label>
  </div>   
 
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn" type="submit"  name="login_button" id="login_button"  style="background-color: #C80096;">
								Ingresar
							</button>
						</div>
					</div>
                 
                    </form>  <div id="error">
  </div>


					
					<!--Start of Tawk.to Script-->

<!--End of Tawk.to Script-->
			
			</div>
		</div>
	</div>
	
<!-- Modal hacia soporte -->

    
    
  <!-- Copyright -->

 
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

</body>
</html>

    <script type="text/javascript">
$(window).load(function() {
    $(".loader").fadeOut(1000);
});
</script>

<script>

   // Cuando el checkbox cambie de estado.
$('#show_password').on('change',function(event){
   // Si el checkbox esta "checkeado"
   if($('#show_password').is(':checked')){
      // Convertimos el input de contraseña a texto.
      $('#password').get(0).type='text';
   // En caso contrario..
   } else {
      // Lo convertimos a contraseña.
      $('#password').get(0).type='password';
   }
});

$('#login-form').attr('autocomplete', 'off');
</script>
