// Lógica básica para pedidos administrativos
$(document).ready(function() {
    // Cargar tabla de pedidos al iniciar
    cargarPedidos();

    // Botón para nuevo pedido
    $('#btnNuevoPedido').on('click', function() {
        // Reinicializa el campo autocomplete
        $('#producto_autocomplete').val('');
        $('#producto_id').val('');
        $('#modalNuevoPedido').modal('show');
        // Inicializa autocomplete
        $('#producto_autocomplete').autocomplete({
            minLength: 2,
            source: function(request, response) {
                $.ajax({
                    url: 'Controladores/PedidosController.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        accion: 'buscar_producto',
                        q: request.term
                    },
                    success: function(resp) {
                        console.log('Respuesta productos:', resp);
                        if (resp.data && resp.data.length > 0) {
                            response($.map(resp.data, function(item) {
                                return {
                                    label: item.Nombre_Prod,
                                    value: item.Nombre_Prod,
                                    id: item.ID_Prod_POS
                                };
                            }));
                        } else {
                            response([{ label: 'No se encontraron productos', value: '', id: '' }]);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en AJAX:', error);
                        response([]);
                    }
                });
            },
            select: function(event, ui) {
                if (ui.item.id) {
                    $('#producto_id').val(ui.item.id);
                } else {
                    $('#producto_id').val('');
                }
            },
            focus: function(event, ui) {
                if (!ui.item.id) {
                    event.preventDefault();
                }
            }
        });
    });

    // Enviar formulario de nuevo pedido
    $('#formNuevoPedido').on('submit', function(e) {
        e.preventDefault();
        if (!$('#producto_id').val()) {
            alert('Debes seleccionar un producto válido.');
            return;
        }
        $.post('Controladores/PedidosController.php', {
            accion: 'crear',
            producto_id: $('#producto_id').val(),
            cantidad: $('#cantidad').val(),
            observaciones: $('#observaciones').val()
        }, function(resp) {
            console.log('Respuesta crear pedido:', resp);
            if(resp.status === 'ok') {
                $('#modalNuevoPedido').modal('hide');
                cargarPedidos();
            } else {
                alert('Error al crear pedido');
            }
        }, 'json');
    });

    // Función para cargar la tabla de pedidos
    function cargarPedidos() {
        $.post('Controladores/PedidosController.php', {accion: 'listar'}, function(resp) {
            console.log('Respuesta listar pedidos:', resp);
            let html = '<div class="table-responsive"><table class="table table-striped table-hover"><thead class="table-primary"><tr><th>ID</th><th>Producto</th><th>Cantidad</th><th>Acciones</th></tr></thead><tbody>';
            if(resp.data.length === 0) {
                html += '<tr><td colspan="4" class="text-center">No hay pedidos registrados</td></tr>';
            } else {
                resp.data.forEach(function(pedido) {
                    html += `<tr><td>${pedido.id}</td><td>${pedido.Nombre_Prod ? pedido.Nombre_Prod : 'Sin nombre'}</td><td>${pedido.cantidad}</td><td><button class='btn btn-info btn-sm verDetalle' data-id='${pedido.id}'>Ver</button></td></tr>`;
                });
            }
            html += '</tbody></table></div>';
            $('#DataDePedidos').html(html);
        }, 'json');
    }

    // Acción para ver detalle
    $(document).on('click', '.verDetalle', function() {
        let id = $(this).data('id');
        $.post('Controladores/PedidosController.php', {accion: 'detalle', id: id}, function(resp) {
            console.log('Respuesta detalle pedido:', resp);
            let html = `<b>Producto:</b> ${resp.data.Nombre_Prod ? resp.data.Nombre_Prod : 'Sin nombre'}<br><b>Cantidad:</b> ${resp.data.cantidad}`;
            $('#detallePedidoBody').html(html);
            $('#modalDetallePedido').modal('show');
        }, 'json');
    });
});

// --- INICIO AUTOCOMPLETE Y AGREGADO DE PRODUCTOS AL MODAL DE NUEVO PEDIDO ---
$(document).ready(function() {
  // Autocompletado para el input de productos en el modal
  $('#busquedaProductoPedido').autocomplete({
    minLength: 3,
    source: function(request, response) {
      $.ajax({
        url: 'Controladores/autocompletado.php',
        type: 'GET',
        dataType: 'json',
        data: {
          term: request.term
        },
        success: function(data) {
          response(data);
        },
        error: function(xhr, status, error) {
          response([]);
        }
      });
    },
    select: function(event, ui) {
      if (ui.item.value) {
        // Buscar el producto completo usando el código o nombre
        buscarProductoCompleto(ui.item.value);
      }
      $('#busquedaProductoPedido').val('');
      return false;
    }
  });

  // Función para buscar el producto completo y agregarlo
  function buscarProductoCompleto(codigoONombre) {
    $.ajax({
      url: 'Controladores/escaner_articulo.php',
      type: 'POST',
      dataType: 'json',
      data: {
        codigoEscaneado: codigoONombre
      },
      success: function(data) {
        if (data && data.id) {
          agregarProductoAPedido(data);
        } else {
          alert('Producto no encontrado');
        }
      },
      error: function() {
        alert('Error al buscar el producto');
      }
    });
  }

  // Función para agregar producto a la tabla del pedido
  window.agregarProductoAPedido = function(producto) {
    // Verifica si ya está en la tabla
    if ($(`#tablaProductosPedido tbody tr[data-id='${producto.id}']`).length) {
      alert('El producto ya está en la lista.');
      return;
    }
    var fila = `
      <tr data-id="${producto.id}">
        <td>${producto.codigo || ''}</td>
        <td>${producto.descripcion || ''}</td>
        <td><input type="number" name="cantidad[]" class="form-control" value="1" min="1"></td>
        <td>${producto.precio || ''}</td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFilaPedido(this)">Eliminar</button></td>
        <input type="hidden" name="producto_id[]" value="${producto.id}">
      </tr>
    `;
    $('#tablaProductosPedido tbody').append(fila);
  };

  window.eliminarFilaPedido = function(btn) {
    $(btn).closest('tr').remove();
  };
});
// --- FIN AUTOCOMPLETE Y AGREGADO DE PRODUCTOS AL MODAL DE NUEVO PEDIDO --- 

// --- INICIO ESCANEO Y AUTOCOMPLETADO IGUAL QUE VENTAS ---
var isScannerInput = false;

$('#codigoEscaneado').keyup(function (event) {
  if (event.which === 13) { // Enter
    if (!isScannerInput) {
      var codigoEscaneado = $('#codigoEscaneado').val();
      buscarArticuloPedido(codigoEscaneado);
      event.preventDefault();
    }
    isScannerInput = false;
  }
});

$('#codigoEscaneado').autocomplete({
  source: function (request, response) {
    $.ajax({
      url: 'Controladores/autocompletado.php',
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

function buscarArticuloPedido(codigoEscaneado) {
  var formData = new FormData();
  formData.append('codigoEscaneado', codigoEscaneado);

  $.ajax({
    url: "Controladores/escaner_articulo.php",
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (data) {
      if ($.isEmptyObject(data)) {
        alert('Producto no encontrado');
      } else if (data.codigo || data.descripcion) {
        agregarProductoAPedido(data);
      }
      $('#codigoEscaneado').val('');
      $('#codigoEscaneado').focus();
    },
    error: function () {
      alert('Error en la búsqueda');
    }
  });
}
// --- FIN ESCANEO Y AUTOCOMPLETADO IGUAL QUE VENTAS --- 