<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/obtenertoken.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Tipos de usuarios <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <?php
   include "header.php";?>
<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->


            <!-- Table Start -->
          
            <div class="container-fluid pt-4 px-4">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Tipos de Usuarios</h6>
            <div class="table-responsive">
                <div class="text-center">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
  Agregar Usuario
</button>
</div>

                <table class="table" id="userTable">
                    <thead>
                        <tr>
                            <th>ID User</th>
                            <th>Tipo de Usuario</th>
                            <th>Licencia</th>
                            <th>Creado el</th>
                            <th>Creado</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="js/TablaUsuarios.js"></script>
<script src="js/AgregarTipoDeusuarios.js"></script>

            </div>   </div>
            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoTipoUsuario.php";
            include "Footer.php";?>
</body>

</html>