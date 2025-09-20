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
              try {
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
                          setTimeout(function() {
                              location.reload();
                          }, 1000);
                      });
                  } else {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: res.message,
                      });
                  }
              } catch (e) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error inesperado',
                      text: 'No se pudo procesar la respuesta del servidor.',
                  });
                  console.error('Error al procesar la respuesta:', e);
              }
          },
          error: function (xhr, status, error) {
              Swal.fire({
                  icon: 'error',
                  title: 'Error en la conexión',
                  text: `No se pudo conectar con el servidor: ${status} - ${error}`,
              });
              console.error('Error AJAX:', xhr.responseText);
          }
      });
  }

  function enviarDatosTicket() {
      var data = $('#TraspasosNotasALMomento').serialize();

      $.ajax({
          type: 'POST',
          url: 'Controladores/TicketTraspasoONotaDeCredito.php',
          data: data,
          success: function (response) {
              try {
                  let res = JSON.parse(response);
                  if (res.status === 'success') {
                      console.log("Ticket generado exitosamente:", response);
                  } else {
                      console.log("Ticket no generado:", res.message);
                  }
              } catch (e) {
                  console.log("Respuesta inesperada del ticket:", e);
              }
          },
          error: function (xhr, status, error) {
              console.log("Error al generar el ticket:", status, error);
          }
      });
  }

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
          // Verificar que haya al menos un artículo en la tabla
          if ($('#tablaAgregarArticulos tbody tr').length === 0) {
              Swal.fire({
                  icon: 'warning',
                  title: 'Sin artículos',
                  text: 'Debe agregar al menos un artículo antes de realizar el traspaso.',
              });
              return false;
          }
          
          // Verificar que se haya seleccionado el tipo de movimiento
          const tipoMovimiento = $('#selTipoPago').val();
          if (!tipoMovimiento || tipoMovimiento === '0') {
              Swal.fire({
                  icon: 'warning',
                  title: 'Tipo de movimiento requerido',
                  text: 'Debe seleccionar el tipo de movimiento antes de continuar.',
              });
              return false;
          }
          
          // Verificar que se haya seleccionado la sucursal destino
          const sucursalDestino = $('#sucursaldestinoelegida').val();
          if (!sucursalDestino || sucursalDestino === '0') {
              Swal.fire({
                  icon: 'warning',
                  title: 'Sucursal destino requerida',
                  text: 'Debe seleccionar la sucursal destino antes de continuar.',
              });
              return false;
          }
          
          generarVistaPreviaYConfirmar();
      }
  });

  // Manejador de clic para el botón de realizar traspaso
  $("#btnIniciarVenta").click(function(e) {
      console.log("Botón btnIniciarVenta clickeado");
      e.preventDefault(); // Prevenir el comportamiento por defecto del botón
      e.stopPropagation(); // Detener la propagación del evento
      console.log("Enviando formulario...");
      $("#TraspasosNotasALMomento").submit(); // Enviar el formulario
  });
});
