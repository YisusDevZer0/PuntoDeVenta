$(document).ready(function () {
    // Métodos de validación personalizados
    $.validator.addMethod("Sololetras", function (value, element) {
        return this.optional(element) || /^[a-zA-ZÀ-ÿñÑ\s]+$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar letras!");

    $.validator.addMethod("Telefonico", function (value, element) {
        return this.optional(element) || /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/.test(value);
    }, "<i class='fas fa-exclamation-triangle' style='color:red'></i> Solo debes ingresar números!");

    // Función para generar vista previa del ticket
    function generarVistaPreviaYConfirmar() {
        // Recopilamos los datos de la tabla #tablaAgregarArticulos
        var tablaDatos = [];
        $('#tablaAgregarArticulos tbody tr').each(function () {
            var fila = {
                codigo: $(this).find('.codigo input').val(),
                producto: $(this).find('.descripcion textarea').val(),
                cantidad: $(this).find('.cantidad input').val(),
                precio: $(this).find('.preciofijo input').val(),
                tipoMov: $(this).find('.TipoMovimiento input').val(),
                fecha: $(this).find('.Fecha input').val()
            };
            tablaDatos.push(fila);
        });

        // Construimos la tabla de confirmación
        var tablaConfirmacion = `
            <div class="dataTable">
                <table id="tablaConfirmacion">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Tipo de mov</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>`;

        tablaDatos.forEach(function (fila) {
            tablaConfirmacion += `
                <tr>
                    <td>${fila.codigo}</td>
                    <td>${fila.producto}</td>
                    <td>${fila.cantidad}</td>
                    <td>${fila.precio}</td>
                    <td>${fila.tipoMov}</td>
                    <td>${fila.fecha}</td>
                </tr>`;
        });

        tablaConfirmacion += `
                    </tbody>
                </table>
            </div>`;

        // Mostramos la confirmación con SweetAlert
        Swal.fire({
            icon: 'warning',
            title: 'Verifique los datos del traspaso o nota de crédito',
            html: tablaConfirmacion,
            showCancelButton: true,
            confirmButtonText: 'Confirmar datos',
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
            data: $('#TraspasosNotasALMomento').serialize(),
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
                        enviarDatosTicket();
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
        var data = $('#TraspasosNotasALMomento').serialize();

        $.ajax({
            type: 'POST',
            url: 'http://localhost/ticket/TicketTraspasoONotaDeCredito.php',
            data: data,
            success: function (response) {
                console.log("Ticket generado exitosamente:", response);
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
            generarVistaPreviaYConfirmar();
        }
    });
});
