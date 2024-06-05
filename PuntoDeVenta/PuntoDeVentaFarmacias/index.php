<?php
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Pantalla de inicio punto de venta <?php echo $row['Licencia']?> </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Incluir dependencias de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <?php
    include "header.php";
    ?>
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <?php include_once "Menu.php"; ?>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <?php include "navbar.php"; ?>
            <!-- Navbar End -->

            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa-solid fa-capsules fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Productos</p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ConsultaProductos">Consultar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa-solid fa-right-left fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Traspasos</p>
                                <button type="button" class="btn btn-primary" id="openModalBtn">Consultar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid pt-4 px-8">
                    <div class="col-12">
                        <div class="bg-light rounded h-100 p-4">
                            <h6 class="mb-4" style="color:#0172b6;">Mensajes o recordatorios de <?php echo $row['Licencia'] ?> Sucursal <?php echo $row['Nombre_Sucursal'] ?></h6>
                            <div class="text-center">
                                <div id="Cajas"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sale & Revenue End -->
            </div>
        </div>

      <?php include "Modales/ConsultaProductosRapidos.php"?>

        <script src="js/Recordatorios_mensajes.js"></script>

        <?php
        include "Footer.php";
        ?>
    </div>
</body>

</html>
