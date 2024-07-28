$(document).ready(function ($) {
    $.validator.addMethod("soloLetras", function (value, element) {
        return this.optional(element) || /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$/.test(value);
    }, "Solo debes ingresar letras");

    $("#ActualizaDatosDeProductos").validate({
      
        submitHandler: function (form) {
            $.ajax({
                type: 'POST',
                url: 'Controladores/actualiza_producto.php',
                data: $(form).serialize(),
                beforeSend: function () {
                    Swal.fire({
                        title: 'Enviando...',
                        text: 'Por favor, espera mientras se procesa la actualización.',
                        icon: 'info',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                success: function (response) {
                    const dataResult = JSON.parse(response);
                    if (dataResult.success) {
                        Swal.fire({
                            title: 'Actualizado',
                            text: dataResult.message,
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            location.reload(); // Recargar la página después de mostrar el mensaje de éxito
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: dataResult.message,
                            icon: 'error',
                            confirmButtonText: 'Intentar de nuevo'
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error en la solicitud.',
                        icon: 'error',
                        confirmButtonText: 'Intentar de nuevo'
                    });
                }
            });
        }
    });
});
