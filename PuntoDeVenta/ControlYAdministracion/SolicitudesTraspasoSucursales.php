<?php
$currentPage = 'SolicitudesTraspasoSucursales';
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Solicitudes entre sucursales - <?php echo $row['Licencia']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">
                        <i class="fa-solid fa-inbox me-2"></i>Solicitudes entre sucursales
                    </h6>
                    <p class="text-muted small mb-3">
                        <?php
                        $esAdmin = ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT');
                        if ($esAdmin) {
                            echo 'Listado de todas las solicitudes de traspaso. La sucursal "Para sucursal" es quien debe atender la solicitud.';
                        } else {
                            echo 'Solicitudes dirigidas a tu sucursal. Aquí puedes ver qué productos otras sucursales te están pidiendo.';
                        }
                        ?>
                    </p>
                    <div id="DataDeServicios"></div>
                </div>
            </div>
        </div>
    </div>
    <?php include "Footer.php"; ?>
    <script src="js/SolicitudesTraspasoSucursales.js"></script>
</body>
</html>
