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
            // Mostrar confirmación antes de enviar
            Swal.fire({
                icon: 'warning',
                title: '¿Está seguro de realizar el corte?',
                text: 'Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Sí, realizar corte',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si el usuario confirma, enviamos la información
                    $.ajax({
                        type: 'POST',
                        url: "Controladores/RegistraCorte.php",
                        data: $(form).serialize(),
                        cache: false,
                        success: function (data) {
                            var response = JSON.parse(data);

                        Swal.fire({
                            if (response.statusCode === 200) {
                                // Enviar los datos al ticket
                                $.ajax({
                                    type: 'POST',
                                    url: "http://localhost/ticket/GenerarTicketCorte.php",
                                    data: $(form).serialize(),
                                    cache: false,
                                    success: function (ticketResponse) {
                                        console.log("Datos enviados al ticket correctamente:", ticketResponse);
                                    },
                                    error: function (xhr, status, error) {
                                        console.log("Error al generar el ticket:", xhr.responseText);
                                    }
                                });

                                // Mostrar mensaje de éxito
                                Swal.fire({
                                    icon: 'success',
                                    title: 'El corte se ha realizado con éxito!',
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
                } else {
                    // Si el usuario cancela, no hacemos nada
                    Swal.fire({
                        icon: 'info',
                        title: 'Corte cancelado',
                        text: 'El proceso de corte ha sido cancelado.',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            });
        },
    });
});
