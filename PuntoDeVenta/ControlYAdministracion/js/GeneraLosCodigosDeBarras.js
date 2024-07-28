$(document).ready(function () {
    // Agregar los métodos de validación personalizados
    function validarFormulario() {
        var clienteInput = $("#clienteInput");

        if (clienteInput.val() === "") {
            Swal.fire({
                icon: 'error',
                title: 'Campo requerido',
                text: 'El nombre del cliente es necesario',
            });
            return false;
        }
        return true;
    }

    // Validar el formulario
    $("#ActualizaDatosDeProductos").validate({
        rules: {
            clienteInput: {
                required: true,
            },
        },
        messages: {
            clienteInput: {
                required: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campo requerido',
                        text: 'El nombre del cliente es necesario',
                    });
                },
            },
        },
        submitHandler: function () {
            if (validarFormulario()) {
                $.ajax({
                    type: 'POST',
                    url: 'Controladores/actualiza_producto.php',
                    data: $('#ActualizaDatosDeProductos').serialize(),
                    cache: false,
                    success: function (data) {
                        var response = JSON.parse(data);

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Datos guardados con exito!',
                                showConfirmButton: false,
                                timer: 2000,
                                didOpen: () => {
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                },
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Algo salió mal',
                                text: response.message,
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en la petición',
                            text: 'No se pudieron guardar los datos. Por favor, inténtalo de nuevo.',
                        });
                    }
                });
            }
        },
    });
});

