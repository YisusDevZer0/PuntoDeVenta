<?php
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Movimientos de inventario - <?php echo $row['Licencia']; ?></title>
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="mb-0" style="color:#0172b6;">
                            <i class="fa-solid fa-arrows-rotate me-2"></i>
                            Movimientos de inventario - <?php echo $row['Licencia']; ?>
                        </h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm" onclick="CargarMovimientosInventario()">
                                <i class="fa-solid fa-refresh me-1"></i>Actualizar
                            </button>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-2">
                            <label class="form-label">Sucursal:</label>
                            <select class="form-select form-select-sm" id="filtroSucursal" onchange="CargarMovimientosInventario()">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipo origen:</label>
                            <select class="form-select form-select-sm" id="filtroTipoRef" onchange="CargarMovimientosInventario()">
                                <option value="">Todos</option>
                                <option value="sale">Venta</option>
                                <option value="ingreso">Ingreso</option>
                                <option value="transfer">Traspaso</option>
                                <option value="traspaso_salida">Traspaso salida</option>
                                <option value="traspaso_entrada">Traspaso entrada</option>
                                <option value="count">Conteo</option>
                                <option value="inventario_sucursal">Inventario sucursal</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha desde:</label>
                            <input type="date" class="form-control form-control-sm" id="fechaDesde" onchange="CargarMovimientosInventario()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha hasta:</label>
                            <input type="date" class="form-control form-control-sm" id="fechaHasta" onchange="CargarMovimientosInventario()">
                        </div>
                    </div>

                    <div id="DataMovimientosInventario"></div>
                </div>
            </div>
        </div>

        <script src="js/MovimientosInventario.js"></script>

        <?php
        include "Modales/Modales_Errores.php";
        include "Modales/Modales_Referencias.php";
        include "Footer.php";
        ?>
    </div>
</body>
</html>
