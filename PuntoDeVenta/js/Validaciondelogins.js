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
			url: 'https://doctorpez.mx/PuntoDeVenta/Consultas/ValidadorUsaurio.php',
			data: data,
			beforeSend: function() {	
				$("#error").fadeOut();
				$("#login_button").html("Validando...");
				$('#Validacion').modal('toggle');
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
