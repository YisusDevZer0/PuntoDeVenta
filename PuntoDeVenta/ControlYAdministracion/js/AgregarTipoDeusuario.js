$(document).ready(function () {
    $('#NewTypeUser').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        // Imprime el valor del token y otros datos en la consola
        console.log(formData);

        $.ajax({
            type: 'POST',
            url: 'Controladores/NuevoTipoDeusuario.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', // Indicamos que esperamos una respuesta en formato JSON
        })
        .done(function (data) {
            if (data.success) {
                $('#userTable').DataTable().ajax.reload();
                $('#myModal').modal('hide');
            } else {
                mostrarError('Error al agregar nuevo usuario: ' + data.message);
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            mostrarError('Error en la solicitud: ' + textStatus + ' - ' + errorThrown);
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
