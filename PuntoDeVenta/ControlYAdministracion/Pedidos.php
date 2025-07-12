<?php
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Órdenes de pedidos de <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
    <div id="loading-overlay">
      <div class="loader"></div>
      <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">Órdenes de pedidos <?php echo $row['Licencia']?> Sucursal <?php echo $row['Nombre_Sucursal']?></h6>
                    <div class="text-center mb-3">
                        <button class="btn btn-primary" id="btnNuevoPedido">Nuevo pedido</button>
                    </div>
                    <div id="DataDePedidos"></div>
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