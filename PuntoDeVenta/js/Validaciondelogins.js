$('document').ready(function() { 
    // Validación del formulario utilizando el plugin jQuery Validation
    $("#login-form").validate({
        // Reglas de validación para los campos del formulario
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
        // Mensajes de error para cada campo del formulario
        messages: {
            password: {
                required: "<i class='fas fa-times'></i> Se requiere tu contraseña " 
            },
            user_email: "<i class='fas fa-times'></i> Ingresa tu correo por favor ",
        },
        // Función que se ejecuta cuando el formulario se envía correctamente
        submitHandler: submitForm    
    });   

    // Función para manejar el envío del formulario
    function submitForm() {       
        // Recolecta los datos del formulario
        var data = $("#login-form").serialize();             
        $.ajax({                
            type: 'POST',
            url: 'Consultas/ValidadorUsuario.php',
            data: data,
            beforeSend: function() {    
                $("#error").fadeOut();
                $("#login_button").html("Validando...");
            },
            success: function(response) {                        
                // Oculta el modal de validación
                $('#Validacion').modal('hide');
                if (response == "ok") {                                    
                    // Muestra un Sweet Alert de bienvenida
                    Swal.fire({
                        title: '¡Bienvenido!',
                        text: 'Gracias por visitar nuestro sitio. Esperamos que disfrutes tu experiencia.',
                        icon: 'success',
                        confirmButtonText: '¡Entendido!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirecciona al usuario después de 2 segundos
                            setTimeout(function() {
                                window.location.href = "https://doctorpez.mx/PuntoDeVenta/ControlPOS";
                            }, 2000);
                        }
                    });
                } else {                                    
                    // Muestra un Sweet Alert de error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de inicio de sesión',
                        text: 'Credenciales incorrectas o usuario inactivo',
                        showConfirmButton: false,
                        timer: 2000,
                        onClose: function() {
                            // Restaura el texto del botón a "Ingresar"
                            $("#login_button").html('<span></span> &nbsp; Ingresar');
                        }
                    });
                }
            }
        });
        return false; // Evita que el formulario se envíe de forma convencional
    }   
});
