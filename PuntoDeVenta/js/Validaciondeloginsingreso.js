$('document').ready(function() { 
    // Inicializar los modales
    $('.modal').modal();

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
                // Muestra el modal de validación
                $('#Validacion').modal('open');
            },
            success: function(response) {                        
                // Cierra el modal de validación
                $('#Validacion').modal('close');
                if (response == "ok") {                                    
                    // Redirecciona al usuario después de 2 segundos
                    setTimeout(function() {
                        window.location.href = "https://doctorpez.mx/PuntoDeVenta/ControlPOS";
                    }, 2000);
                } else {                                    
                    // Muestra el modal de error
                    $('#Error').modal('open');
                }
            }
        });
        return false; // Evita que el formulario se envíe de forma convencional
    }   
});
