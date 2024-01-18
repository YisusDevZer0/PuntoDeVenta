$('document').ready(function() { 

	/* handling form validation */
	$("#login-form").validate({
		rules: {
			password: {
				required: true,
			},
			nivel: {
				required: true,
			},
			user_email: {
				required: true,
				email: true
			},
		},
		messages: {
			password: {
			  required: "<i class='fas fa-times'></i> Se requiere tu contraseña " 
			},
			user_email: "<i class='fas fa-times'></i> Ingresa tu correo por favor ",
		},
		submitHandler: submitForm	
	});	   
	/* Handling login functionality */
	function submitForm() {		
		var data = $("#login-form").serialize();				
		$.ajax({				
			type: 'POST',
			url: 'Consultas/ValidadorUsuario.php',
			data: data,
			beforeSend: function() {	
				$("#error").fadeOut();
				$("#login_button").html("Validando...");
				// Aquí es donde se muestra el mensaje de bienvenida con SweetAlert2
				Swal.fire({
				  title: '¡Bienvenido!',
				  text: 'Gracias por visitar nuestro sitio. Esperamos que disfrutes tu experiencia.',
				  icon: 'success',
				  confirmButtonText: '¡Entendido!',
				  // Puedes agregar más configuraciones según la documentación de SweetAlert2
				});
			  },
			success: function(response) {						
				$('#Validacion').modal('hide');
				if (response == "ok") {									
					$("#login_button").html("Iniciando...");
					$('#Ingreso').modal('toggle');
					setTimeout(function() {
						window.location.href = "https://doctorpez.mx/PuntoDeVenta/ControlPOS";
					}, 2000);
				} else {									
					Swal.fire({
						icon: 'error',
						title: 'Error de inicio de sesión',
						text: 'Credenciales incorrectas o usuario inactivo',
						showConfirmButton: false,
						timer: 2000,
						onClose: function() {
							$("#login_button").html('<span></span> &nbsp; Ingresar');
						}
					});
				}
			}
		});
		return false;
	}   
});
