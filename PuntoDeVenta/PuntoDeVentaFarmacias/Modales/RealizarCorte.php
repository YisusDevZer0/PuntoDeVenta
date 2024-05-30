<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT Venta_POS_ID,Folio_Ticket,Fk_Caja,Fk_sucursal FROM Ventas_POS WHERE Fk_Caja = " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
?>

<?php if ($Especialistas) : ?>
  <?php if($query->num_rows>0):?>
  <div class="text-center">
 
  <div class="row">
    <div class="col">
  <label for="exampleFormControlInput1">Sucursal</label>
  <input type="text" class="form-control "  id="cantidadtotalventasss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas14->Nombre_Sucursal; ?>" aria-describedby="basic-addon1" >
  </div>
  <div class="col">
  <label for="exampleFormControlInput1">Turno</label>
  <input type="text" class="form-control "  id="cantidadtotalventasss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->Turno; ?>" aria-describedby="basic-addon1" >
  </div>  </div>
  <div class="row">
    <div class="col">
  <label for="exampleFormControlInput1">Cajero</label>
  <input type="text" class="form-control "  id="cantidadtotalventassss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->AgregadoPor; ?>" aria-describedby="basic-addon1" >
  </div> 
  <div class="col">
  <label for="exampleFormControlInput1">Total de venta</label>
  <input type="number" class="form-control "  id="cantidadtotalventassss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->VentaTotal; ?>" aria-describedby="basic-addon1" > 
  </div>  </div>
  <div class="row">
    <div class="col">
  <label for="exampleFormControlInput1">Total de tickets</label>
  <input type="text" class="form-control "  id="cantidadtotalventassss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->Total_tickets; ?>" aria-describedby="basic-addon1" >
  </div> 
  <div class="col">
  <label for="exampleFormControlInput1">Total de signos vitales</label>
  <input type="number" class="form-control "  id="cantidadtotalventasssss" name="TicketVentasTotl" step="any" readonly value="<?php echo $Especialistas3->Total_Folios; ?>" aria-describedby="basic-addon1" > 
  </div>  </div>
<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
