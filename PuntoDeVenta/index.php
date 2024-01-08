
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Bienvenido al inicio de sesion del punto de venta</title>
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/sweetalert2.css">
	<link rel="stylesheet" href="css/material.min.css">
	<link rel="stylesheet" href="css/material-design-iconic-font.min.css">
	<link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="css/main.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/jquery-1.11.2.min.js"><\/script>')</script>
	<script src="js/material.min.js" ></script>
	<script src="js/sweetalert2.min.js" ></script>
	<script src="js/jquery.mCustomScrollbar.concat.min.js" ></script>
	<script src="js/main.js" defer></script>
	
</head>
<body>
	<div class="login-wrap cover">
		<div class="container-login">
			<p class="text-center" style="font-size: 80px;">
				<i class="zmdi zmdi-account-circle"></i>
			</p>
			<p class="text-center text-condensedLight">Inicia sesión con tu cuenta</p>
			<form action="" id="login-form">
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				    <input class="mdl-textfield__input" type="text" name="user_email" id="user_email">
				    <label class="mdl-textfield__label" for="userName">Correo o nombre de usuario</label>
				</div>
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				    <input class="mdl-textfield__input" type="password" name="password" id="password">
				    <label class="mdl-textfield__label" for="pass">Ingresa tu contraseña</label>
				</div>
				<br>
                    <div class="checkbox">
    <label>
    <input id="show_password" type="checkbox" /> Mostrar contraseña
    </label>
  </div>   
				<button class="mdl-button mdl-js-button mdl-js-ripple-effect" style="color: #3F51B5; margin: 0 auto; display: block;">
					Ingresar
				</button>
			</form>
		</div>
	</div>
</body>
</html>
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