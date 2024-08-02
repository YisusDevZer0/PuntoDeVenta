<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de productos en cedis <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
   <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
<script>
        function showAlertWithPassword() {
            const correctPassword = 'DoctorFishman'; // Cambia esto a tu contraseña secreta

            Swal.fire({
                title: 'Alerta',
                text: 'Esta alerta no puede ser cerrada sin la contraseña.',
                input: 'password',
                inputLabel: 'Ingresa la contraseña',
                inputPlaceholder: 'Contraseña',
                showCancelButton: false,
                confirmButtonText: 'Aceptar',
                didOpen: () => {
                    Swal.getInput().focus();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value === correctPassword) {
                        Swal.fire('¡Correcto!', 'Puedes cerrar la alerta.', 'success');
                    } else {
                        Swal.fire('Incorrecto', 'La contraseña es incorrecta.', 'error').then(() => {
                            showAlertWithPassword(); // Mostrar la alerta nuevamente si la contraseña es incorrecta
                        });
                    }
                } else {
                    showAlertWithPassword(); // Mostrar la alerta nuevamente si se intenta cerrar
                }
            });
        }

        // Mostrar la alerta cuando se carga la página
        showAlertWithPassword();
    </script>
<body>
    
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
            <h6 class="mb-4" style="color:#0172b6;">lista de productos de cedis de <?php echo $row['Licencia']?></h6>
            
            <div id="DataDeProductos"></div>
            </div></div></div></div>
            
          
<script src="js/ControlDeProductos.js"></script>

            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>