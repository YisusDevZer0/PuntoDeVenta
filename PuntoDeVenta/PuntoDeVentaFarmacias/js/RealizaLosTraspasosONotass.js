// Funciones para el sistema de traspasos

function actualizarSumaTotal() {
  var totalVenta = parseFloat(document.getElementById("totalVenta").textContent); // Total de la venta
  var metodoPago = document.getElementById("selTipoPago").value; // Método de pago seleccionado
  var iptTarjeta = parseFloat(document.getElementById("iptTarjeta")?.value) || 0; // Pago con tarjeta (por defecto 0)
  var iptEfectivo = parseFloat(document.getElementById("iptEfectivoRecibido")?.value) || 0; // Pago con efectivo (por defecto 0)
  var totalCubierto = 0; // Inicializamos el total cubierto
  var cambio = 0; // Inicializamos el cambio
  switch (metodoPago) {
  case "Credito":
    iptEfectivo = totalVenta;
    if (document.getElementById("iptEfectivoRecibido")) {
      document.getElementById("iptEfectivoRecibido").value = iptEfectivo.toFixed(2);
      $('#iptEfectivoRecibido').trigger('input');
    }
  
    totalCubierto = iptEfectivo;
    cambio = 0;
    break;

  case "Efectivo y Tarjeta":
    if (iptTarjeta >= totalVenta) {
      iptEfectivo = 0;
    } else {
      iptEfectivo = totalVenta - iptTarjeta;
    }
    if (document.getElementById("iptEfectivoRecibido")) {
      document.getElementById("iptEfectivoRecibido").value = iptEfectivo.toFixed(2);
      $('#iptEfectivoRecibido').trigger('input');
    }
    totalCubierto = iptTarjeta + iptEfectivo;
    cambio = iptEfectivo - (totalVenta - iptTarjeta);
    cambio = cambio > 0 ? cambio : 0;
    break;

    case "Efectivo Y Credito":
   
    if (iptTarjeta >= totalVenta) {
      iptEfectivo = 0;
    } else {
      iptEfectivo = totalVenta - iptTarjeta;
    }
    if (document.getElementById("iptEfectivoRecibido")) {
      document.getElementById("iptEfectivoRecibido").value = iptEfectivo.toFixed(2);
      $('#iptEfectivoRecibido').trigger('input');
    }
    totalCubierto = iptTarjeta + iptEfectivo;
    cambio = iptEfectivo - (totalVenta - iptTarjeta);
    cambio = cambio > 0 ? cambio : 0;
    break;

}

  // Actualizar el cambio en el elemento <span>
  if (document.getElementById("Vuelto")) {
    document.getElementById("Vuelto").textContent = cambio.toFixed(2);
  }

  // Actualizar el total que se muestra al cliente
  if (metodoPago === "Efectivo y Tarjeta" || metodoPago === "Efectivo Y Credito") {
    if (document.getElementById("totaldeventacliente")) {
      document.getElementById("totaldeventacliente").value = iptEfectivo.toFixed(2);
    }
  } else {
    if (document.getElementById("totaldeventacliente")) {
      document.getElementById("totaldeventacliente").value = totalVenta.toFixed(2);
    }
  }
}

// Detectar cambios en el método de pago
if (document.getElementById("selTipoPago")) {
  document.getElementById("selTipoPago").addEventListener("change", actualizarSumaTotal);
}

// Detectar cambios en los campos de tarjeta y efectivo
if (document.getElementById("iptTarjeta")) {
  document.getElementById("iptTarjeta").addEventListener("input", actualizarSumaTotal);
}
if (document.getElementById("iptEfectivoRecibido")) {
  document.getElementById("iptEfectivoRecibido").addEventListener("input", actualizarSumaTotal);
}

