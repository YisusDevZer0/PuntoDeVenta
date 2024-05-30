<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id=null;
// CONSULTA 1 TODO OK
$sql1= "SELECT Venta_POS_ID,Folio_Ticket,Fk_Caja,Fk_sucursal,ID_H_O_D FROM Ventas_POS WHERE Fk_Caja = '".$_POST['id']."'  AND Fk_sucursal ='".$row['Fk_Sucursal']."' 
AND ID_H_O_D='".$row['ID_H_O_D']."' order by  Venta_POS_ID ASC limit 1";
$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
  // CONSULTA 2 OK
  $sql2= "SELECT Venta_POS_ID,Folio_Ticket,Fk_Caja,Fk_sucursal,ID_H_O_D FROM Ventas_POS WHERE Fk_Caja = '".$_POST['id']."'  AND Fk_sucursal ='".$row['Fk_Sucursal']."' 
  AND ID_H_O_D='".$row['ID_H_O_D']."' order by  Venta_POS_ID DESC limit 1";
  $query = $conn->query($sql2);
  $Especialistas2 = null;
  if($query->num_rows>0){
  while ($r=$query->fetch_object()){
    $Especialistas2=$r;
    break;
  }
  
    }
    // CONSULTA 3 OK
  $sql3= "SELECT Venta_POS_ID,Fk_Caja,Turno,Fecha_venta,Fk_sucursal,AgregadoPor,Turno,ID_H_O_D,COUNT(DISTINCT Folio_Ticket)AS Total_tickets,
  COUNT(DISTINCT FolioSignoVital ) AS Total_Folios,SUM(Importe) AS VentaTotal  FROM Ventas_POS where  Fk_sucursal ='".$row['Fk_Sucursal']."' 
 AND ID_H_O_D='".$row['ID_H_O_D']."' AND Fk_Caja = ".$_POST["id"];
$query = $conn->query($sql3);
$Especialistas3 = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas3=$r;
  break;
}

  }
      



  $sql14="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja,
  Ventas_POS.AgregadoEl,Sucursales.ID_Sucursal,Sucursales.Nombre_Sucursal,
  Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totaldeservicios FROM
   Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
   AND Ventas_POS.Fk_sucursal=Sucursales.ID_Sucursal AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' 
    GROUP by Servicios_POS.Servicio_ID";
  $query = $conn->query($sql14);
  $Especialistas14 = null;
  if($query->num_rows>0){
  while ($r=$query->fetch_object()){
    $Especialistas14=$r;
    break;
  }
  
    }

?>


<?php if($Especialistas!=null):?>

<?php if($Especialistas!=null):?>
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
  <?php endif;?>
  <?php else:?>
  
<?php endif;?>
  
  <?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>