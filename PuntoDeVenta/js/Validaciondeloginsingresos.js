$(document).ready(function() { 
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
                // Muestra una notificación llamativa con Noty.js
                new Noty({
                    text: 'Validando... Por favor, espera un momento.',
                    type: 'info',
                    layout: 'topCenter',
                    theme: 'metroui',
                    timeout: 3000,
                    progressBar: true
                }).show();
            },
            success: function(response) {                        
                if (response.trim().includes("ok")) {                                    
                    // Muestra una notificación de éxito con Noty.js
                    new Noty({
                        text: '¡Bienvenido! Acceso concedido. Redirigiendo...',
                        type: 'success',
                        layout: 'topCenter',
                        theme: 'metroui',
                        timeout: 2000,
                        progressBar: true
                    }).show();
                    setTimeout(function() {
                        window.location.href = "https://doctorpez.mx/PuntoDeVenta/ControlPOS";
                    }, 2000);
                } else {                                    
                    // Muestra una notificación de error con Noty.js
                    new Noty({
                        text: '¡Error! Usuario o contraseña incorrectos. Por favor, intenta de nuevo.',
                        type: 'error',
                        layout: 'topCenter',
                        theme: 'metroui',
                        timeout: 3000,
                        progressBar: true
                    }).show();
                }
            }
        });
        return false; // Evita que el formulario se envíe de forma convencional
    }   

    // Inicializar AOS para animaciones
    AOS.init();
});
