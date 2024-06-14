$(document).ready(function () {
    // Validar el formulario
    $("#FormDeCortes").validate({
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
                required: 'El campo Sucursal es necesario',
            },
            Turno: {
                required: 'El campo Turno es necesario',
            },
            Cajero: {
                required: 'El campo Cajero es necesario',
            },
            VentaTotal: {
                required: 'El campo VentaTotal es necesario',
            },
            TicketVentasTotal: {
                required: 'El campo TicketVentasTotal es necesario',
            },
            EfectivoTotal: {
                required: 'El campo EfectivoTotal es necesario',
            },
            TarjetaTotal: {
                required: 'El campo TarjetaTotal es necesario',
            },
            CreditosTotales: {
                required: 'El campo CreditosTotales es necesario',
            },
        },
        submitHandler: function (form) {
            $.ajax({
                type: 'POST',
                url: "Controladores/RegistraCorte.php",
                data: $(form).serialize(),
                cache: false,
                success: function (data) {
                    var response = JSON.parse(data);

                    if (response.statusCode === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'El corte se ha realizado con exito!',
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
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la petición',
                        text: 'No se pudieron guardar los datos. Por favor, inténtalo de nuevo.',
                    });
                    console.log(xhr.responseText); // Para ver la respuesta del servidor
                }
            });
        },
    });
});
