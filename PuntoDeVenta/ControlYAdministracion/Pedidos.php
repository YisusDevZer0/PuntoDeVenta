<?php
include_once "Controladores/ControladorUsuario.php";
include "Controladores/db_connect.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Órdenes de pedidos de <?php echo $row['Licencia']?> - Sucursal <?php echo $row['Nombre_Sucursal']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
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
                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <div class="form-group mb-2">
                                <label class="col-form-label" for="iptCodigoPedido">
                                    <i class="fas fa-barcode fs-6"></i>
                                    <span class="small">Productos</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control producto" name="codigoEscaneado" id="codigoEscaneado" style="position: relative;" placeholder="Escanea o escribe el producto para el pedido">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tablaAgregarArticulos" class="display">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th style="width:30%">Producto</th>
                                            <th style="width:6%">Cantidad</th>
                                            <th>Precio</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Aquí se agregan los productos del pedido -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mb-3">
                        <button class="btn btn-primary" id="btnGuardarPedido">Guardar pedido</button>
                    </div>
                    <div id="DataDePedidos"></div>
                </div>
            </div>
        </div>
    </div>
    <?php include "Footer.php"; ?>
    <script src="js/pedidos.js"></script>
</body>
</html> 