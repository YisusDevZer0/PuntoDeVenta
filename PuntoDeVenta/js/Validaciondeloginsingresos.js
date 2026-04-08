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

        // Muestra una notificación llamativa con Noty.js antes de enviar
        new Noty({
            text: '🐟 Validando tus credenciales, por favor espera...',
            type: 'info',
            layout: 'topCenter',
            timeout: 3000,
            theme: 'metroui'
        }).show();

        $.ajax({                
            type: 'POST',
            url: 'Consultas/ValidadorUsuario.php',
            data: data,
            success: function(response) {                        
                if (response.trim() === 'ok') {                                    
                    // Muestra una notificación de éxito con Noty.js
                    new Noty({
                        text: '🌊 ¡Bienvenido al arrecife! Redirigiendo... 🐠',
                        type: 'success',
                        layout: 'topCenter',
                        timeout: 2000,
                        theme: 'metroui'
                    }).show();
                    setTimeout(function() {
                        window.location.href = (typeof fdpUrl === 'function' ? fdpUrl('ControlPOS') : ((window.__FDP_BASE_URL__||"")+"ControlPOS"));
                    }, 2000);
                } else {                                    
                    // Muestra una notificación de error con Noty.js
                    new Noty({
                        text: '⚠️ ¡Error! Usuario o contraseña incorrectos. 🦀',
                        type: 'error',
                        layout: 'topCenter',
                        timeout: 3000,
                        theme: 'metroui'
                    }).show();
                }
            }
        });
        return false; // Evita que el formulario se envíe de forma convencional
    }   

    // Inicializar AOS para animaciones
    AOS.init();
});
