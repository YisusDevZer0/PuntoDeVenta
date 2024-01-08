$('document').ready(function() { 
	/* handling form validation */
	$("#login-form").validate({
		rules: {
			password: {
				required: true,
			},
			user_email: {
				required: true,
				email: true
			},
		},
		messages: {
			password:{
			  required: "Se requiere tu contraseña"
			 },
			user_email: "ingresa tu correo por favor.",
		},
		submitHandler: submitForm	
	});	   
	/* Handling login functionality */
	function submitForm() {		
		var data = $("#login-form").serialize();				
		$.ajax({				
			type : 'POST',
			url  : 'Scripts/Citas.php',
			data : data,
			beforeSend: function(){	
				$("#error").fadeOut();
				
				$("#login_button").html(Swal.fire({
					showConfirmButton: false,
					imageUrl: 'images/Verificando.gif',
					imageWidth: 900,
					title: 'Verificando Datos',
					imageAlt: 'Custom image',
					timer:6000,
				  }));
			},
			success : function(response){						
				if(response=="ok"){									
					$("#login_button").html("Iniciando ",Swal.fire({
						icon: 'success',
						title: 'Datos Correctos.',
						text: 'Bienvenido, espere un momento!',
						showConfirmButton: false,
					  }))
					setTimeout(' window.location.href = "https://controlfarmacia.com/Controldecitas/"; ',2000);
				} else {									
					$("#error").fadeIn(1000, function(){									
						$("#error").html(Swal.fire({
							icon: 'error',
							title: 'Datos no validos...',
							text: 'Usuario o contraseña incorrectos.',
							showConfirmButton: true,
						  }));
						  $("#login_button").html('<span ></span> &nbsp;   Ingresar   ');
				
						});
				}
			}
		});
		return false;
	}   
});