$(document).ready(function() {
  

  // Agregar un controlador de eventos al input
  $('#iptEfectivoRecibido').on('input', function() {
    var valorInput = $(this).val();
    var miBoton = $('#btnIniciarVenta');

    if (valorInput.length > 0) {
      // Desbloquear el botón si el input contiene datos
      miBoton.prop('disabled', false);
    } else {
      // Bloquear el botón si el input está vacío
      miBoton.prop('disabled', true);
    }
  });
});



  $(document).ready(function() {
    $("#chkEfectivoExacto").change(function() {
      if ($(this).is(":checked")) {
        var boletaTotal = parseFloat($("#boleta_total").text());
        $("#Vuelto").text("0.00");
        $("#iptEfectivoRecibido").val(boletaTotal.toFixed(2));
      }
    });

    $("#iptEfectivoRecibido").change(function() {
      var boletaTotal = parseFloat($("#boleta_total").text());
      var efectivoRecibido = parseFloat($(this).val());

      if ($("#chkEfectivoExacto").is(":checked") && boletaTotal >= efectivoRecibido) {
        $("#Vuelto").text("0.00");
        $("#boleta_total").text(efectivoRecibido.toFixed(2));
      } else {
        var vuelto = efectivoRecibido - boletaTotal;
        $("#Vuelto").text(vuelto.toFixed(2));
        $("#cambiorecibidocliente").val(vuelto.toFixed(2));
        
      }
    });
  });

  $("#btnVaciarListado").click(function() {
    console.log("Click en el botón");
    $("#tablaAgregarArticulos tbody").empty();
    actualizarImporte($('#tablaAgregarArticulos tbody tr:last-child'));
    calcularIVA();
    actualizarSuma();
    mostrarTotalVenta();
    mostrarSubTotal();
    mostrarIvaTotal()
  });


  function actualizarEfectivoEntregado() {
  var inputEfectivo = document.getElementById("iptEfectivoRecibido");
  var spanEfectivoEntregado = document.getElementById("EfectivoEntregado");
  var inputEfectivoOculto = document.getElementById("iptEfectivoOculto");

  if (spanEfectivoEntregado && inputEfectivo) {
    spanEfectivoEntregado.innerText = inputEfectivo.value;
  }
  if (inputEfectivoOculto && inputEfectivo) {
    inputEfectivoOculto.value = inputEfectivo.value;
  }
}

  $(function() {
    $("#clienteInput").autocomplete({
      source: function(request, response) {
        $.ajax({
          url: "Controladores/clientes.php",
          dataType: "json",
          data: {
            term: request.term
          },
          success: function(data) {
            response(data);
          }
        });
      },
      minLength: 0
    });
  });

  table = $('#tablaAgregarArticulos').DataTable({
    searching: false, // Deshabilitar la funcionalidad de búsqueda
    paging: false, // Deshabilitar el paginador
    "columns": [{
        "data": "id"
      },
      {
        "data": "codigo"
      },
      {
        "data": "descripcion"
      },
      {
        "data": "cantidad"
      },

      {
        "data": "precio"
      },
      // {
      //     "data": "importesiniva"
      // },
      // {
      //     "data": "ivatotal"
      // },
      // {
      //     "data": "ieps"
      // },
      {
        "data": "eliminar"
      },
      {
        "data": "descuentos"

      },
      {
        "data": "descuentos2"
      },
      {
        "data": "descuento2"
      },
    ],

    "order": [
      [0, 'desc']

    ],
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    //para usar los botones   
    responsive: "true",

  });

  function mostrarTotalVenta() {
    var totalVenta = 0;
    $('#tablaAgregarArticulos tbody tr').each(function() {
        var importe = parseFloat($(this).find('.importe').val().replace(/[^\d.-]/g, ''));
        if (!isNaN(importe)) {
            totalVenta += importe;
        }
    });

 
    $('#totalVenta').text(totalVenta.toFixed(2));
    $('#boleta_total').text(totalVenta.toFixed(2));
    $("#totaldeventacliente").val(totalVenta.toFixed(2));
    
}



  function mostrarSubTotal() {
    var subtotal = 0;
    $('#tablaAgregarArticulos tbody tr').each(function() {
      var importeSinIVA = parseFloat($(this).find('.importe_siniva').val().replace(/[^\d.-]/g, ''));
      if (!isNaN(importeSinIVA)) {
        subtotal += importeSinIVA;
      }
    });

    $('#boleta_subtotal').text(subtotal.toFixed(2));
  }


  function mostrarIvaTotal() {
    var subtotal = 0;
    $('#tablaAgregarArticulos tbody tr').each(function() {
      var importeSinIVA = parseFloat($(this).find('.valordelniva').val().replace(/[^\d.-]/g, ''));
      if (!isNaN(importeSinIVA)) {
        subtotal += importeSinIVA;
      }
    });

    $('#ivatotal').text(subtotal.toFixed(2));
  }
  function mostrarToast(mensaje) {
  var toast = $('<div class="toast"></div>').text(mensaje);
  $('body').append(toast);
  toast.fadeIn(400).delay(3000).fadeOut(400, function() {
    $(this).remove();
  });
}
  function buscarArticulo(codigoEscaneado) {
  var formData = new FormData();
  formData.append('codigoEscaneado', codigoEscaneado);

  $.ajax({
    url: "Controladores/escaner_articulo_administrativo.php",
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (data) {
      if ($.isEmptyObject(data)) {
        // Manejar caso de no resultados
      } else if (data.codigo || data.descripcion) {
        agregarArticulo(data);
        if (data.esAntibiotico) {
          Swal.fire({
    title: '¡Atención!',
    text: 'El artículo escaneado es de tipo "ANTIBIOTICO". Por favor, recuerde solicitar la receta médica al paciente antes de proceder con la venta.',
    icon: 'warning',
    confirmButtonText: 'Entendido',
    confirmButtonColor: '#3085d6',
    background: '#fff3cd', // Color de fondo amarillo claro para llamar la atención
    customClass: {
        title: 'swal-title',
        content: 'swal-content'
    },
    backdrop: `
        rgba(0,0,123,0.4)
        url("https://via.placeholder.com/150") // Puedes añadir una imagen o dejarlo en blanco
        center center
        no-repeat
    `
});

        }
      }

      limpiarCampo();
    },
    error: function (data) {
      msjError('Error en la búsqueda');
    }
  });
}


