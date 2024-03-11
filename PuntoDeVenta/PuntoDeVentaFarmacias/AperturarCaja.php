<?php
include_once "Controladores/ControladorUsuario.php";
$sql ="SELECT Fondos_Cajas.ID_Fon_Caja,Fondos_Cajas.Fk_Sucursal,Fondos_Cajas.Fondo_Caja, 
Fondos_Cajas.Estatus, 
Sucursales.ID_Sucursal,Sucursales.Nombre_Sucursal FROM 
Fondos_Cajas,Sucursales where Fondos_Cajas.Fk_Sucursal = Sucursales.ID_Sucursal
 AND Fondos_Cajas.Fk_Sucursal='".$row['Fk_Sucursal']."' AND Fondos_Cajas.Estatus ='Activo' ";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$ValorFondoCaja = mysqli_fetch_assoc($resultset);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Fondos de cajas disponibles para  <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
</div>
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


            <!-- Table Start -->
          
            <div class="container-fluid pt-4 px-8">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Administracion de cajas de  <?php echo $row['Licencia']?> Sucursal <?php echo $row['Nombre_Sucursal']?></h6>
            <div class="text-center">
            <button data-id="<?php echo $ValorFondoCaja["ID_Fon_Caja"];?>" class="btn-editcaja btn btn-success">
  Aperturar nueva caja </button> <br>
<div id="Cajas"></div>
            </div></div></div></div>
            </div>
            <script src="js/Cajas.js"></script>
         
            <!-- Footer Start -->
            <?php 
          
            include "Modales/Modales_Errores.php";
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>
           <script>
    $(".btn-editcaja").click(function(){
        id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/AbreCaja.php", "id=" + id, function(data){
            $("#form-edit").html(data);
            $("#Titulo").html("Apertura de caja");
            $("#Di").addClass("modal-dialog modal-lg modal-notify modal-success");
        });
        $('#editModal').modal('show');
    });
</script>


<div class="modal fade" id="editModal" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="editModalLabel" aria-hidden="true">
    <div id="Di" class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="Titulo" style="color:white;">Apertura de caja</h5>
                
            </div>
            <div class="modal-body">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong><?php echo $row['Nombre_Apellidos']; ?></strong> Verifique los campos antes de realizar alguna acción.
                  
                </div>
                <div id="form-edit">
                    <!-- Contenido del formulario -->
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>