<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Fondos de cajas disponibles para  <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
<body>
    
        <!-- Spinner End -->


        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->
            <div class="container"> <!-- Agrega un contenedor para centrar el contenido -->
    <div class="row justify-content-center"> <!-- Centra el contenido horizontalmente -->
        <div class="col-sm-12 col-xl-6">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">Datos de la Cuenta</h6>
                <div class="account-info">
                    <img class="img-fluid rounded-circle mx-auto mb-4" src="tu_imagen_de_perfil.jpg" style="width: 100px; height: 100px;">
                    <div class="row mb-3">
                    <h5 class="mb-1">Tu Nombre</h5>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="inputPassword3">
                                    </div>
                                </div>
                    
                
                    <p>Tu Profesión</p>
                    <p class="mb-0">Información adicional sobre tu cuenta, como detalles de contacto, membresía, etc.</p>
                    <ul>
                        <li>Dato 1: Valor 1</li>
                        <li>Dato 2: Valor 2</li>
                        <!-- Agrega más datos según sea necesario -->
                    </ul>
                    <button class="btn btn-primary">Editar</button>
                </div>
            </div>
        </div>
    </div>
</div>


            <?php 
         
            include "Footer.php";?>
</body>

</html>