function limpiarCampo() {
  $('#codigoEscaneado').val('');
  $('#codigoEscaneado').focus();
}

var isScannerInput = false;

// Escucha el evento keyup en el campo de búsqueda
$('#codigoEscaneado').keyup(function (event) {
  if (event.which === 13) { // Verifica si la tecla presionada es "Enter"
    if (!isScannerInput) { // Verifica si el evento no viene del escáner
      var codigoEscaneado = $('#codigoEscaneado').val();
      buscarArticulo(codigoEscaneado);
      event.preventDefault(); // Evita que el formulario se envíe al presionar "Enter"
    }
    isScannerInput = false; // Restablece la bandera del escáner
  }
});

// Agrega el autocompletado al campo de búsqueda
$('#codigoEscaneado').autocomplete({
  source: function (request, response) {
    // Realiza una solicitud AJAX para obtener los resultados de autocompletado
    $.ajax({
      url: 'Controladores/autocompletadoAdministrativo.php',
      type: 'GET',
      dataType: 'json',
      data: {
        term: request.term
      },
      success: function (data) {
        response(data);
      }
    });
  },
  minLength: 3, // Especifica la cantidad mínima de caracteres para activar el autocompletado
  select: function (event, ui) {
    // Cuando se selecciona un resultado del autocompletado, llamar a la función buscarArticulo() con el código seleccionado
    var codigoEscaneado = ui.item.value;
    isScannerInput = true; // Establece la bandera del escáner
    $('#codigoEscaneado').val(codigoEscaneado);
    buscarArticulo(codigoEscaneado);
  }
});


  var tablaArticulos = ''; // Variable para almacenar el contenido de la tabla



  // Variable para almacenar el total del IVA
  var totalIVA = 0;
  function agregarArticulo(articulo) {
  if (!articulo || (!articulo.id && !articulo.descripcion)) {
    mostrarMensaje('El artículo no es válido');
    return;
  }

  let row = articulo.id
    ? $('#tablaAgregarArticulos tbody').find('tr[data-id="' + articulo.id + '"]')
    : null;

  if (!row || !row.length) {
    $('#tablaAgregarArticulos tbody tr').each(function () {
      if ($(this).find('.descripcion-producto-input').val() === articulo.descripcion) {
        row = $(this);
        return false;
      }
    });
  }

  if (row && row.length) {
    let cantidadActual = parseInt(row.find('.cantidad input').val());
    let nuevaCantidad = cantidadActual + parseInt(articulo.cantidad);

    if (nuevaCantidad < 0) {
      mostrarMensaje('La cantidad no puede ser negativa');
      return;
    }

    row.find('.cantidad input').val(nuevaCantidad);
    mostrarToast('Cantidad actualizada para el producto: ' + articulo.descripcion);
    actualizarImporte(row);
    calcularIVA();
    actualizarSuma();
    mostrarTotalVenta();
    mostrarSubTotal();
    mostrarIvaTotal();
  } else {
    const btnEliminar = '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this);"><i class="fas fa-minus-circle fa-xs"></i></button>';

    const tr = `
      <tr data-id="${articulo.id}">
        <td class="codigo"><input class="form-control codigo-barras-input" style="font-size: 0.75rem;" type="text" value="${articulo.codigo || ''}" name="CodBarras[]" /></td>
        <td class="descripcion"><textarea class="form-control descripcion-producto-input" name="NombreDelProducto[]" style="font-size: 0.75rem;">${articulo.descripcion}</textarea></td>
        <td class="cantidad"><input class="form-control cantidad-vendida-input" style="font-size: 0.75rem;" type="number" name="Cantidad[]" value="${articulo.cantidad}" onchange="actualizarImporte($(this).parent().parent());" /></td>
        <td class="preciofijo"><input class="form-control preciou-input" style="font-size: 0.75rem;" type="number" value="${articulo.precio}" /></td>
        <td style="display:none;" class="precio"><input hidden id="precio_${articulo.id}" class="form-control precio" style="font-size: 0.75rem;" type="number" name="Pc[]" value="${articulo.precio}" onchange="actualizarImporte($(this).parent().parent());" /></td>
        <td><input id="importe_${articulo.id}" class="form-control importe" name="ImporteGenerado[]" style="font-size: 0.75rem;" type="number" readonly /></td>
        <td style="display:none;"><input id="importe_siniva_${articulo.id}" class="form-control importe_siniva" type="number" readonly /></td>
        <td style="display:none;"><input id="valordelniva_${articulo.id}" class="form-control valordelniva" type="number" readonly /></td>
        <td style="display:none;"><input id="ieps_${articulo.id}" class="form-control ieps" type="number" readonly /></td>
        <td style="display:none;" class="idbd"><input class="form-control" style="font-size: 0.75rem;" type="text" value="${articulo.id}" name="IdBasedatos[]" /></td>
        <td style="display:none;" class="lote"><input class="form-control" style="font-size: 0.75rem;" type="text" value="${articulo.lote}" name="LoteDelProducto[]" /></td>
        <td style="display:none;" class="claveess"><input class="form-control" style="font-size: 0.75rem;" type="text" value="${articulo.clave}" name="ClaveAdicional[]" /></td>
        <td style="display:none;" class="tiposservicios"><input class="form-control" style="font-size: 0.75rem;" type="text" value="${articulo.tipo}" name="Tipo[]" /></td>
        <td style="display:none;" class="NumeroTicket"><input type="text" class="form-control" hidden id="Folio_Ticket" name="Folio_Ticket[]" value="<?php echo $resultado_en_mayusculas; ?>" readonly /></td>
        <td style="display:none;" class="Vendedor"><input hidden id="VendedorFarma" type="text" class="form-control" name="AgregadoPor[]" readonly value="<?php echo $row['Nombre_Apellidos'] ?>" /></td>
        <td class="TipoMovimiento"><input type="text" class="form-control tipo-movimiento" name="TipoDeMov[]" readonly value="" /></td>
        <td style="display:none;" class="Sucursal"><input hidden type="text" class="form-control" name="Fk_sucursal[]" readonly value="<?php echo $row['Fk_Sucursal'] ?>" /></td>
        <td style="display:none;" class="SucursalDestino"><input type="text" class="form-control tipoajuste-input" id="inputDestino" name="Fk_SucursalDestino[]" readonly /></td>
        <td style="display:none;" class="SucursalDestinoLetras"><input type="text" class="form-control tipoajusteletras-input" id="inputDestino" name="Fk_SucursalDestinoLetras[]" readonly /></td>
           <td style="display:none;" class="SucursalOrigenLetras"><input type="text" class="form-control destino-input" id="inputDestinoLetras" value="<?php echo $row['Nombre_Sucursal'] ?>" name="Fk_SucursalOrigenLetras[]" readonly /></td>
        <td style="display:none;" class="Sistema"><input hidden type="text" class="form-control" name="Sistema[]" readonly value="POSVENTAS" /></td>
        <td style="display:none;" class="Liquidado"><input hidden type="text" class="form-control" name="Liquidado[]" readonly value="N/A" /></td>
        <td style="display:none;" class="Estatus"><input hidden type="text" class="form-control" name="Estatus[]" readonly value="Generado" /></td>
        <td style="display:none;" class="Empresa"><input hidden type="text" class="form-control" name="ID_H_O_D[]" readonly value="Doctor Pez" /></td>
        <td class="Fecha"> <input type="date" class="form-control fecha-dinamica" name="FechaVenta[]" id="fecha-apertura2-${articulo.id}" readonly value="" /></td>
        <td style="display:none;" class="FormaPago"><input hidden type="text" class="form-control forma-pago-input" id="FormaPagoCliente" name="FormaDePago[]" value="Efectivo" /></td>
        <td><div class="btn-container">${btnEliminar}</div></td>
      </tr>`;

    $('#tablaAgregarArticulos tbody').append(tr);

    const newRow = $('#tablaAgregarArticulos tbody tr:last-child');
    actualizarImporte(newRow);
    newRow.find('.tipoajuste-input').val(selectedAdjustment);
    newRow.find('.tipoajusteletras-input').val(selectedText);
    newRow.find('.tipo-movimiento').val(selectedAdjustmentTipoMov);
   
    calcularIVA();
    actualizarSuma();
    mostrarTotalVenta();
  }

  limpiarCampo();
  $('#codigoEscaneado').focus();
}



