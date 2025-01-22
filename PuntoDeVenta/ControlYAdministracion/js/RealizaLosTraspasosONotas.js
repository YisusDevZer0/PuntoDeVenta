$('document').ready(function () {
    // Métodos de validación personalizados
    $.validator.addMethod("Sololetras", function (value, element) {
        return this.optional(element) || /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

    $.validator.addMethod("Telefonico", function (value, element) {
        return this.optional(element) || /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar números!");

    // Función para generar vista previa del ticket
    function generarVistaPreviaYConfirmar() {
        // Datos específicos para mostrar en la confirmación
        var boletaTotal = $('#boleta_total').text();
        var cambiocliente = $('#Vuelto').text();
        var clienteInputValue = $('#clienteInput').val();
        var formaPagoSeleccionada = $('#selTipoPago option:selected').text();
        var TicketVal = $("#Folio_Ticket").val();
        var Vendedor = $("#VendedorFarma").val();

        var mensajeConfirmacion = `
            <div class="dataTable">
                <table id="tablaConfirmacion">
                    <thead>
                        <tr>
                            <th>Total de venta</th>
                            <th>Cambio del cliente</th>
                            <th>Nombre del cliente</th>
                            <th>Forma de pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>${boletaTotal}</td>
                            <td>${cambiocliente}</td>
                            <td>${clienteInputValue}</td>
                            <td>${formaPagoSeleccionada}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;

        Swal.fire({
            icon: 'warning',
            title: 'Verifique los datos de la venta',
            html: mensajeConfirmacion,
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            width: '800px',
        }).then((result) => {
            if (result.isConfirmed) {
                guardarEnBaseDeDatos();
            }
        });
    }

    // Función para guardar datos en la base de datos
    function guardarEnBaseDeDatos() {
        $.ajax({
            type: 'POST',
            url: "Controladores/TraspasoYNotasAlmomento.php",
            data: $('#TraspasosNotasALMomento').serialize(), // Enviamos todos los datos del formulario
            cache: false,
            beforeSend: function () {
                $("#submit_registro").html("Guardando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
            },
            success: function (response) {
                let res = JSON.parse(response);
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Datos guardados correctamente. Generando ticket...',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        enviarDatosTicket(); // Llamada a la función para generar el ticket
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron guardar los datos. Por favor, inténtalo de nuevo.',
                });
            }
        });
    }

    // Función para enviar los datos del ticket
    function enviarDatosTicket() {
        var data = $('#TraspasosNotasALMomento').serialize(); // Serializamos todos los datos del formulario

        $.ajax({
            type: 'POST',
            url: 'http://localhost/ticket/TicketTraspasoONotaDeCredito.php',
            data: data,
            success: function (response) {
                console.log("Ticket generado exitosamente:", response);
                location.reload();
            },
            error: function (error) {
                console.error("Error al generar el ticket:", error);
            }
        });
    }

    // Configuración del validador
    $("#TraspasosNotasALMomento").validate({
        rules: {
            codbarras: {
                required: true,
            },
        },
        messages: {
            codbarras: {
                required: "<i class='fas fa-exclamation-triangle' style='color:red'></i> Dato requerido",
            },
        },
        submitHandler: function () {
            generarVistaPreviaYConfirmar(); // Generamos la vista previa antes de enviar
        }
    });
});
