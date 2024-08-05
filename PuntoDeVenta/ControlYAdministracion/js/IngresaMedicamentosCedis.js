$(document).ready(function() {
    // Agregar un método de validación personalizado si es necesario
    $.validator.addMethod("validateID", function(value, element) {
        return this.optional(element) || /^[0-9]+$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar números");

    $("#FormularioProducto").validate({
        rules: {
            ID_Prod_Cedis: {
                required: true,
                validateID: true
            },
            numeroFactura: {
                required: true
            },
            cantidadPiezas: {
                required: true,
                digits: true
            }
            // Agrega otras reglas según sea necesario
        },
        messages: {
            ID_Prod_Cedis: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> ID requerido",
                validateID: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar números"
            },
            numeroFactura: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Número de factura requerido"
            },
            cantidadPiezas: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Cantidad de piezas requerida",
                digits: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo se permiten números"
            }
            // Agrega otros mensajes según sea necesario
        },
        submitHandler: function(form) {
            $.ajax({
                type: 'POST',
                url: "Controladores/IngresaMedicamentosAlCedis.php", // Cambia esto a la ruta correcta
                data: $(form).serialize(),
                cache: false,
                beforeSend: function() {
                    $("#FormularioProducto button[type='submit']").html("Guardando... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
                },
                success: function(response) {
                    $("#FormularioProducto button[type='submit']").html("Guardar");
                    if (response.includes("Producto insertado correctamente")) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'El producto ha sido insertado con éxito.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload(); // Recargar la página
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al insertar el producto: ' + response,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr);
                    $("#FormularioProducto button[type='submit']").html("Guardar");
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