$('#fecha-apertura').on('change', function() {
    const nuevaFecha = $(this).val();
    $('input[name="FechaDeTraspaso[]"]').each(function() {
        $(this).val(nuevaFecha);
    });
});



  
  function actualizarImporte(row) {
  var cantidad = parseInt(row.find('.cantidad-vendida-input').val());
  var precio = parseFloat(row.find('.precio input').val());

  

  if (cantidad < 0) {
    mostrarMensaje('La cantidad no puede ser negativa');
    return;
  }

  if (precio < 0) {
    mostrarMensaje('El precio no puede ser negativo');
    return;
  }

  var importe = cantidad * precio;
  var iva = importe / 1.16 * 0.16;
  var importeSinIVA = importe - iva;
  var ieps = importe * 0.08;

  row.find('input.importe').val(importe.toFixed(2));
  row.find('input.importe_siniva').val(importeSinIVA.toFixed(2));
  row.find('input.valordelniva').val(iva.toFixed(2));
  row.find('input.ieps').val(ieps.toFixed(2));

  // Llamar a la función para recalcular la suma de importes
  actualizarSuma();
  mostrarTotalVenta();
  mostrarSubTotal();
  mostrarIvaTotal();
}



  // Función para calcular el IVA
  function calcularIVA() {
    totalIVA = 0;

    $('#tablaAgregarArticulos tbody tr').each(function() {
      var iva = parseFloat($(this).find('.valordelniva input').val());
      totalIVA += iva;
    });

    $('#totalIVA').text(totalIVA.toFixed(2));
  }

  // Función para actualizar la suma de importe sin IVA, IEPS y diferencia de IVA
  function actualizarSuma() {
    var sumaImporteSinIVA = 0;
    var totalIEPS = 0;

    $('#tablaAgregarArticulos tbody tr').each(function() {
      var importeSinIVA = parseFloat($(this).find('.importe_siniva input').val());
      sumaImporteSinIVA += importeSinIVA;

      var ieps = parseFloat($(this).find('.ieps input').val());
      totalIEPS += ieps;
    });

    $('#sumaImporteSinIVA').text(sumaImporteSinIVA.toFixed(2));
    $('#totalIEPS').text(totalIEPS.toFixed(2));
  }

  // Función para mostrar un mensaje
  function mostrarMensaje(mensaje) {
    // Mostrar el mensaje en una ventana emergente de alerta
    alert(mensaje);
  }
