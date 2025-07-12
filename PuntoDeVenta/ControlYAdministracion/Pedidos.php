<?php
include_once "Controladores/ControladorUsuario.php";
include "header.php";
?>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>
        <div class="container-fluid pt-4 px-8">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4">Órdenes de pedidos <?php echo $row['Licencia']?> Sucursal <?php echo $row['Nombre_Sucursal']?></h6>
                    <div class="text-center mb-3">
                        <button class="btn btn-primary" id="btnNuevoPedido">Nuevo pedido</button>
                    </div>
                    <div id="tablaPedidos"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modales -->
    <?php
    include "Modales/ModalNuevoPedido.php";
    include "Modales/ModalDetallePedido.php";
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php";
    ?>
    <script src="js/pedidos.js"></script>
</body>
</html> 