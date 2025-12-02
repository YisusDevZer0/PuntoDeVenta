$(document).ready(function () {
  console.log("Document is ready"); 
  
  
  var valoresTabla = [];
  var boletaTotal = 0;
  var cambiocliente = "";
  var clienteInputValue = "";
  var formaPagoSeleccionada = "";
  var TicketVal = "";
  var TicketRifa = "";
  var Vendedor = "";

  function validarFormulario() {
    var clienteInput = document.getElementById("clienteInput");
    if (clienteInput.value === "") {
      clienteInput.setCustomValidity("Este campo es obligatorio");
    } else {
      clienteInput.setCustomValidity("");
    }
  }

  function validarTicket(ticket) {
    valoresTabla = [];

    $.ajax({
      type: 'POST',
      url: "Controladores/ValidarTicket.php",
      data: { NumeroDeTickeT: ticket },
      cache: false,
      success: function (data) {
        var response = JSON.parse(data);
        if (response.existe) {
          var numeroTicket = $("#Folio_Ticket").val();
          var mensajeError = 'El número de ticket "' + numeroTicket + '" ya existe, por favor actualize la pantalla. <br><br> ';
          Swal.fire({
            icon: 'error',
            title: 'Ticket duplicado',
            html: mensajeError + "si el problema persiste por favor contacte a soporte",
          });
        } else {
          $('#tablaConfirmaciondatos tbody').empty();
          $('#tablaAgregarArticulos tbody tr').each(function() {
            var codigoBarras = $(this).find('.codigo-barras-input').val();
            var descripcionProducto = $(this).find('.descripcion-producto-input').val();
            var cantidadVendida = $(this).find('.cantidad-vendida-input').val();
            var descuentorealizado = $(this).find('.descuento-aplicado').val();
            var preciounitario = $(this).find('.preciou-input').val();
            var importeventa = $(this).find('.importe').val();
            valoresTabla.push({
              codigoBarras: codigoBarras,
              descripcionProducto: descripcionProducto,
              cantidadVendida: cantidadVendida,
              descuentorealizado: descuentorealizado,
              preciounitario: preciounitario,
              importeventa: importeventa
            });
          });

          boletaTotal = $('#boleta_total').text();
          cambiocliente = $('#Vuelto').text();
           clienteInputValue = $('#clienteInput').val();
           formaPagoSeleccionada = $('#selTipoPago option:selected').text();
           TicketVal = $("#Folio_Ticket").val();
           Vendedor = $("#VendedorFarma").val();

          var mensajeConfirmacion = '<div class="dataTable">';
          mensajeConfirmacion += '<table id="tablaConfirmacion">';
          mensajeConfirmacion += '<thead><tr><th>Total de venta</th><th>Cambio del cliente</th><th>Nombre del cliente</th><th>Forma de pago</th></tr></thead>';
          mensajeConfirmacion += '<tbody>';
          mensajeConfirmacion += '<tr><td>' + boletaTotal + '</td><td>' + cambiocliente + '</td><td>' + clienteInputValue + '</td><td>' + formaPagoSeleccionada + '</td></tr>';
          mensajeConfirmacion += '</tbody>';
          mensajeConfirmacion += '</table>';
          mensajeConfirmacion += '</div><br><br>';
          mensajeConfirmacion += '<div class="dataTable">';
          mensajeConfirmacion += '<table id="tablaConfirmaciondatos">';
          mensajeConfirmacion += '<thead><tr><th>Código de Barras</th><th>P.U</th><th>Producto</th><th>Cantidad Vendida</th><th>Descuento aplicado</th><th>Importe</th></tr></thead>';
          mensajeConfirmacion += '<tbody>';

          for (var i = 0; i < valoresTabla.length; i++) {
            mensajeConfirmacion += '<tr>';
            mensajeConfirmacion += '<td>' + valoresTabla[i].codigoBarras + '</td>';
            mensajeConfirmacion += '<td>' + valoresTabla[i].preciounitario + '</td>';
            mensajeConfirmacion += '<td>' + valoresTabla[i].descripcionProducto + '</td>';
            mensajeConfirmacion += '<td>' + valoresTabla[i].cantidadVendida + '</td>';
            mensajeConfirmacion += '<td>' + valoresTabla[i].descuentorealizado + '</td>';
            mensajeConfirmacion += '<td>' + valoresTabla[i].importeventa + '</td>';
            mensajeConfirmacion += '</tr>';
          }

          mensajeConfirmacion += '</tbody>';
          mensajeConfirmacion += '</table>';
          mensajeConfirmacion += '</div>';

          Swal.fire({
            icon: 'warning',
            title: 'Verifique los datos de la venta',
            html: mensajeConfirmacion,
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            width: '800px',
            height: '800px',
          }).then((result) => {
            if (result.isConfirmed) {
              submitForm();
            }
          });

          $('#tablaConfirmacion').DataTable({
            Language: {
              "lengthMenu": "Mostrar _MENU_ registros",
              "zeroRecords": "No se encontraron resultados",
              "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
              "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
              "infoFiltered": "(filtrado de un total de _MAX_ registros)",
              "sSearch": "Buscar:",
              "oPaginate": {
                "sFirst": "Primero",
                "sLast":"Último",
                "sNext":"Siguiente",
                "sPrevious": "Anterior"
              },
              "sProcessing":"Procesando...",
            }
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Error en la petición',
          text: 'No se pudo verificar el ticket. Por favor, inténtalo de nuevo.',
        });
      }
    });
  }

  function submitForm() {
    $.ajax({
      type: 'POST',
      url: "Controladores/RegistroDeVentasSucursales.php",
      data: $('#VentasAlmomento').serialize(),
      cache: false,
      beforeSend: function () {
        $("#submit_registro").html("Verificando datos... <span class='fa fa-refresh fa-spin' role='status' aria-hidden='true'></span>");
      },
      success: function (data) {
        var response = JSON.parse(data);
        if (response.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Venta realizada con éxito',
            showConfirmButton: false,
            timer: 2000,
            didOpen: () => {
              Swal.showLoading();
              setTimeout(() => {
                Swal.hideLoading();
                Swal.fire({
                  icon: 'info',
                  title: 'Generando ticket...',
                  showConfirmButton: false,
                });

                 // SOLUCIÓN SIMPLE Y DIRECTA: Construir el folio con el prefijo del ticket + número de la tabla
                 var folioNumero = $("input[name='NumeroDeTickeRifa[]']").first().val() || "1";
                 var prefijoTicket = TicketVal.substring(0, 3); // Extraer TEA de TEA9852
                 var folioRifaFinal = prefijoTicket + folioNumero; // TEA + 15 = TEA15
                 
                 console.log("=== FOLIO RIFA FINAL ===");
                 console.log("Folio número tabla:", folioNumero);
                 console.log("Prefijo del ticket:", prefijoTicket);
                 console.log(">>> FOLIO RIFA ENVIADO:", folioRifaFinal);
                
                 var encodedValoresTabla = encodeURIComponent(JSON.stringify(valoresTabla));
                 var encodedBoletaTotal = encodeURIComponent(boletaTotal);
                 var encodedCambioCliente = encodeURIComponent(cambiocliente);
                 var encodedClienteInputValue = encodeURIComponent(clienteInputValue);
                 var encodedFormaPagoSeleccionada = encodeURIComponent(formaPagoSeleccionada);
                 var encodedTicketVal = encodeURIComponent(TicketVal);
                 var encodedTicketRifa = encodeURIComponent(folioRifaFinal);
                 var encodedVendedor = encodeURIComponent(Vendedor);

                var data = 'BoletaTotal=' + encodedBoletaTotal +
                           '&CambioCliente=' + encodedCambioCliente +
                           '&ClienteInputValue=' + encodedClienteInputValue +
                           '&FormaPagoSeleccionada=' + encodedFormaPagoSeleccionada +
                           '&TicketVal=' + encodedTicketVal +
                           '&TicketRifa=' + encodedTicketRifa +
                           '&Vendedor=' + encodedVendedor +
                           '&ValoresTabla=' + encodedValoresTabla;

                 // Debug: Mostrar datos enviados
                 console.log("=== DATOS ENVIADOS AL TICKET ===");
                 console.log("TicketVal:", TicketVal);
                 console.log(">>> TicketRifa ENVIADO:", folioRifaFinal);
                 console.log("Vendedor:", Vendedor);
                 console.log("BoletaTotal:", boletaTotal);

                $.ajax({
                  type: 'POST',
                  url: 'http://localhost/ticket/TicketVenta.php',
                  data: data,
                  success: function(response) {
                    console.log("=== RESPUESTA DEL SERVIDOR ===");
                    console.log("Response from ticket generation:", response);
                    setTimeout(function() {
                      location.reload(); // Recargar después de 2 segundos para ver los logs
                    }, 2000);
                  },
                  error: function(error) {
                    console.error("=== ERROR EN LA PETICIÓN ===");
                    console.error("Error generating ticket:", error);
                  }
                });

              }, 1500);
            }
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Algo salió mal, por favor inténtalo de nuevo',
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

    return false;
  }

  // Prevenir el submit por defecto del formulario
  $("#VentasAlmomento").on('submit', function(e) {
    e.preventDefault();
    e.stopPropagation();
    return false;
  });

  $("#VentasAlmomento").validate({
    rules: {
      CodBarras: {
        required: true,
      },
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
    submitHandler: function (form) {
      validarFormulario();
      var ticket = $("#Folio_Ticket").val();
      validarTicket(ticket);
      return false;
    },
  });
});
