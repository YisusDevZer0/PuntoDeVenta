$(document).ready(function() {
    // Inicializar los modales
    $('.modal').modal();

    // Validación del formulario utilizando el plugin jQuery Validation
    $("#login-form").validate({
        rules: {
            password: { required: true },
            nivel: { required: true },
            user_email: { required: true, email: true }
        },
        messages: {
            password: { required: "<i class='fas fa-times'></i> Se requiere tu contraseña " },
            user_email: "<i class='fas fa-times'></i> Ingresa tu correo por favor ",
        },
        submitHandler: submitForm    
    });   

    // Función para manejar el envío del formulario
    function submitForm() {       
        var data = $("#login-form").serialize();             

        // Muestra una notificación llamativa con Noty.js antes de enviar
        new Noty({
            text: '🐟 Validando tus credenciales, por favor espera...',
            type: 'info',
            layout: 'topCenter',
            timeout: 3000,
            theme: 'metroui',
            animation: {
                open: 'animated bounceIn',
                close: 'animated bounceOut'
            },
            maxWidth: '500px', // Aumentar el ancho de la notificación
            fontSize: '18px',  // Tamaño de fuente más grande
            progressBar: true, // Barra de progreso
            closeWith: ['click'], // Cerrar al hacer clic
        }).show();

        $.ajax({                
            type: 'POST',
            url: 'Consultas/ValidadorUsuario.php',
            data: data,
            success: function(response) {                         
                if (response.trim() === 'ok') {
                    new Noty({
                        text: '🌊 ¡Bienvenido al arrecife! Redirigiendo... 🐠',
                        type: 'success',
                        layout: 'topCenter',
                        timeout: 4000,
                        theme: 'relax',
                        animation: {
                            open: 'animated fadeIn',
                            close: 'animated fadeOut'
                        },
                        maxWidth: '500px', // Aumentar el ancho de la notificación
                        fontSize: '20px',  // Tamaño de fuente más grande
                        progressBar: true,
                        closeWith: ['click'], // Cerrar al hacer clic
                        buttons: [
                            Noty.button('¡Listo!', 'btn btn-success', function() {
                                window.location.href = "https://doctorpez.mx/PuntoDeVenta/ControlPOS";
                            })
                        ]
                    }).show();
                } else {
                    new Noty({
                        text: '⚠️ ¡Error! Usuario o contraseña incorrectos. 🦀',
                        type: 'error',
                        layout: 'topCenter',
                        timeout: 4000,
                        theme: 'sunset',
                        animation: {
                            open: 'animated shake',
                            close: 'animated bounceOut'
                        },
                        maxWidth: '500px', // Aumentar el ancho de la notificación
                        fontSize: '20px',  // Tamaño de fuente más grande
                        progressBar: true,
                        closeWith: ['click'], // Cerrar al hacer clic
                        buttons: [
                            Noty.button('Reintentar', 'btn btn-danger', function() {
                                // Lógica para intentar nuevamente
                            })
                        ]
                    }).show();

                    // Redirigir en caso de error (opcional)
                    setTimeout(function() {
                        window.location.href = "https://doctorpez.mx/PuntoDeVenta/"; // O cualquier URL donde quieras redirigir
                    }, 1000);
                }
            }
        });
        return false; // Evita que el formulario se envíe de forma convencional
    }

    // Inicializar AOS para animaciones
    AOS.init();
});
