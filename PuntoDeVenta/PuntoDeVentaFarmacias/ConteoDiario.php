<?php
include_once "Controladores/ControladorUsuario.php";

$sql1="SELECT * 
FROM `Stock_POS` 
WHERE Tipo_Servicio = 24 
  AND Fk_sucursal = '".$row['Fk_Sucursal']."' 
  AND Tipo_Servicio NOT IN (3, 6, 7, 8, 10, 11, 12) 
ORDER BY RAND() 
LIMIT 50;
";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Ingreso de medicamentos de <?php echo $row['Licencia']?></title>
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
          
            <div class="container-fluid pt-4 px-4">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Base de productos de <?php echo $row['Licencia']?></h6>
            
            <script type="text/javascript">
$(document).ready( function () {
    var printCounter = 0;
    $('#StockSucursalesDistribucion').DataTable({
      "order": [[ 0, "desc" ]],
      "lengthMenu": [[30], [30]],   
        language: {
            "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
			     },
			     "sProcessing":"Procesando...",
            },
          
         //para usar los botones   
         responsive: "true",
         
       
			
		
        
       
   
	   
        	        
    });     
});
   
	 
</script>
<?php
;


$user_id=null;

$query = $conn->query($sql1);
?>

<?php if($query->num_rows>0):?>
    <form action="javascript:void(0)" method="post" id="RegistraConteoDelDia">
        
  <div class="text-center">
<button type="submit"   id="EnviarDatos" value="Guardar" class="btn btn-success">Guardar <i class="fas fa-save"></i></button>
	<div class="table-responsive">
	<table  id="StockSucursalesDistribucion" class="table table-hover">
<thead>
<th>Codigo</th>
<th>Nombre</th>





<th>Stock Fisico</th>


   

	


</thead>
<?php while ($Usuarios=$query->fetch_array()):?>
<tr>


<td><input type="text" class="form-control" name="CodBarra[]" value="<?php echo $Usuarios["Cod_Barra"]; ?>" readonly></td>
<td><input type="text" class="form-control" name="NombreProd[]" value="<?php echo $Usuarios["Nombre_Prod"]; ?>" readonly></td>
    <td style="display: none;" ><input type="text" name="StockEnEseMomento[]"class="form-control" value="<?php echo $Usuarios["Existencias_R"]; ?>" readonly></td>
    
    <td style="display: none;"><input type="text" name="Agrego[]"class="form-control" value="<?php echo $row['Nombre_Apellidos']?>" readonly></td>
    <td style="display: none;"><input type="text" name="Sucursal[]"class="form-control" value="<?php echo $row['Fk_Sucursal']?>" readonly></td>
    <td><input type="number" class="form-control" name="StockFisico[]"></td>
    
    
    </form>
   
		
</tr>
<?php endwhile;?>
</form>
</table>
</div>
</div>
<?php else:?>
	<p class="alert alert-warning">No se encontraron coincidencias</p>
<?php endif;?>
</div>
</div>
</div>
</div>







            </div></div></div></div>
            
          
<script src="js/IngresoDeMedicamentos.js"></script>

            <!-- Footer Start -->
            <?php 
            
            include "Footer.php";?>
</body>

</html>