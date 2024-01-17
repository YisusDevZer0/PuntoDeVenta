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
			password:{
			  required: "<i class='fas fa-times'></i> Se requiere tu contraseña " 
			 },
			 
			user_email: "<i class='fas fa-times'></i> ingresa tu correo por favor ",
		},
		submitHandler: submitForm	
	});	   
	/* Handling login functionality */
	function submitForm() {		
		var data = $("#login-form").serialize();				
		$.ajax({				
			type : 'POST',
			url  : 'Consultas/ValidadorUsaurio.php',
			data : data,
            beforeSend: function(){	
				$("#error").fadeOut();
				
				$("#login_button").html();
				$('#Validacion').modal('toggle');
				setTimeout(function(){ 
					$('#Validacion').modal('hide') 
				}, 3000); 
                  
			},
				
			success: function(response) {						
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
						timer: 2000
					});
			
					$("#login_button").html('<span ></span> &nbsp;   Ingresar   ');
				}
			}
			
		});
		return false;
	}   
});