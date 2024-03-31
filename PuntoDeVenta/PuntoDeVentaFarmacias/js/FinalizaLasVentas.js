$(document).ready(function () {
  console.log("Document is ready"); 
  // Obtener todos los valores de los campos de la tabla
    var valoresTabla = [];
  var boletaTotal = 0;
  var cambiocliente = "";
  var clienteInputValue = "";
  var formaPagoSeleccionada = "";
  var TicketVal = "";
  var Vendedor = "";
  // Agregar los métodos de validación personalizados
  function validarFormulario() {
    var clienteInput = document.getElementById("clienteInput");

    if (clienteInput.value === "") {
      clienteInput.setCustomValidity("Este campo es obligatorio");
    } else {
      clienteInput.setCustomValidity("");
    }
  }

  // Función para validar el ticket en la base de datos
  function validarTicket(ticket) {
    console.log("Validating ticket:", ticket);
    // Realizar la petición AJAX para verificar el ticket
    $.ajax({
      type: 'POST',
      url: "Controladores/ValidarTicket.php",
      data: { NumeroDeTickeT: ticket },
      cache: false,
      success: function (data) {
        var response = JSON.parse(data);

        if (response.existe) {
          // El ticket ya existe en la base de datos, mostrar mensaje de error
          var numeroTicket = $("#Folio_Ticket").val();
          var mensajeError = 'El número de ticket "' + numeroTicket + '" ya existe, por favor actualize la pantalla. <br><br> ';
          Swal.fire({
            icon: 'error',
            title: 'Ticket duplicado',
            text: mensajeError + "si el problema persiste por favor contacte a soporte",
             
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
              preciounitario:preciounitario,
              importeventa:importeventa
            });
          });
          
         // Obtener el valor del elemento con el ID "boleta_total"
 boletaTotal = $('#boleta_total').text();
 cambiocliente = $('#Vuelto').text();
 clienteInputValue = $('#clienteInput').val();
 formaPagoSeleccionada = $('#selTipoPago option:selected').text();
 TicketVal = $("#Folio_Ticket").val();
 Vendedor = $("#VendedorFarma").val();
// Generar el contenido para el mensaje de confirmación

mensajeConfirmacion = '';

var mensajeConfirmacion = '<div class="dataTable" >';
mensajeConfirmacion += '<table id="tablaConfirmacion">';
mensajeConfirmacion += '<thead><tr><th>Total de venta</th><th>Cambio del cliente</th><th>Nombre del cliente</th><th>Forma de pago</th></tr></thead>';
mensajeConfirmacion += '<tbody>';
mensajeConfirmacion += '<tr><td>' + boletaTotal + '</td><td>' + cambiocliente + '</td><td>' + clienteInputValue + '</td><td>' + formaPagoSeleccionada + '</td></tr>';
mensajeConfirmacion += '</tbody>';
mensajeConfirmacion += '</table>';
mensajeConfirmacion += '</div>';
mensajeConfirmacion += '<br>';
mensajeConfirmacion += '<br>';




          // Agregar los valores de la tabla al mensaje
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
          
          // Mostrar el mensaje de confirmación con los valores de la tabla
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
              // El usuario confirmó finalizar la venta, continuar con el envío del formulario
              submitForm();
            }
          });

// Inicializar DataTables en la tabla de confirmación
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
    },



} 

);


          
        }
      },
      error: function () {
        // Error en la petición AJAX
        Swal.fire({
          icon: 'error',
          title: 'Error en la petición',
          text: 'No se pudo verificar el ticket. Por favor, inténtalo de nuevo.',
        });
      }
    });
  }

  // Función para enviar el formulario
  function submitForm() {
    // Realizar la petición AJAX
    console.log("Submitting form...");
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
          // Operación exitosa, realizar acciones necesarias
        // Después de que la operación sea exitosa, mostrar mensaje y abrir ventana
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
        
              // Realiza la solicitud POST una vez aquí
              var encodedValoresTabla = encodeURIComponent(JSON.stringify(valoresTabla));
              var encodedBoletaTotal = encodeURIComponent(boletaTotal);
              var encodedCambioCliente = encodeURIComponent(cambiocliente);
              var encodedClienteInputValue = encodeURIComponent(clienteInputValue);
              var encodedFormaPagoSeleccionada = encodeURIComponent(formaPagoSeleccionada);
              var encodedTicketVal = encodeURIComponent(TicketVal);
              var encodedVendedor = encodeURIComponent(Vendedor);
              // Construye la cadena de datos
              var data = 'BoletaTotal=' + encodedBoletaTotal +
                         '&CambioCliente=' + encodedCambioCliente +
                         '&ClienteInputValue=' + encodedClienteInputValue +
                         '&FormaPagoSeleccionada=' + encodedFormaPagoSeleccionada +
                         '&TicketVal=' + encodedTicketVal +
                         '&Vendedor=' + encodedVendedor +
                         '&ValoresTabla=' + encodedValoresTabla;
        
              // Realiza la solicitud POST
              $.ajax({
                type: 'POST',
                url: 'http://localhost:8080/ticket/TicketVenta.php',
                data: data,
                success: function(response) {
                  // Maneja la respuesta del servidor
                  console.log("Response from ticket generation:", response);
                  location.reload();
                },
                error: function(error) {
                  console.log("Validating ticket:", data);
                  console.error(error);
                }
              });
        
      
    }, 1500);
  },
});

        } else {
          // Error en la operación, mostrar mensaje de error
          Swal.fire({
            icon: 'error',
            title: 'Algo salió mal, por favor inténtalo de nuevo',
            text: response.message,
          });
        }
      },
      error: function () {
        // Error en la petición AJAX
        Swal.fire({
          icon: 'error',
          title: 'Error en la petición',
          text: 'No se pudieron guardar los datos. Por favor, inténtalo de nuevo.',
        });
      }
    });

    return false;
  }

  // Validar el formulario
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
    submitHandler: function () {
      console.log("Form submitted handler");
      validarFormulario();
      var ticket = $("#Folio_Ticket").val(); // Reemplaza "numeroTicket" por el ID o selector del campo de número de ticket
      console.log(ticket); // Agrega este console.log para verificar el valor del número de ticket en la consola del navegador
      validarTicket(ticket);
    },
  });
});
