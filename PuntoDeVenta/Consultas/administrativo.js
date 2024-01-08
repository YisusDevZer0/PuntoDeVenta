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
			  required: "Ingresa la contraseña"
			 },
			user_email: "Ingresa el correo",
		},
		submitHandler: submitForm	
	});	   
	/* Handling login functionality */
	function submitForm() {		
		var data = $("#login-form").serialize();				
		$.ajax({				
			type : 'POST',
			url  : 'Scripts/Enfermeria.php',
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
                        timer:2000,
                      }))
                      setTimeout(function(){ 
                        $('#Ingreso').modal('toggle');
                    }, 3000); 
                   
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