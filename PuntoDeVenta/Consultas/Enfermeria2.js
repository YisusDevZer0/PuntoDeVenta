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
			  required: "<i class='fas fa-times'></i> Se requiere tu contraseña "
			 },
			user_email: "<i class='fas fa-times'></i> Ingresa tu correo por favor",
		},
		submitHandler: submitForm	
	});	   
	/* Handling login functionality */
	function submitForm() {		
		var data = $("#login-form").serialize();				
		$.ajax({				
			type : 'POST',
			url  : 'Scripts/Enfermeria2.php',
			data : data,
			beforeSend: function(){	
				$("#error").fadeOut();
				
				$("#login_button").html();
				$('#Validacion').modal('toggle');
				setTimeout(function(){ 
					$('#Validacion').modal('hide') 
				}, 3000); 
                  
			},
			success : function(response){						
				if(response=="ok"){									
                    $("#login_button").html("Iniciando...")
					$('#Ingreso').modal('toggle');
					setTimeout(' window.location.href = "https://controlfarmacia.com/App/Secure/ControladorEnfermeria"; ',2000);
				} else {									
					$("#error").fadeIn(1000, function(){						
                        $("#error").html();
                        setTimeout(function(){ 
                            $('#Fallo').modal('toggle');
                        }, 2000); 
						
                        $("#login_button").html('<span ></span> &nbsp;   Ingresar   ');
					});
				}
			}
		});
		return false;
	}   
});