// Modificar la función eliminarFila() para llamar a las funciones necesarias después de eliminar la fila
function eliminarFila(element) {
  var fila = $(element).closest('tr'); // Obtener la fila más cercana al elemento
  fila.remove(); // Eliminar la fila

  // Llamar a las funciones necesarias después de eliminar la fila
  calcularIVA();
  actualizarSuma();
  mostrarTotalVenta();
  mostrarSubTotal();
  mostrarIvaTotal();
}
/// INICIO CODIGO DESCUENTOS///
// Variable para rastrear si el descuento ya fue aplicado
var descuentoAplicado = false;

function aplicarDescuento() {
  // Si el descuento ya fue aplicado, no se hace nada
  if (descuentoAplicado) {
    return;
  }
}
  var importeInput = $('#importe'); // Asegúrate de que 'importe' sea el ID correcto de tu campo de importe
  var importeInput2 = $('#precio'); // Asegúrate de que 'importe' sea el ID correcto de tu campo de importe
function abrirSweetAlert(elemento) {
  // Obtener la fila correspondiente
  var fila = $(elemento).closest('tr');

  // Obtener el campo importe
  var importeInput = fila.find('.importe');
  var importeInput2 = fila.find('.precio');
  // $(elemento).hide();

  // Mostrar el SweetAlert para ingresar el descuento
  var swalInstance = Swal.fire({
    title: 'Seleccionar descuento',
    html: '<label for="customDescuento">Ingresar monto a descontar:</label>' +
      '<input type="number" class="form-control" id="customDescuento" min="0" placeholder="Ingrese monto a descontar">' +
      '<br>' +
      '<label for="porcentajeDescuento">Seleccionar porcentaje de descuento:</label>' +
      '<select class="form-control" id="porcentajeDescuento">' +
      '<option value="">Seleccionar</option>' +
      '<option value="0">0%</option>' +
      '<option value="5">5%</option>' +
        '<option value="10">10%</option>' +
      '<option value="15">15%</option>' +
      '<option value="20">20%</option>' +
      '<option value="25">25%</option>' +
      '<option value="30">30%</option>' +
      '<option value="35">35%</option>' +
      '<option value="40">40%</option>' +
      '<option value="45">45%</option>' +
      '<option value="50">50%</option>' +
      '<option value="55">55%</option>' +
      '<option value="60">60%</option>' +
         '<option value="65">65%</option>' +
         '<option value="70">70%</option>' +
         '<option value="75">75%</option>' +
         '<option value="80">80%</option>' +
         '<option value="85">85%</option>' +
         '<option value="90">90%</option>' +
         '<option value="95">95%</option>' +
         '<option value="100">100%</option>' +
      // Resto de opciones del select
      '</select>',
    showCancelButton: true,
    confirmButtonText: 'Aplicar descuento',
    cancelButtonText: 'Cancelar',
    showLoaderOnConfirm: true,
    preConfirm: () => {
      // Una vez que se aplique el descuento, marcar como descuento aplicado
      descuentoAplicado = true;
      // Deshabilitar ambos campos select e input
        $('#customDescuento').prop('disabled', true);
        $('#porcentajeDescuento').prop('disabled', true);
      // Obtener el valor del monto a descontar
      var montoDescontar = parseFloat($('#customDescuento').val());

      // Obtener el valor del porcentaje de descuento seleccionado
      var porcentajeDescuento = parseFloat($('#porcentajeDescuento').val());

      // Validar y calcular el importe con descuento
      if (!isNaN(porcentajeDescuento) && porcentajeDescuento >= 0 && porcentajeDescuento <= 100) {
        // Obtener el valor del importe actual
        var importeActual = parseFloat(importeInput.val());

        // Calcular el importe con descuento por porcentaje
        var importeDescuento = importeActual * (1 - porcentajeDescuento / 100);

        // Actualizar el valor del importe
        importeInput.val(importeDescuento.toFixed(2));
        importeInput2.val(importeDescuento.toFixed(2));
        // Deshabilitar el input de monto a descontar
        $('#customDescuento').prop('disabled', true);

        // Mostrar mensaje del tipo de descuento y monto aplicado
        var tipoDescuento = porcentajeDescuento + '% de descuento';
        var montoAplicado = (importeActual - importeDescuento).toFixed(2);
        fila.find('.descuento-aplicado').val(montoAplicado); // Aquí puedes ajustar cómo mostrar el valor
      } else if (!isNaN(montoDescontar) && montoDescontar >= 0) {
        // Obtener el valor del importe actual
        var importeActual = parseFloat(importeInput.val());

        // Restar el monto al importe actual
        var importeNuevo = importeActual - montoDescontar;

        // Actualizar el valor del importe
        importeInput.val(importeNuevo.toFixed(2));
        importeInput2.val(importeNuevo.toFixed(2));
        // Deshabilitar el select de porcentaje de descuento
        $('#porcentajeDescuento').prop('disabled', true);

        // Mostrar mensaje del tipo de descuento y monto aplicado
        var tipoDescuento = 'Descuento de monto';
        var montoAplicado = montoDescontar.toFixed(2);
        fila.find('.descuento-aplicado').val(montoAplicado); // Aquí puedes ajustar cómo mostrar el valor
      }

      Swal.fire({
        title: 'Descuento aplicado',
        html: `$${montoAplicado}`,
        icon: 'success'
      });

      // Restablecer los eventos "change" cuando se cierre el SweetAlert
      // swalInstance.then(() => {
      //   $('#customDescuento').off('change');
      //   $('#porcentajeDescuento').off('change');

      //   // Mostrar el botón cuando se presione "Cancelar"
        
      // });

      actualizarSuma();
      mostrarTotalVenta();
      mostrarSubTotal();
      mostrarIvaTotal();
      actualizarImporte();
    },
    allowOutsideClick: () => !Swal.isLoading()
  });

  // Agregar el evento "change" al input de monto a descontar
  $('#customDescuento').on('change', function() {
    // Obtener el valor del monto a descontar
    var montoDescontar = parseFloat($(this).val());

    // Obtener el elemento select
    var porcentajeSelect = $('#porcentajeDescuento');

    // Si se seleccionó un monto a descontar, deshabilitar el select
    if (!isNaN(montoDescontar) && montoDescontar >= 0) {
      porcentajeSelect.prop('disabled', true);
    } else {
      porcentajeSelect.prop('disabled', false);
    }
  });

  // Agregar el evento "change" al select de porcentaje de descuento
  $('#porcentajeDescuento').on('change', function() {
    // Obtener el valor del porcentaje de descuento seleccionado
    var porcentajeDescuento = parseFloat($(this).val());

    // Obtener el input de monto a descontar
    var montoInput = $('#customDescuento');

    // Si se seleccionó un porcentaje de descuento, deshabilitar el input de monto
    if (!isNaN(porcentajeDescuento) && porcentajeDescuento >= 0 && porcentajeDescuento <= 100) {
      montoInput.prop('disabled', true);
    } else {
      montoInput.prop('disabled', false);
    }
  });
}

