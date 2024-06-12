$(document).ready(function () {
 
    // Validar el formulario
    $("#FormDeCortes").validate({
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
                    url: "Controladores/RegistraCorte.php",
                    data: $('#FormDeCortes').serialize(),
                    cache: false,
                    success: function (data) {
                        var response = JSON.parse(data);

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Venta realizada con éxito',
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
