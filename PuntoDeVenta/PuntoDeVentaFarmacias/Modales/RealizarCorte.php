<?php

  include "../Controladores/db_connect.php.php";
  include "../Controladores/ControladorUsuario.php";
  include "../Controladores/ConsultaCaja.php";
  $user_id = null;
  $sql1 = "SELECT Venta_POS_ID,Folio_Ticket,Fk_Caja,Fk_sucursal FROM Ventas_POS WHERE Fk_Caja" . $_POST["id"];
  $query = $conn->query($sql1);
  $Especialistas = $query->fetch_object();
  ?>
  
  
  

  