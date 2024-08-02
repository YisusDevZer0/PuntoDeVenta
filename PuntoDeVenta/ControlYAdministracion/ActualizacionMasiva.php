<?php
include('dbconect.php');
require_once('vendor/php-excel-reader/excel_reader2.php');
require_once('vendor/SpreadsheetReader.php');

if (isset($_POST["import"]))
{
    
$allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
  
  if(in_array($_FILES["file"]["type"],$allowedFileType)){

        $targetPath = 'subidas/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
        $Sucursal= mysqli_real_escape_string($con, $_POST["Sucursalnueva"]);
        $Tipo_ajuste= mysqli_real_escape_string($con, $_POST["TipAjuste"]);
        $Agrego= mysqli_real_escape_string($con, $_POST["Agrega"]);
        $Reader = new SpreadsheetReader($targetPath);
        
        $sheetCount = count($Reader->sheets());
        for($i=0;$i<$sheetCount;$i++)
        {
            
            $Reader->ChangeSheet($i);
            
            foreach ($Reader as $Row)
            {
          
                $Cod_Barra = "";
                if(isset($Row[0])) {
                    $Cod_Barra = mysqli_real_escape_string($con,$Row[0]);
                }
                
                $Nombre_prod = "";
                if(isset($Row[1])) {
                    $Nombre_prod = mysqli_real_escape_string($con,$Row[1]);
                }
				
                $Cantidad_Ajuste= "";
                if(isset($Row[2])) {
                    $Cantidad_Ajuste= mysqli_real_escape_string($con,$Row[2]);
                }
                $Existencia = "";
                if(isset($Row[2])) {
                    $Cantidad_Ajuste= mysqli_real_escape_string($con,$Row[2]);
                }
                
                if (!empty($Cod_Barra) || !empty($Nombre_prod) || !empty($Cantidad) || !empty($Sucursal)) {
                    $query = "INSERT INTO Inserciones_Excel_inventarios (Cod_Barra, Nombre_prod, Cantidad_Ajuste,ExistenciaReal, Sucursal,Tipo_ajuste, Agrego) values('".$Cod_Barra."','".$Nombre_prod."','".$Cantidad_Ajuste."','".$Existencia."','".$Sucursal."','".$Tipo_ajuste."','".$Agrego."')";
                    $resultados = mysqli_query($con, $query);
                
                    if (!empty($resultados)) {
                      $type = "success";
                      $message = "Excel importado correctamente";
                  } else {
                      $type = "error";
                      $message = "Hubo un problema al importar registros";
                  }
              }
          }
      }
      // Redirigir a una nueva página
      header("Location:https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/ResultadoActualizacionMasiva");
      exit(); // Asegura que el script se detenga después de la redirección
  } else { 
      $type = "error";
      $message = "El archivo enviado es inválido. Por favor vuelva a intentarlo";
  }
}
include_once "Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Listado de productos de <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
   <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
<body>
    
        <!-- Spinner End -->


        <?php include_once "Menu.php" ?>

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
        <?php include "navbar.php";?>
            <!-- Navbar End -->


            <!-- Table Start -->
          <div class="text-center">
            <div class="container-fluid pt-4 px-4">
    <div class="col-12">
     
            <h6 class="mb-4" style="color:#0172b6;">Base de productos de <?php echo $row['Licencia']?></h6> <br>
      


            <div id="DataDeProductos"></div>
            </div></div></div></div>
            </div>
          
<script src="js/ControlDeProductos.js"></script>

<script src="js/AltaProductosNuevos.js"></script>
            <!-- Footer Start -->
            <script>
  	
    $(document).ready(function() {
    // Delegación de eventos para el botón ".btn-edit" dentro de .dropdown-menu
    $(document).on("click", ".btn-EditarProd", function() {
    
        var id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EditaProductosGenerales.php", { id: id }, function(data) {
          $("#FormCajas").html(data);
            $("#TitulosCajas").html("Editar datos de productos");
            $("#Di").addClass("modal-dialog modal-xl modal-notify modal-warning");
        });
        $('#ModalEdDele').modal('show');
        });

  // Delegación de eventos para el botón ".btn-edit" dentro de .dropdown-menu
  $(document).on("click", ".btn-EliminarData", function() {
    
    var id = $(this).data("id");
    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EliminaProductosGeneral.php", { id: id }, function(data) {
      $("#FormCajas").html(data);
        $("#TitulosCajas").html("Eliminar datos");
        $("#Di").addClass("modal-dialog  modal-notify modal-warning");
    });
    $('#ModalEdDele').modal('show');
    });
        $(document).on("click", ".btn-CrearCodBar", function() {
    
    var id = $(this).data("id");
    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/GeneraCodigoDeBarrasProductos.php", { id: id }, function(data) {
      $("#FormCajas").html(data);
        $("#TitulosCajas").html("Generar codigos de barras");
        $("#Di").addClass("modal-dialog modal-lg modal-notify modal-warning");
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
  </div><!-- /.modal --></div></div>
            <?php 
            include "Modales/NuevoProductos.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>