// Agregar el evento click a un botón o enlace que abrirá el SweetAlert
$('#abrirSweetAlertBtn').on('click', function() {
  aplicarDescuento();
});

// Evento click para el botón de realizar traspaso
$('#btnIniciarVenta').on('click', function(e) {
  e.preventDefault(); // Prevenir el comportamiento por defecto del formulario
  
  // Validar que se haya seleccionado un tipo de movimiento
  var tipoMovimiento = $('#selTipoPago').val();
  if (tipoMovimiento === '0' || tipoMovimiento === '') {
    Swal.fire({
      icon: 'warning',
      title: 'Advertencia',
      text: 'Debe seleccionar un tipo de movimiento'
    });
    return;
  }
  
  // Validar que se haya seleccionado una sucursal destino
  var sucursalDestino = $('#sucursaldestinoelegida').val();
  if (sucursalDestino === '0' || sucursalDestino === '') {
    Swal.fire({
      icon: 'warning',
      title: 'Advertencia',
      text: 'Debe seleccionar una sucursal destino'
    });
    return;
  }
  
  // Validar que haya productos en la tabla
  var filasProductos = $('#tablaAgregarArticulos tbody tr').length;
  if (filasProductos === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Advertencia',
      text: 'Debe agregar al menos un producto para realizar el traspaso'
    });
    return;
  }
  
  // Mostrar confirmación
  Swal.fire({
    title: '¿Confirmar traspaso?',
    text: 'Se realizará el traspaso de los productos seleccionados',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, realizar traspaso',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      // Aquí se procesaría el traspaso
      procesarTraspaso();
    }
  });
});

