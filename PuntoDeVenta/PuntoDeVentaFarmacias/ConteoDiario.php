<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar que $row esté definido
if (!isset($row['Fk_Sucursal']) || !isset($row['Licencia'])) {
    die("Error: No se ha iniciado sesión correctamente");
}

// Optimizar la consulta SQL usando índices y limitando los campos necesarios
$sql1 = "SELECT Cod_Barra, Nombre_Prod, Existencias_R 
         FROM `Stock_POS` 
         WHERE Fk_sucursal = ? 
         AND Tipo_Servicio = 5
         AND Cod_Barra IS NOT NULL 
         AND Cod_Barra != ''
         ORDER BY Cod_Barra 
         LIMIT 50";

// Preparar la consulta para prevenir SQL injection
$stmt = $conn->prepare($sql1);
$stmt->bind_param("s", $row['Fk_Sucursal']);
$stmt->execute();
$query = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Conteo Diario - <?php echo htmlspecialchars($row['Licencia']); ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   

    <?php
   include "header.php";?>
   <style>
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        #loading-text {
            color: white;
            font-size: 18px;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text">Cargando datos...</div>
    </div>

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
            <h6 class="mb-4" style="color:#0172b6;">Conteo Diario - <?php echo htmlspecialchars($row['Licencia']); ?></h6>
            
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





<th>Existencias</th>


   

	


</thead>
<?php while ($producto = $query->fetch_assoc()):?>
<tr>


<td><input type="text" class="form-control" name="CodBarra[]" value="<?php echo htmlspecialchars($producto['Cod_Barra']); ?>" readonly></td>
<td><input type="text" class="form-control" name="NombreProd[]" value="<?php echo htmlspecialchars($producto['Nombre_Prod']); ?>" readonly></td>
    <td style="display: none;" ><input type="text" name="StockEnEseMomento[]"class="form-control" value="<?php echo htmlspecialchars($producto['Existencias_R']); ?>" readonly></td>
    
    <td style="display: none;"><input type="text" name="Agrego[]"class="form-control" value="<?php echo htmlspecialchars($row['Nombre_Apellidos']); ?>" readonly></td>
    <td style="display: none;"><input type="text" name="Sucursal[]"class="form-control" value="<?php echo htmlspecialchars($row['Fk_Sucursal']); ?>" readonly></td>
    <td><input type="number" class="form-control" name="StockFisico[]" min="0" step="1" required></td>
    
    
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

    <script>
    $(document).ready(function() {
        // Función para mostrar el loading
        function showLoading(message = 'Cargando datos...') {
            $('#loading-text').text(message);
            $('#loading-overlay').css('display', 'flex');
        }

        // Función para ocultar el loading
        function hideLoading() {
            $('#loading-overlay').css('display', 'none');
        }

        // Destruir la tabla si ya existe
        if ($.fn.DataTable.isDataTable('#StockSucursalesDistribucion')) {
            $('#StockSucursalesDistribucion').DataTable().destroy();
        }

        // Mostrar loading al cargar la página
        showLoading('Inicializando tabla...');

        // Inicializar DataTable con loading
        var table = $('#StockSucursalesDistribucion').DataTable({
            "destroy": true, // Permite reinicializar la tabla
            "retrieve": true, // Recupera la instancia existente si existe
            "order": [[0, "desc"]],
            "lengthMenu": [[30], [30]],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "processing": "Procesando..."
            },
            "responsive": true,
            "initComplete": function() {
                hideLoading();
            }
        });

        // Mostrar loading al enviar el formulario
        $('#RegistraConteoDelDia').on('submit', function() {
            showLoading('Guardando datos...');
        });

        // Mostrar loading al buscar
        table.on('search.dt', function() {
            showLoading('Buscando...');
        });

        // Ocultar loading cuando termina la búsqueda
        table.on('search.dt', function() {
            setTimeout(hideLoading, 500);
        });

        // Mostrar loading al cambiar de página
        table.on('page.dt', function() {
            showLoading('Cargando página...');
        });

        // Ocultar loading cuando termina de cargar la página
        table.on('page.dt', function() {
            setTimeout(hideLoading, 500);
        });
    });
    </script>
</body>

</html>