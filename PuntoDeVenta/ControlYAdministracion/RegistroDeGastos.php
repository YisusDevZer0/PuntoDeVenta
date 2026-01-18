
<?php
include_once "Controladores/ControladorUsuario.php";

// Obtener el valor de Fk_Sucursal y Nombre_Apellidos
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';
$nombre_apellidos = isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : '';

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT Cajas.ID_Caja, Cajas.Cantidad_Fondo, Cajas.Empleado, Cajas.Sucursal,
       Cajas.Estatus, Cajas.CodigoEstatus, Cajas.Turno, Cajas.Asignacion, Cajas.Fecha_Apertura,
       Cajas.Valor_Total_Caja, Cajas.Licencia, Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
FROM Cajas
INNER JOIN Sucursales ON Cajas.Sucursal = Sucursales.ID_Sucursal
WHERE Cajas.Sucursal = ? 
  AND Cajas.Empleado = ? 
  AND Cajas.Estatus = 'Abierta';";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ss", $fk_sucursal, $nombre_apellidos);
    $stmt->execute();
    $result = $stmt->get_result();
    $ValorCaja = $result->fetch_assoc();
    $stmt->close();
} else {
    // Si hay error en la preparación, usar consulta directa como fallback
    $sql_fallback = "SELECT Cajas.ID_Caja, Cajas.Cantidad_Fondo, Cajas.Empleado, Cajas.Sucursal,
           Cajas.Estatus, Cajas.CodigoEstatus, Cajas.Turno, Cajas.Asignacion, Cajas.Fecha_Apertura,
           Cajas.Valor_Total_Caja, Cajas.Licencia, Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
    FROM Cajas
    INNER JOIN Sucursales ON Cajas.Sucursal = Sucursales.ID_Sucursal
    WHERE Cajas.Sucursal = '$fk_sucursal' 
      AND Cajas.Empleado = '$nombre_apellidos' 
      AND Cajas.Estatus = 'Abierta'";
    $result = mysqli_query($conn, $sql_fallback);
    $ValorCaja = mysqli_fetch_assoc($result);
}

// Si no hay caja activa, crear un array vacío para evitar errores
if (!$ValorCaja) {
    $ValorCaja = array('ID_Caja' => '0');
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Registro de gastos <?php echo $row['Licencia']?></title>
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
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4" style="color:#0172b6;">Registro de gastos <?php echo $row['Licencia']?></h6>
            <button type="button" class="btn btn-primary btn-registraGasto" data-id="<?php echo $ValorCaja['ID_Caja']; ?>">
  Agregar nuevo gasto
</button> <br>
            <div id="DataDeServicios"></div>
            </div></div></div></div>
            </div>
          
<script src="js/ControlDeGastosRegistrados.js"></script>


            <!-- Footer Start -->
            <?php 
            include "Modales/AltaNuevoUsuario.php";
           
            include "Modales/Modales_Referencias.php";
            include "Footer.php";?>

<script>
   $(document).ready(function() {
    // Manejar el clic del botón para registrar gastos
    $(document).on("click", ".btn-registraGasto", function() {
        var id = $(this).data("id");
        // Validar que el ID sea válido
        if (id && id != '0') {
            $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/RegistrarGasto.php", {
                id: id
            }, function(data) {
                $("#FormCajas").html(data);
                $("#TitulosCajas").html("Registrar nuevo gasto");
            });
            $('#ModalEdDele').modal('show');
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'No hay caja activa',
                text: 'No hay caja activa para registrar gastos. Debe abrir una caja primero.',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#dc3545'
            });
        }
    });

    // Delegación de eventos para el botón "btn-Movimientos" dentro de .dropdown-menu
    $(document).on("click", ".btn-edita", function() {
        var id = $(this).data("id");
        console.log("Botón de cancelar clickeado para el ID:", id);
        $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EditaTipoDeUsuario.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Editar servicios");
            
        });
        $('#ModalEdDele').modal('show');
    });

    // Delegación de eventos para el botón "btn-Ventas" dentro de .dropdown-menu
    $(document).on("click", ".btn-elimina", function() {
        var id = $(this).data("id");
        $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EliminarTipoUsuario.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Eliminar servicio");
           
        });
        $('#ModalEdDele').modal('show');
    });

   
});

</script>

  <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
  <div id="CajasDi"class="modal-dialog  modal-notify modal-success" >
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
  </div><!-- /.modal --></div>
</body>

</html>