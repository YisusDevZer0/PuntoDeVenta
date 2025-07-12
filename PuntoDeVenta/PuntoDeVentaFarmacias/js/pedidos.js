// Lógica básica para pedidos de usuario de ventas
$(document).ready(function() {
    // Cargar productos al abrir el modal
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
    // Enviar formulario con producto_id
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
    function cargarPedidos() {
        $.post('Controladores/PedidosController.php', {accion: 'listar'}, function(resp) {
            let data = JSON.parse(resp);
            let html = '<table class="table"><thead><tr><th>ID</th><th>Producto</th><th>Cantidad</th><th>Acciones</th></tr></thead><tbody>';
            data.data.forEach(function(pedido) {
                html += `<tr><td>${pedido.id}</td><td>${pedido.Nombre_Prod}</td><td>${pedido.cantidad}</td><td><button class='btn btn-info btn-sm verDetalle' data-id='${pedido.id}'>Ver</button></td></tr>`;
            });
            html += '</tbody></table>';
            $('#tablaPedidos').html(html);
        });
    }
    cargarPedidos();
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