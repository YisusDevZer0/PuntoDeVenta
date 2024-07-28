$(document).ready(function ($) {
    $.validator.addMethod("soloLetras", function (value, element) {
        return this.optional(element) || /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras");

    // Añadir más métodos de validación si es necesario

    $("#ActualizaDatosDeProductos").validate({
        rules: {
            Cod_BarraActualiza: {
                required: true,
                minlength: 5, // Ajusta según sea necesario
                maxlength: 60
            },
            Tipo_Servicio: {
                required: true
            }
        },
        messages: {
            Cod_BarraActualiza: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Código de barra es requerido",
                minlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> El código debe tener al menos 5 caracteres",
                maxlength: "<i class='fas fa-exclamation-triangle' style='color:red'></i> El código no puede tener más de 60 caracteres"
            },
           
        },
        submitHandler: function (form) {
            $.ajax({
                type: 'POST',
                url: 'Controladores/actualiza_producto.php',
                data: $(form).serialize(),
                beforeSend: function () {
                    $("#submit").html("Enviando... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
                },
                success: function (response) {
                    const dataResult = JSON.parse(response);
                    if (dataResult.success) {
                        $("#submit").html("Actualizado <i class='fas fa-check'></i>");
                        setTimeout(function () {
                            location.reload(); // Recargar la página completa después de 3 segundos
                        }, 3000);
                    } else {
                        alert("Error al actualizar: " + dataResult.message);
                        $("#submit").html("Intentar de nuevo <i class='fas fa-refresh'></i>");
                    }
                },
                error: function () {
                    alert("Error en la solicitud.");
                    $("#submit").html("Intentar de nuevo <i class='fas fa-refresh'></i>");
                }
            });
        }
    });
});
