// Lógica básica para pedidos de usuario de ventas
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