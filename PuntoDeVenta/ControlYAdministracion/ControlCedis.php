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
<style>
        .swal2-popup {
            font-size: 1.2rem;
            color: #333;
        }
        .swal2-title {
            color: #d9534f; /* Color de título */
            font-weight: bold;
        }
        .swal2-input {
            border-radius: 4px;
            border: 1px solid #d9534f;
        }
        .swal2-confirm {
            background-color: #d9534f;
            border-color: #d9534f;
        }
        .swal2-confirm:hover {
            background-color: #c9302c;
            border-color: #ac2925;
        }
        .swal2-error {
            color: #d9534f;
        }
    </style>
</head>
<body>
<style>
        .swal2-popup {
            font-size: 1.2rem;
            color: #333;
        }
        .swal2-title {
            color: #d9534f; /* Color de título */
            font-weight: bold;
        }
        .swal2-input {
            border-radius: 4px;
            border: 1px solid #d9534f;
        }
        .swal2-confirm {
            background-color: #d9534f;
            border-color: #d9534f;
        }
        .swal2-confirm:hover {
            background-color: #c9302c;
            border-color: #ac2925;
        }
        .swal2-error {
            color: #d9534f;
        }
    </style>
</head>
<body>
    <script>
        function showAlertWithPassword() {
            const correctPassword = 'DoctorFishman'; // Cambia esto a tu contraseña secreta

            Swal.fire({
                title: 'Área en Mantenimiento',
                text: 'Esta área está actualmente en mantenimiento y no se puede usar (Si eres desarrollador por favor ingresa tu clave para continuar).',
                input: 'password',
                inputLabel: 'Ingresa la contraseña',
                inputPlaceholder: 'Contraseña',
                showCancelButton: false,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#d9534f',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    input: 'swal2-input',
                    confirmButton: 'swal2-confirm'
                },
                didOpen: () => {
                    Swal.getInput().focus();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value === correctPassword) {
                        Swal.fire({
                            title: '¡Acceso Permitido!',
                            text: 'Puedes proceder a utilizar la funcionalidad.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#5bc0de'
                        }).then(() => {
                            // Aquí puedes agregar lógica para redirigir a otra página si es necesario
                        });
                    } else {
                        Swal.fire({
                            title: 'Contraseña Incorrecta',
                            text: 'La contraseña ingresada es incorrecta. Inténtalo nuevamente.',
                            icon: 'error',
                            confirmButtonText: 'Intentar de nuevo',
                            confirmButtonColor: '#d9534f'
                        }).then(() => {
                            showAlertWithPassword(); // Mostrar la alerta nuevamente si la contraseña es incorrecta
                        });
                    }
                } else {
                    // No hacemos nada si el usuario cierra la alerta, solo la mostramos de nuevo
                    showAlertWithPassword();
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
            
          
<script src="js/ControlDeProductosCedis.js"></script>
<script>
  	
      $(document).ready(function() {
    
  
    // Delegación de eventos para el botón ".btn-edit" dentro de .dropdown-menu
    $(document).on("click", ".btn-EliminarData", function() {
      
      var id = $(this).data("id");
      $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EliminaProductosCedis.php", { id: id }, function(data) {
        $("#FormCajas").html(data);
          $("#TitulosCajas").html("Eliminar datos");
          $("#Di").addClass("modal-dialog  modal-notify modal-warning");
      });
      $('#ModalEdDele').modal('show');
      });
        
     
  });
  
  </script>
  
  <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
    <div id="Di"class="modal-dialog  modal-notify modal-success" >
      <div class="text-center">
        <div class="modal-content">
        <div class="modal-header" style=" background-color: #ef7980 !important;" >
           <p class="heading lead" id="TitulosCajas"  style="color:white;" ></p>
  
           
         </div>
          
              <div class="modal-body">
            <div class="text-center">
          <div id="FormCajas"></div>
          
          </div>
  
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
            <!-- Footer Start -->
            <?php 
            include "Modales/NuevoFondoDeCaja.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>