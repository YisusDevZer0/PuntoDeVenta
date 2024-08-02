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
        <style>
        .error {
  color: red;
  margin-left: 5px; 
  
        }
        #frmExcelImport {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        input[type="file"] {
            margin-bottom: 10px;
        }

        button.btn-submit {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        select {
            padding: 8px;
            margin-right: 10px;
        }


    </style>


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
      

            <script type="text/javascript">
$(document).ready( function () {
    $('#resultadosinventarios').DataTable({
      "order": [[ 0, "desc" ]],
      "paging": true, // Activar paginación
      "lengthMenu": [10, 25, 50, 75, 100], // Opciones de longitud de página
      "stateSave": true,
      "language": {
        "url": "Componentes/Spanish.json"
      }
    });
});

</script>
<!-- MOBILIARIO VIGENTE -->
<div class="tab-pane fade show active" id="MobiVigente" role="tabpanel" aria-labelledby="pills-profile-tab">
  <div class="card text-center">
    <div class="card-header" style="background-color:#2b73bb !important;color: white;">
      Actualizacion de inventarios
    </div>

    <div class="card-body">
    <form action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
    <div>
        <label for="file">Elija Archivo Excel</label>
        <input type="file" name="file" id="file" accept=".xls, .xlsx">
       
    </div>

    <label for="Sucursalnueva">Seleccione una Sucursal:</label>
    <select id="Sucursalnueva" class="form-control" name="Sucursalnueva">
        <option value="">Seleccione una Sucursal:</option>
        <?php
          $query = $conn->query("SELECT ID_SucursalC, Nombre_Sucursal, ID_H_O_D FROM SucursalesCorre WHERE  ID_H_O_D='".$row['ID_H_O_D']."'");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["ID_SucursalC"].'">'.$valores["Nombre_Sucursal"].'</option>';
          }
        ?>
    </select> <br>
    <select id="TipAjuste" class="form-control" name="TipAjuste">
        <option value="">Especifique el tipo de ajuste que se realizara</option>
        <option value="Inventario inicial">Inventario inicial</option>
        <option value="Ajuste de inventario">Ajuste de inventario</option>
    </select>
    <input type="text" hidden name="Agrega" value="<?php echo $row['Nombre_Apellidos']?>">

    <br>
    <button type="submit" id="submit" name="import" class="btn-submit">Importar Registros</button>
</form>
    </div>
    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>
    
         

   </div>
   </div>
   </div>
   </div>
   </div>
          


<script src="js/AltaProductosNuevos.js"></script>
            <!-- Footer Start -->
         
            <?php 
            include "Modales/NuevoProductos.php";
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
</body>

</html>