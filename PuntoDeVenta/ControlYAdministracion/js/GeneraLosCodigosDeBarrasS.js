$(document).ready(function () {
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
                        try {
                            var response = JSON.parse(data);

                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Datos guardados con éxito!',
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
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error en la respuesta',
                                text: 'La respuesta del servidor no es válida.',
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
