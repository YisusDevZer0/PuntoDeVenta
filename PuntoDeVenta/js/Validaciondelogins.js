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
			url  : 'Consultas/ValidadorUsuario.php',
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
					 // Mostrar SweetAlert2 con animaciones
                     Swal.fire({
                        title: '¡Bienvenido de nuevo!',
                        text: saludo + ' ' + mensaje_aleatorio,
                        icon: 'success',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    });
                
					setTimeout(' window.location.href = "https://controlfarmacia.com/App/Secure/ControlPOS"; ',2000);
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