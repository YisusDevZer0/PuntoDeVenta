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


  $sql33= "SELECT Venta_POS_ID,Fk_Caja,Turno,Fecha_venta,Fk_sucursal,AgregadoPor,Turno,ID_H_O_D,COUNT(DISTINCT Folio_Ticket)AS Total_tickets,
  COUNT(DISTINCT FolioSignoVital ) AS Total_Folios,SUM(Importe) AS VentaTotaldeEfectivoPAraElcorte  FROM Ventas_POS where FormaDePago='Efectivo' AND Fk_sucursal ='".$row['Fk_Sucursal']."' 
 AND ID_H_O_D='".$row['ID_H_O_D']."' AND Fk_Caja = ".$_POST["id"];
$query = $conn->query($sql33);
$Especialistas33 = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas33=$r;
  break;
}

  }

  
  
    
    $sql6="SELECT Venta_POS_ID,Fk_Caja,Fk_sucursal,Turno,ID_H_O_D,COUNT( DISTINCT Folio_Ticket) AS Total_tickets,SUM(Importe) AS VentaTotalCredito  FROM Ventas_POS where FormaDePago='Crédito Enfermería'  AND Fk_sucursal ='".$row['Fk_Sucursal']."' 
    AND ID_H_O_D='".$row['ID_H_O_D']."' AND Fk_Caja = ".$_POST["id"];
    $query = $conn->query($sql6);
    $Especialistas6 = null;
    if($query->num_rows>0){
    while ($r=$query->fetch_object()){
      $Especialistas6=$r;
      break;
    }
    
      }

      $sql7="SELECT Venta_POS_ID,Fk_Caja,Fk_sucursal,Turno,ID_H_O_D,COUNT( DISTINCT Folio_Ticket) AS Total_tickets,SUM(Importe) AS VentaTotalCreditoLimpieza  FROM Ventas_POS where FormaDePago='Crédito Limpieza'  AND Fk_sucursal ='".$row['Fk_Sucursal']."' 
    AND ID_H_O_D='".$row['ID_H_O_D']."' AND Fk_Caja = ".$_POST["id"];
    $query = $conn->query($sql7);
    $Especialistas7 = null;
    if($query->num_rows>0){
    while ($r=$query->fetch_object()){
      $Especialistas7=$r;
      break;
    }
    
      }
      $sql11="SELECT Venta_POS_ID,Fk_Caja,Fk_sucursal,Turno,ID_H_O_D,COUNT( DISTINCT Folio_Ticket) AS Total_tickets,SUM(Importe) AS VentaTotalCreditoFarmaceutico FROM Ventas_POS where FormaDePago='Crédito Farmacéutico'  AND Fk_sucursal ='".$row['Fk_Sucursal']."' 
      AND ID_H_O_D='".$row['ID_H_O_D']."' AND Fk_Caja = ".$_POST["id"];
      $query = $conn->query($sql11);
      $Especialistas11 = null;
      if($query->num_rows>0){
      while ($r=$query->fetch_object()){
        $Especialistas11=$r;
        break;
      }
      
        }

        $sql12="SELECT Venta_POS_ID,Fk_Caja,Fk_sucursal,Turno,ID_H_O_D,COUNT( DISTINCT Folio_Ticket) AS Total_tickets,SUM(Importe) AS VentaTotalCreditoMedicos FROM Ventas_POS where FormaDePago='Crédito Médico'  AND Fk_sucursal ='".$row['Fk_Sucursal']."' 
        AND ID_H_O_D='".$row['ID_H_O_D']."' AND Fk_Caja = ".$_POST["id"];
        $query = $conn->query($sql12);
        $Especialistas12 = null;
        if($query->num_rows>0){
        while ($r=$query->fetch_object()){
          $Especialistas12=$r;
          break;
        }
        
          }


      
?>

<?php if($Especialistas!=null):?>

  
<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>