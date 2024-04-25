<?php

include "../Controladores/ConsultaCaja.php";
$fcha = date("Y-m-d");
$user_id=null;
$sql1= "SELECT Venta_POS_ID,Folio_Ticket,Fk_Caja,Fk_sucursal FROM Ventas_POS WHERE Fk_Caja = '".$_POST['id']."'   order by  Venta_POS_ID ASC limit 1";
$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
 