$(document).ready(function () {
    // Función para obtener el token CSRF
    function obtenerTokenCSRF() {
        var token = ''; // Tu lógica para obtener el token CSRF aquí
        return token;
    }

    // Evento submit del formulario
    $('#NewTypeUser').submit(function (e) {
        e.preventDefault();

        // Obtener el token CSRF
        var csrfToken = obtenerTokenCSRF();

        // Crear objeto FormData con los datos del formulario
        var formData = new FormData(this);

        // Agregar el token CSRF al formulario
        formData.append('csrf_token', csrfToken);

        // Realizar la solicitud Ajax
        $.ajax({
            type: 'POST',
            url: 'Controladores/NuevoTipoDeusuario.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#userTable').DataTable().ajax.reload();
                    $('#myModal').modal('hide');
                } else {
                    mostrarError('Error al agregar nuevo usuario: ' + data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                mostrarError('Error en la solicitud: ' + textStatus + ' - ' + errorThrown);
            }
        });
    });

    // Función para mostrar mensajes de error de manera más elegante
    function mostrarError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje
        });
    }
});
