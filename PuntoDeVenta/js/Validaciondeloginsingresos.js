// Configuración de notificaciones
const NOTY_CONFIG = {
    layout: 'topCenter',
    theme: 'metroui',
    timeout: 3000,
    closeWith: ['click', 'button']
};

// Mensajes del sistema
const MESSAGES = {
    validating: '🐟 Validando tus credenciales, por favor espera...',
    success: '🌊 ¡Bienvenido al arrecife! Redirigiendo... 🐠',
    error: '⚠️ ¡Error! Usuario o contraseña incorrectos. 🦀'
};

// URLs del sistema
const URLS = {
    validator: 'Consultas/ValidadorUsuario.php',
    redirect: 'https://doctorpez.mx/PuntoDeVenta/ControlPOS'
};

/**
 * Muestra una notificación usando Noty
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (success, error, info)
 */
function showNotification(message, type) {
    new Noty({
        ...NOTY_CONFIG,
        text: message,
        type: type
    }).show();
}

/**
 * Maneja el envío del formulario
 * @param {Event} e - Evento del formulario
 */
function handleSubmit(e) {
    e.preventDefault();
    showNotification(MESSAGES.validating, 'info');

    const formData = $(e.target).serialize();

    $.ajax({
        type: 'POST',
        url: URLS.validator,
        data: formData,
        cache: false,
        success: function(response) {
            if (response.trim() === 'ok') {
                showNotification(MESSAGES.success, 'success');
                setTimeout(() => window.location.href = URLS.redirect, 2000);
            } else {
                showNotification(MESSAGES.error, 'error');
            }
        },
        error: function() {
            showNotification('🔥 Error de conexión. Intenta nuevamente.', 'error');
        }
    });
}

// Inicialización cuando el documento está listo
$(document).ready(function() {
    // Inicializar componentes
    $('.modal').modal();
    AOS.init();

    // Configurar validación del formulario
    $("#login-form").validate({
        rules: {
            password: {
                required: true,
                minlength: 6
            },
            user_email: {
                required: true,
                email: true
            }
        },
        messages: {
            password: {
                required: "<i class='fas fa-times'></i> Se requiere tu contraseña",
                minlength: "<i class='fas fa-times'></i> La contraseña debe tener al menos 6 caracteres"
            },
            user_email: {
                required: "<i class='fas fa-times'></i> Ingresa tu correo por favor",
                email: "<i class='fas fa-times'></i> Ingresa un correo válido"
            }
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.input-field').append(error);
        },
        highlight: function(element) {
            $(element).addClass('invalid').removeClass('valid');
        },
        unhighlight: function(element) {
            $(element).addClass('valid').removeClass('invalid');
        },
        submitHandler: handleSubmit
    });
});
