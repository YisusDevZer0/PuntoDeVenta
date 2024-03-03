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
                    <img class="img-fluid rounded-circle mx-auto mb-4"  src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo $row['file_name']?>" style="width: 100px; height: 100px;">
                    <div class="row mb-3">
                    <form id="NewTypeUser">
                    <h5 class="mb-1">Tu Nombre</h5>
                    <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">@</span>
                                <input type="text" class="form-control" placeholder="Username" aria-label="Username"
                                    aria-describedby="basic-addon1" value="<?php echo $row['Nombre_Apellidos']?>">
                            </div>
                                </div>
                                <h5 class="mb-1">Tu Sucursal actual</h5>
                                <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">@</span>
                                <input type="text" class="form-control" placeholder="Sucursal" aria-label="Username"
                                    aria-describedby="basic-addon1" value="<?php echo $row['Nombre_Apellidos']?>">
                            </div>
                            <h5 class="mb-1">Correo Electronico</h5>
                                <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">@</span>
                                <input type="text" class="form-control" placeholder="Sucursal" aria-label="Username"
                                    aria-describedby="basic-addon1" value="<?php echo $row['Correo_Electronico']?>">
                            </div>
                            <h5 class="mb-1">Contraseña</h5>
                                <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">@</span>
                                <input type="text" class="form-control" placeholder="Sucursal" aria-label="Username"
                                    aria-describedby="basic-addon1" value="<?php echo $row['Password']?>">
                            </div>
                
                    </ul>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
              </form>
                </div>
            </div>
        </div>
    </div>
</div>


            <?php 
         
            include "Footer.php";?>
</body>

</html>