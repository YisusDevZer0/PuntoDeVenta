$(document).ready(function () {
  var valoresTabla = [];
  var boletaTotal = 0;
  var cambiocliente = "";
  var clienteInputValue = "";
  var formaPagoSeleccionada = "";
  var TicketVal = "";
  var TicketRifa = "";
  var Vendedor = "";
  // Variables de sorteo
  var sorteoId = 0;
  var sorteoClienteId = 0;
  var sorteoTelefono = "";
  var sorteoFechaNac = "";
  var sorteoFolioRifa = "";
  var sorteoParticipa = 1;

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

          // Capturar datos de sorteo si existen
          if ($('#sorteoId').length && $('#sorteoId').val() != '0') {
            sorteoId = $('#sorteoId').val();
            sorteoClienteId = $('#sorteoClienteId').val() || '0';
            sorteoTelefono = $('#sorteoTelefono').val() || '';
            sorteoFechaNac = $('#sorteoFechaNac').val() || '';
            sorteoFolioRifa = $('#sorteoFolioRifa').val() || '';
            sorteoParticipa = $('#chkNoParticipa').is(':checked') ? 0 : 1;
          }

          var mensajeConfirmacion = '<div class="dataTable">';
          // Mostrar info de sorteo si hay sorteo activo
          if (sorteoId > 0) {
            var folioSorteo = ($('#sorteoPrefijoFolio').val() || '') + sorteoFolioRifa;
            var participaText = sorteoParticipa ? '<span style="color:green;font-weight:bold;">Sí participa</span>' : '<span style="color:red;font-weight:bold;">No participa</span>';
            mensajeConfirmacion += '<div style="background:#fff3cd;padding:8px;border-radius:6px;margin-bottom:10px;border:1px solid #ffc107;">';
            mensajeConfirmacion += '<strong><i class="fa-solid fa-gift"></i> Sorteo:</strong> Folio <strong>' + folioSorteo + '</strong> | ';
            mensajeConfirmacion += 'Tel: ' + (sorteoTelefono || 'N/A') + ' | F.Nac: ' + (sorteoFechaNac || 'N/A') + ' | ' + participaText;
            mensajeConfirmacion += '</div>';
          }
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

                var folioNumero = $("input[name='NumeroDeTickeRifa[]']").first().val() || "1";
                var prefijoTicket = TicketVal.substring(0, 3);
                var folioRifaFinal = prefijoTicket + folioNumero;
                
                var encodedValoresTabla = encodeURIComponent(JSON.stringify(valoresTabla));
                var encodedBoletaTotal = encodeURIComponent(boletaTotal);
                var encodedCambioCliente = encodeURIComponent(cambiocliente);
                var encodedClienteInputValue = encodeURIComponent(clienteInputValue);
                var encodedFormaPagoSeleccionada = encodeURIComponent(formaPagoSeleccionada);
                var encodedTicketVal = encodeURIComponent(TicketVal);
                var encodedTicketRifa = encodeURIComponent(folioRifaFinal);
                var encodedVendedor = encodeURIComponent(Vendedor);

                // Datos del sorteo para el ticket
                var sorteoFolioCompleto = (sorteoId > 0) ? ($('#sorteoPrefijoFolio').val() || '') + sorteoFolioRifa : folioRifaFinal;

                var data = 'BoletaTotal=' + encodedBoletaTotal +
                           '&CambioCliente=' + encodedCambioCliente +
                           '&ClienteInputValue=' + encodedClienteInputValue +
                           '&FormaPagoSeleccionada=' + encodedFormaPagoSeleccionada +
                           '&TicketVal=' + encodedTicketVal +
                           '&TicketRifa=' + encodedTicketRifa +
                           '&Vendedor=' + encodedVendedor +
                           '&ValoresTabla=' + encodedValoresTabla;

                // Agregar datos de sorteo al payload del ticket
                if (sorteoId > 0) {
                  data += '&SorteoId=' + encodeURIComponent(sorteoId);
                  data += '&SorteoClienteId=' + encodeURIComponent(sorteoClienteId);
                  data += '&SorteoClienteNombre=' + encodeURIComponent(clienteInputValue);
                  data += '&SorteoTelefono=' + encodeURIComponent(sorteoTelefono);
                  data += '&SorteoFechaNac=' + encodeURIComponent(sorteoFechaNac);
                  data += '&SorteoFolioRifa=' + encodeURIComponent(sorteoFolioCompleto);
                  data += '&SorteoParticipa=' + encodeURIComponent(sorteoParticipa);
                }

                // === CONSOLE.LOG PARA PREPARAR TICKET ===
                console.log('========== DATOS ENVIADOS PARA TICKET ==========');
                console.log('Ticket:', TicketVal);
                console.log('Folio Rifa:', folioRifaFinal);
                console.log('Total:', boletaTotal);
                console.log('Cambio:', cambiocliente);
                console.log('Cliente:', clienteInputValue);
                console.log('Forma de Pago:', formaPagoSeleccionada);
                console.log('Vendedor:', Vendedor);
                console.log('Productos:', valoresTabla);
                if (sorteoId > 0) {
                  console.log('--- DATOS SORTEO ---');
                  console.log('Sorteo ID:', sorteoId);
                  console.log('Cliente ID:', sorteoClienteId);
                  console.log('Teléfono:', sorteoTelefono);
                  console.log('Fecha Nacimiento:', sorteoFechaNac);
                  console.log('Folio Sorteo:', sorteoFolioCompleto);
                  console.log('Participa:', sorteoParticipa ? 'SÍ' : 'NO');
                }
                console.log('Data completa enviada:', data);
                console.log('=================================================');

                $.ajax({
                  type: 'POST',
                  url: 'http://localhost/ticket/TicketVenta.php',
                  data: data,
                  success: function(response) {
                    // === REGISTRAR PARTICIPACIÓN EN SORTEO ===
                    if (sorteoId > 0) {
                      registrarParticipacionSorteo(function() {
                        location.reload();
                      });
                    } else {
                      location.reload();
                    }
                  },
                  error: function(error) {
                    // Aun en error de ticket, intentar registrar sorteo
                    if (sorteoId > 0) {
                      registrarParticipacionSorteo(function() {
                        location.reload();
                      });
                    } else {
                      location.reload();
                    }
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

  // === FUNCIÓN PARA REGISTRAR PARTICIPACIÓN EN SORTEO ===
  function registrarParticipacionSorteo(callback) {
    var sucursalVal = $("input[name='SucursalEnVenta[]']").first().val() || '0';
    
    // Si el cliente no existe en la BD (sorteoClienteId == 0), registrarlo primero
    if (sorteoClienteId == '0' && clienteInputValue && clienteInputValue.trim() !== '') {
      registrarClienteYParticipacion(sucursalVal, callback);
    } else {
      // Cliente ya existe, registrar participación directamente
      enviarParticipacion(sucursalVal, sorteoClienteId, callback);
    }
  }

  function registrarClienteYParticipacion(sucursalVal, callback) {
    var licencia = ''; // Se puede obtener del DOM si es necesario
    var sucursalNombre = '';
    
    $.ajax({
      type: 'POST',
      url: 'Controladores/RegistrarClienteRapido.php',
      data: {
        nombre: clienteInputValue,
        telefono: sorteoTelefono,
        fecha_nacimiento: sorteoFechaNac,
        sucursal: sucursalVal,
        sucursal_nombre: sucursalNombre,
        licencia: licencia,
        ingreso: Vendedor
      },
      dataType: 'json',
      success: function(resp) {
        var clienteIdFinal = '0';
        if (resp.status === 'success' || resp.status === 'exists') {
          clienteIdFinal = resp.cliente.id;
        }
        enviarParticipacion(sucursalVal, clienteIdFinal, callback);
      },
      error: function() {
        // En caso de error, registrar participación sin ID de cliente
        enviarParticipacion(sucursalVal, '0', callback);
      }
    });
  }

  function enviarParticipacion(sucursalVal, clienteIdFinal, callback) {
    var folioRifaCompleto = ($('#sorteoPrefijoFolio').val() || '') + sorteoFolioRifa;
    
    $.ajax({
      type: 'POST',
      url: 'Controladores/RegistrarParticipacionSorteo.php',
      data: {
        sorteo_id: sorteoId,
        venta_ticket: TicketVal,
        cliente_id: clienteIdFinal,
        nombre_cliente: clienteInputValue,
        telefono_cliente: sorteoTelefono,
        fecha_nac_cliente: sorteoFechaNac,
        folio_rifa: folioRifaCompleto,
        sucursal: sucursalVal,
        registrado_por: Vendedor,
        participa: sorteoParticipa
      },
      dataType: 'json',
      success: function(resp) {
        if (callback) callback();
      },
      error: function() {
        if (callback) callback();
      }
    });
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
