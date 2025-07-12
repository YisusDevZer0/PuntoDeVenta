// Lógica básica para pedidos administrativos
$(document).ready(function() {
    // Cargar tabla de pedidos al iniciar
    cargarPedidos();

    // Botón para nuevo pedido
    $('#btnNuevoPedido').on('click', function() {
        $.post('Controladores/PedidosController.php', {accion: 'productos'}, function(resp) {
            let data = JSON.parse(resp);
            let select = '<select class="form-control" id="producto_id" name="producto_id" required>';
            data.data.forEach(function(prod) {
                select += `<option value="${prod.ID_Prod_POS}">${prod.Nombre_Prod}</option>`;
            });
            select += '</select>';
            $('#producto').replaceWith(select);
        });
        $('#modalNuevoPedido').modal('show');
    });

    // Enviar formulario de nuevo pedido
    $('#formNuevoPedido').on('submit', function(e) {
        e.preventDefault();
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