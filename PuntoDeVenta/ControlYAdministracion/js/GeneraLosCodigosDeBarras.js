$(document).ready(function ($) {
    // Método de validación para solo letras
    $.validator.addMethod("soloLetras", function (value, element) {
        return this.optional(element) || /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$/.test(value);
    }, "Solo debes ingresar letras");

    // Configuración de la validación del formulario
    $("#ActualizaDatosDeProductos").validate({
        rules: {
            Cod_BarraActualiza: {
                required: true,
                minlength: 5, // Ajusta según sea necesario
                maxlength: 60
            }
        },
        messages: {
            Cod_BarraActualiza: {
                required: "Código de barra es requerido",
                minlength: "El código debe tener al menos 5 caracteres",
                maxlength: "El código no puede tener más de 60 caracteres"
            }
        },
        submitHandler: function (form) {
            $.ajax({
                type: 'POST',
                url: 'Controladores/actualiza_producto.php',
                data: $(form).serialize(),
                dataType: 'json', // Especifica que la respuesta debe ser JSON
                beforeSend: function () {
                    Swal.fire({
                        title: 'Enviando...',
                        text: 'Por favor, espera mientras se procesa la actualización.',
                        icon: 'info',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                success: function (dataResult) {
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
