$(document).ready(function($) {
    $.validator.addMethod("Sololetras", function(value, element) {
        return this.optional(element) || /[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]*$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras");
    
    // Otros métodos de validación omitidos por brevedad

    $("#ActualizaDatosDeProductos").validate({
        rules: {
            Cod_Barra: {
                required: true
            },
           
            // Otros campos omitidos por brevedad
        },
        messages: {
            Cod_Barra: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido"
            },
           
            // Otros mensajes omitidos por brevedad
        },
        submitHandler: function(form) {
            $.ajax({
                type: 'POST',
                url: "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ActualizaDataDeProductos.php",
                data: $(form).serialize(),
                cache: false,
                beforeSend: function() {
                    $("#submit").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
                },
                success: function(response) {
                    $("#submit").html("Aplicar cambios <i class='fas fa-check'></i>");
                    if (response.includes("Registro actualizado correctamente")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Los datos del producto han sido actualizados con éxito.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload(); // Recargar la página
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al actualizar los datos: ' + response,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr);
                    $("#submit").html("Aplicar cambios <i class='fas fa-check'></i>");
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un error al procesar la solicitud. Por favor, inténtalo de nuevo.',
                    });
                }
            });
            return false;
        }
    });
});
