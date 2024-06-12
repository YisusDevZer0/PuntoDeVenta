$(document).ready(function () {
    // Validar el formulario
    $("#CortesDeCajaFormulario").validate({
        rules: {
            Sucursal: {
                required: true,
            },
            Turno: {
                required: true,
            },
            Cajero: {
                required: true,
            },
            VentaTotal: {
                required: true,
            },
            TicketVentasTotal: {
                required: true,
            },
            EfectivoTotal: {
                required: true,
            },
            TarjetaTotal: {
                required: true,
            },
            CreditosTotales: {
                required: true,
            },
        },
        messages: {
            Sucursal: {
                required: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campo requerido',
                        text: 'El campo Sucursal es necesario',
                    });
                },
            },
            Turno: {
                required: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campo requerido',
                        text: 'El campo Turno es necesario',
                    });
                },
            },
            // Repite esto para cada campo
        },
        submitHandler: function () {
            $.ajax({
                type: 'POST',
                url: "Controladores/RegistraCorte.php",
                data: $('#CortesDeCajaFormulario').serialize(),
                cache: false,
                success: function (data) {
                    var response = JSON.parse(data);

                    if (response.statusCode === 200) {
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
                            text: response.error || 'Error inesperado',
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
        },
    });
});
