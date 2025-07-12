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
                    success: function(data) {
                        response($.map(data.data, function(item) {
                            return {
                                label: item.Nombre_Prod,
                                value: item.Nombre_Prod,
                                id: item.ID_Prod_POS
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $('#producto_id').val(ui.item.id);
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
            let data = JSON.parse(resp);
            if(data.status === 'ok') {
                $('#modalNuevoPedido').modal('hide');
                cargarPedidos();
            } else {
                alert('Error al crear pedido');
            }
        });
    });

    // Función para cargar la tabla de pedidos
    function cargarPedidos() {
        $.post('Controladores/PedidosController.php', {accion: 'listar'}, function(resp) {
            let data = JSON.parse(resp);
            let html = '<div class="table-responsive"><table class="table table-striped table-hover"><thead class="table-primary"><tr><th>ID</th><th>Producto</th><th>Cantidad</th><th>Acciones</th></tr></thead><tbody>';
            if(data.data.length === 0) {
                html += '<tr><td colspan="4" class="text-center">No hay pedidos registrados</td></tr>';
            } else {
                data.data.forEach(function(pedido) {
                    html += `<tr><td>${pedido.id}</td><td>${pedido.Nombre_Prod}</td><td>${pedido.cantidad}</td><td><button class='btn btn-info btn-sm verDetalle' data-id='${pedido.id}'>Ver</button></td></tr>`;
                });
            }
            html += '</tbody></table></div>';
            $('#DataDePedidos').html(html);
        });
    }

    // Acción para ver detalle
    $(document).on('click', '.verDetalle', function() {
        let id = $(this).data('id');
        $.post('Controladores/PedidosController.php', {accion: 'detalle', id: id}, function(resp) {
            let data = JSON.parse(resp);
            let html = `<b>Producto:</b> ${data.data.Nombre_Prod}<br><b>Cantidad:</b> ${data.data.cantidad}`;
            $('#detallePedidoBody').html(html);
            $('#modalDetallePedido').modal('show');
        });
    });
}); 