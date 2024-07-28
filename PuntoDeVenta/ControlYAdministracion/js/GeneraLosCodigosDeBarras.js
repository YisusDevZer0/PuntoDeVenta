$(document).ready(function () {
    $.validator.addMethod("soloLetras", function (value, element) {
        return this.optional(element) || /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

    $.validator.addMethod("codigoBarra", function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Código de barra inválido!");

    $("#ActualizaDatosDeProductos").validate({
       
        messages: {
            Cod_BarraActualiza: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Código de barra es requerido",
                minlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> El código debe tener al menos 5 caracteres",
                maxlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> El código no puede tener más de 60 caracteres"
            },
            ID_Prod_POSAct: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> ID del producto es requerido"
            }
        },
        submitHandler: function (form) {
            $.ajax({
                type: 'POST',
                url: 'Controladores/actualiza_producto.php',
                data: $(form).serialize(),
                dataType: 'json', // Asegura que la respuesta sea JSON
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
                    // dataResult es ya un objeto JSON, no necesita JSON.parse
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
