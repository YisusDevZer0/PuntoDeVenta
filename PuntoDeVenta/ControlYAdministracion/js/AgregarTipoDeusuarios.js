$(document).ready(function () {
    // Evento submit del formulario
    $('#NewTypeUser').submit(function (e) {
        e.preventDefault();

        // Crear objeto FormData con los datos del formulario
        var formData = new FormData(this);

        // Realizar la solicitud Ajax
        $.ajax({
            type: 'POST',
            url: 'Controladores/NuevoTipoDeusuarios.php',
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