// Función para procesar el traspaso
function procesarTraspaso() {
  // Mostrar loading
  Swal.fire({
    title: 'Procesando...',
    text: 'Realizando el traspaso',
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    }
  });
  
  // Recopilar datos del formulario
  var formData = new FormData();
  
  // Agregar datos de cada fila de la tabla
  $('#tablaAgregarArticulos tbody tr').each(function(index) {
    var fila = $(this);
    formData.append('CodBarras[]', fila.find('input[name="CodBarras[]"]').val());
    formData.append('NombreDelProducto[]', fila.find('input[name="NombreDelProducto[]"]').val());
    formData.append('Cantidad[]', fila.find('input[name="Cantidad[]"]').val());
    formData.append('Pc[]', fila.find('input[name="Pc[]"]').val());
    formData.append('ImporteGenerado[]', fila.find('input[name="ImporteGenerado[]"]').val());
    formData.append('IdBasedatos[]', fila.find('input[name="IdBasedatos[]"]').val());
    formData.append('LoteDelProducto[]', fila.find('input[name="LoteDelProducto[]"]').val());
    formData.append('ClaveAdicional[]', fila.find('input[name="ClaveAdicional[]"]').val());
    formData.append('Tipo[]', fila.find('input[name="Tipo[]"]').val());
    formData.append('Folio_Ticket[]', fila.find('input[name="Folio_Ticket[]"]').val());
    formData.append('AgregadoPor[]', fila.find('input[name="AgregadoPor[]"]').val());
    formData.append('TipoDeMov[]', fila.find('input[name="TipoDeMov[]"]').val());
    formData.append('Fk_sucursal[]', fila.find('input[name="Fk_sucursal[]"]').val());
    formData.append('Fk_SucursalDestino[]', fila.find('input[name="Fk_SucursalDestino[]"]').val());
    formData.append('Fk_SucursalDestinoLetras[]', fila.find('input[name="Fk_SucursalDestinoLetras[]"]').val());
    formData.append('Fk_SucursalOrigenLetras[]', fila.find('input[name="Fk_SucursalOrigenLetras[]"]').val());
    formData.append('Sistema[]', fila.find('input[name="Sistema[]"]').val());
    formData.append('Liquidado[]', fila.find('input[name="Liquidado[]"]').val());
    formData.append('Estatus[]', fila.find('input[name="Estatus[]"]').val());
    formData.append('ID_H_O_D[]', fila.find('input[name="ID_H_O_D[]"]').val());
    formData.append('FechaVenta[]', fila.find('input[name="FechaVenta[]"]').val());
    formData.append('FormaDePago[]', fila.find('input[name="FormaDePago[]"]').val());
  });
  
  // Agregar datos adicionales
  formData.append('tipoMovimiento', $('#selTipoPago').val());
  formData.append('sucursalDestino', $('#sucursaldestinoelegida').val());
  formData.append('fechaTraspaso', $('#fecha-apertura').val());
  
  // Enviar datos al servidor
  $.ajax({
    url: 'Controladores/procesar_traspaso.php',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function(response) {
      Swal.close();
      if (response.success) {
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: response.message
        }).then(() => {
          // Limpiar la tabla y recargar la página
          $('#tablaAgregarArticulos tbody').empty();
          location.reload();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: response.message
        });
      }
    },
    error: function() {
      Swal.close();
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error de conexión al procesar el traspaso'
      });
    }
  });
} 