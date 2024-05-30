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
      
?>


<?php if($Especialistas!=null):?>

<?php if($Especialistas!=null):?>
  
  <?php else:?>
  
<?php endif;?>
  
  <?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>