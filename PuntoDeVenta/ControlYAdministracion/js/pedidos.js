// Lógica de pedidos idéntica a ventas, adaptada
$(document).ready(function() {
  var isScannerInput = false;

  // Evento Enter para escanear o escribir
  $('#codigoEscaneado').keyup(function (event) {
    if (event.which === 13) {
      if (!isScannerInput) {
        var codigoEscaneado = $('#codigoEscaneado').val();
        buscarArticuloPedido(codigoEscaneado);
        event.preventDefault();
      }
      isScannerInput = false;
    }
  });

  // Autocompletado igual que ventas
  $('#codigoEscaneado').autocomplete({
    source: function (request, response) {
      $.ajax({
        url: 'Controladores/autocompletadoAdministrativo.php',
        type: 'GET',
        dataType: 'json',
        data: { term: request.term },
        success: function (data) { response(data); }
      });
    },
    minLength: 3,
    select: function (event, ui) {
      var codigoEscaneado = ui.item.value;
      isScannerInput = true;
      $('#codigoEscaneado').val(codigoEscaneado);
      buscarArticuloPedido(codigoEscaneado);
    }
  });

  // Buscar producto y agregarlo a la tabla
  function buscarArticuloPedido(codigoEscaneado) {
    var formData = new FormData();
    formData.append('codigoEscaneado', codigoEscaneado);
    $.ajax({
      url: 'Controladores/escaner_articulo_administrativo.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (data) {
        if ($.isEmptyObject(data)) {
          alert('Producto no encontrado');
        } else if (data.codigo || data.descripcion) {
          agregarArticuloPedido(data);
        }
        $('#codigoEscaneado').val('');
        $('#codigoEscaneado').focus();
      },
      error: function () {
        alert('Error en la búsqueda');
      }
    });
  }

  // Agregar producto a la tabla
  window.agregarArticuloPedido = function(articulo) {
    if (!articulo || (!articulo.id && !articulo.descripcion)) {
      alert('El artículo no es válido');
      return;
    } else if ($(`#tablaAgregarArticulos tbody tr[data-id='${articulo.id}']`).length) {
      alert('El artículo ya está en la lista.');
      return;
    }
    var tr = '';
    tr += `<tr data-id="${articulo.id}">`;
    tr += `<td>${articulo.codigo || ''}</td>`;
    tr += `<td>${articulo.descripcion || ''}</td>`;
    tr += `<td><input class="form-control cantidad-pedido-input" type="number" name="cantidad[]" value="1" min="1"></td>`;
    tr += `<td>${articulo.precio || ''}</td>`;
    tr += `<td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFilaPedido(this)"><i class="fas fa-minus-circle fa-xs"></i></button></td>`;
    tr += `<input type="hidden" name="producto_id[]" value="${articulo.id}">`;
    tr += `</tr>`;
    $('#tablaAgregarArticulos tbody').append(tr);
  };

  window.eliminarFilaPedido = function(btn) {
    $(btn).closest('tr').remove();
  };

  // Guardar pedido
  $('#btnGuardarPedido').on('click', function() {
    var productos = [];
    $('#tablaAgregarArticulos tbody tr').each(function() {
      var id = $(this).find('input[name="producto_id[]"]').val();
      var cantidad = $(this).find('input[name="cantidad[]"]').val();
      productos.push({ id: id, cantidad: cantidad });
    });
    if (productos.length === 0) {
      alert('Agrega al menos un producto al pedido.');
      return;
    }
    $.ajax({
      url: 'Controladores/PedidosController.php',
      type: 'POST',
      dataType: 'json',
      data: {
        accion: 'crear_pedido',
        productos: JSON.stringify(productos)
      },
      success: function(resp) {
        if (resp.status === 'ok') {
          alert('Pedido guardado correctamente');
          $('#tablaAgregarArticulos tbody').empty();
          // Aquí puedes recargar el listado de pedidos si lo deseas
        } else {
          alert('Error al guardar el pedido');
        }
      },
      error: function() {
        alert('Error al guardar el pedido');
      }
    });
  });
}); 