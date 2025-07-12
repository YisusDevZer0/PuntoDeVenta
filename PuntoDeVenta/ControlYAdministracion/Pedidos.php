<?php
// Vista principal de gestión de pedidos para administrativos
include 'header.php';
?>
<div class="container mt-4">
    <h2>Órdenes de pedidos</h2>
    <button class="btn btn-primary mb-3" id="btnNuevoPedido">Nuevo pedido</button>
    <div id="tablaPedidos"></div>
</div>
<?php include 'Footer.php'; ?>
<script src="js/pedidos.js"></script>
<?php
// ... existing code ... 