$(document).ready(function() {
    // Agregar un método de validación personalizado si es necesario
    $.validator.addMethod("validateID", function(value, element) {
        return this.optional(element) || /^[0-9]+$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar números");

    $("#EliminaDatosDeProductos").validate({
        rules: {
            ID_Prod_POS: {
                required: true,
                validateID: true
            }
        },
        messages: {
            ID_Prod_POS: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> ID requerido",
                validateID: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar números"
            }
        },
        submitHandler: function(form) {
            $.ajax({
                type: 'POST',
                url: "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/EliminarProducto.php",
                data: $(form).serialize(),
                cache: false,
                beforeSend: function() {
                    $("#confirmDelete").html("Eliminando... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
                },
                success: function(response) {
                    $("#confirmDelete").html("Eliminar");
                    if (response.includes("Producto eliminado correctamente")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'El producto ha sido eliminado con éxito.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload(); // Recargar la página
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al eliminar el producto: ' + response,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr);
                    $("#confirmDelete").html("Eliminar");
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