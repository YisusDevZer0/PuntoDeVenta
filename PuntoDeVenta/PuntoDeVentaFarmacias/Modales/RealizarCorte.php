<?php

include "../Controladores/ConsultaCaja.php";
$fcha = date("Y-m-d");
$user_id=null;
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

  $sql4= "SELECT Identificador_tipo,Fk_Caja,SUM(Importe) as totaldentalescreditos FROM `Ventas_POS` WHERE Identificador_tipo='Cr&eacute;ditos' AND Fk_Caja = ".$_POST["id"];
  $query = $conn->query($sql4);
  $Especialistas4 = null;
  if($query->num_rows>0){
  while ($r=$query->fetch_object()){
    $Especialistas4=$r;
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


          $sql13="SELECT * FROM `Cortes_Cajas_POS` where  Sucursal ='".$row['Fk_Sucursal']."' 
          AND ID_H_O_D='".$row['ID_H_O_D']."' AND Fk_Caja = ".$_POST["id"];
          $query = $conn->query($sql13);
          $Especialistas13 = null;
          if($query->num_rows>0){
          while ($r=$query->fetch_object()){
            $Especialistas13=$r;
            break;
          }
          
            }

            $sql14="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja,
            Ventas_POS.AgregadoEl,SucursalesCorre.ID_SucursalC,SucursalesCorre.Nombre_Sucursal,
            Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totaldeservicios FROM
             Ventas_POS,Servicios_POS,SucursalesCorre WHERE Fk_Caja = '".$_POST['id']."' AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
             AND Ventas_POS.Fk_sucursal=SucursalesCorre.ID_SucursalC  AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' 
              GROUP by Servicios_POS.Servicio_ID";
            $query = $conn->query($sql14);
            $Especialistas14 = null;
            if($query->num_rows>0){
            while ($r=$query->fetch_object()){
              $Especialistas14=$r;
              break;
            }
            
              }

              




              $sql20="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
              Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,SucursalesCorre.ID_SucursalC,SucursalesCorre.
              Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as VentaTotalDeEfectivo
               FROM Ventas_POS,Servicios_POS,SucursalesCorre WHERE Fk_Caja = '".$_POST['id']."' 
               AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=SucursalesCorre.ID_SucursalC AND Ventas_POS.FormaDePago='Efectivo'AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
            $query = $conn->query($sql20);
            $Especialistas20 = null;
            if($query->num_rows>0){
            while ($r=$query->fetch_object()){
              $Especialistas20=$r;
              break;
            }
            
              }


              $sql21="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
              Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,SucursalesCorre.ID_SucursalC,SucursalesCorre.
              Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as VentaTotalTarjeta
               FROM Ventas_POS,Servicios_POS,SucursalesCorre WHERE Fk_Caja = '".$_POST['id']."' 
               AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=SucursalesCorre.ID_SucursalC AND Ventas_POS.FormaDePago='Tarjeta'";
            $query = $conn->query($sql21);
            $Especialistas21 = null;
            if($query->num_rows>0){
            while ($r=$query->fetch_object()){
              $Especialistas21=$r;
              break;
            }
            
              }


              $sql22="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
              Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,SucursalesCorre.ID_SucursalC,SucursalesCorre.
              Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as VentaTotalCreditosGlobales
               FROM Ventas_POS,Servicios_POS,SucursalesCorre WHERE Fk_Caja = '".$_POST['id']."' 
               AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=SucursalesCorre.ID_SucursalC AND Ventas_POS.FormaDePago!='Efectivo' AND Ventas_POS.FormaDePago!='Tarjeta' AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
            $query = $conn->query($sql22);
            $Especialistas22 = null;
            if($query->num_rows>0){
            while ($r=$query->fetch_object()){
              $Especialistas22=$r;
              break;
            }
            
              }

              // Aqui es donde empieza la linea de codigos que generan las tablas que en teoria deberian ser dinamicas, sin embargo por peticion del departamento administrativo quedaron de forma unica y establecida
  
    $sql5="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja,
Ventas_POS.AgregadoEl,SucursalesCorre.ID_SucursalC,SucursalesCorre.Nombre_Sucursal,
Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totaldeservicios FROM
 Ventas_POS,Servicios_POS,SucursalesCorre WHERE Fk_Caja = '".$_POST['id']."' AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
 AND Ventas_POS.Fk_sucursal=SucursalesCorre.ID_SucursalC  AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' 
  GROUP by Servicios_POS.Servicio_ID";
$query = $conn->query($sql5);
// Aqui es donde se genera el codigo para la forma de pago como efectivo
$sql8="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,SucursalesCorre.ID_SucursalC,SucursalesCorre.
Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totalesdepagoEfectivo
 FROM Ventas_POS,Servicios_POS,SucursalesCorre WHERE Fk_Caja = '".$_POST['id']."' 
 AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=SucursalesCorre.ID_SucursalC AND Ventas_POS.FormaDePago='Efectivo'AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
$query8 = $conn->query($sql8);
// Aqui es donde se genera el codigo para la forma de pago como tarjeta
$sql88="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,SucursalesCorre.ID_SucursalC,SucursalesCorre.
Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totalesdepagotarjeta
 FROM Ventas_POS,Servicios_POS,SucursalesCorre WHERE Fk_Caja = '".$_POST['id']."' 
 AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=SucursalesCorre.ID_SucursalC AND Ventas_POS.FormaDePago='Tarjeta'";
$query88 = $conn->query($sql88);
// Aqui es donde se genera el codigo para la forma de pago global de los Creditos 
$sql888="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,SucursalesCorre.ID_SucursalC,SucursalesCorre.
Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totalesdepagoCreditos
 FROM Ventas_POS,Servicios_POS,SucursalesCorre WHERE Fk_Caja = '".$_POST['id']."' 
 AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=SucursalesCorre.ID_SucursalC AND Ventas_POS.FormaDePago!='Efectivo' AND Ventas_POS.FormaDePago!='Tarjeta' AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
$query888 = $conn->query($sql888);

// Aqui es donde se genera el codigo para la forma de pago global de los abonos dentales


$sql8888="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta, Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, 
Ventas_POS.AgregadoEl,Ventas_POS.FormaDePago,SUM(Ventas_POS.Importe) as totalesdepagoCreditosDentales FROM Ventas_POS WHERE Fk_Caja = '".$_POST['id']."' 
 AND Ventas_POS.Nombre_Prod='Abono de cr&eacute;dito'  ";
$query8888 = $conn->query($sql8888);

?>

<?php if($Especialistas!=null):?>

  <?php if($Especialistas!=null):?>

<form method="post" 
    target="print_popup" 
    action="http://localhost:8080/ticket/CierreDeCaja.php"
    onsubmit="window.open('about:blank','print_popup','width=600,height=600');"  id="GeneraTicketCierreCaja">

    <input type="text" class="form-control "   readonly name="VendedorTicket"  readonly value="<?php echo $row['Nombre_Apellidos']?>">
    <input type="number" class="form-control "  step="any" name="TotalCajaTicket" id="resultadototalventasTicket" readonly   aria-describedby="basic-addon1" >
    <input type="text" class="form-control " id="ticketiniciall" name="TicketInicialTicket"  readonly value="<?php echo $Especialistas->Folio_Ticket; ?>">
    <input type="text" class="form-control " id="ticketfinall" name="TicketFinalTicket" readonly value="<?php echo $Especialistas2->Folio_Ticket; ?>" aria-describedby="basic-addon1" maxlength="60">            
    <input type="text" class="form-control "  name="TotalTicketsTickets"readonly value="<?php echo $Especialistas3->Total_tickets; ?>" aria-describedby="basic-addon1" maxlength="60">            
    <input type="number" class="form-control "  id="cantidadtotalventass" name="VentaTotal" step="any" readonly value="<?php echo $Especialistas3->VentaTotal; ?>" aria-describedby="basic-addon1" >
    <input type="number" class="form-control "  id="cantidadtotalventass" name="totalSignosvitales" step="any" readonly value="<?php echo $Especialistas3->Total_Folios; ?>" aria-describedby="basic-addon1" > 

    <input type="text" class="form-control" name="Sucursal" readonly  value="<?php echo $row['Nombre_Sucursal']?>" aria-describedby="basic-addon1" >   
    <input type="number" class="form-control "  step="any" name="Totaldentales"  readonly   value="<?php echo $Especialistas4->totaldentalescreditos; ?>" aria-describedby="basic-addon1" >
    <input type="text" class="form-control "   name="TotalCreditoEnfermeria"  readonly value="<?php echo $Especialistas6->VentaTotalCredito; ?>" aria-describedby="basic-addon1" >  
    <input type="text" class="form-control "   name="TotalCreditolimpieza"  readonly value="<?php echo $Especialistas7->VentaTotalCreditoLimpieza; ?>" aria-describedby="basic-addon1" >  
    <input type="text" class="form-control "   name="TotalCreditoMedicos"  readonly value="<?php echo $Especialistas12->VentaTotalCreditoMedicos; ?>" aria-describedby="basic-addon1" >  
    <input type="text" class="form-control "   name="TotalCreditoFarmaceutico"  readonly value="<?php echo $Especialistas11->VentaTotalCreditoFarmaceutico; ?>" aria-describedby="basic-addon1" >  
    <input type="text" class="form-control "   name="TurnoCorteticket"  readonly value="<?php echo $Especialistas3->Turno; ?>" aria-describedby="basic-addon1" >  

    <input type="text" class="form-control "   name="TotalDeEfectivo"  readonly value="<?php echo $Especialistas20->VentaTotalDeEfectivo; ?>" aria-describedby="basic-addon1" >  
    <input type="text" class="form-control "   name="TotalDeTarjeta"  readonly value="<?php echo $Especialistas21->VentaTotalTarjeta; ?>" aria-describedby="basic-addon1" >  
    <input type="text" class="form-control "   name="TotalDeCreditos"  readonly value="<?php echo $Especialistas22->VentaTotalCreditosGlobales; ?>" aria-describedby="basic-addon1" >  
    
    <button type="submit"  id="EnviaTicket"  class="btn btn-info">Realizar abono <i class="fas fa-money-check-alt"></i></button>
</form>

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
  
  <br>	<div class="table-responsive">
	<table  id="TotalesGeneralesCortes" class="table table-hover">
<thead>


<th>Nombre Servicio</th>

    <th>Total</th>
    
    
    


</thead>
<?php while ($Usuarios=$query->fetch_array()):?>
<tr>



  
    <td> <input type="text" class="form-control "  name="NombreServicio[]"readonly value="<?php echo $Usuarios["Nom_Serv"]; ?>"></td>
    <td><input type="text" class="form-control "  name="TotalServicio[]"readonly value="<?php echo $Usuarios["totaldeservicios"]; ?>"></td>
   
    
		
</tr> 
<?php endwhile;?>
</table>
</div>
</div> 
<?php if($query8->num_rows>0):?>
  <div class="text-center">
	<div class="table-responsive">
	<table  id="TotalesFormaPAgoCortes" class="table table-hover">
<thead>


<th>Forma de pago</th>

    <th>Total</th>
    <th>Forma de pago</th>

    <th>Total</th>
    <th>Forma de pago</th>

<th>Total</th>
    
    


</thead>
<?php while ($Usuarios2=$query8->fetch_array()):?>
  <?php while ($Usuarios3=$query88->fetch_array()):?>
    <?php while ($Usuarios4=$query888->fetch_array()):?>
<tr>
 


 
   
    <td> <input type="text" class="form-control "  name="NombreFormaPago[]"readonly value="<?php echo $Usuarios2["FormaDePago"]; ?>"></td>
    <td><input type="text" class="form-control "  name="TotalFormasPagos[]"readonly value="<?php echo $Usuarios2["totalesdepagoEfectivo"]; ?>"></td>
    <td> <input type="text" class="form-control "  name="NombreFormaPago[]"readonly value="<?php echo $Usuarios3["FormaDePago"]; ?>"></td>
    <td><input type="text" class="form-control "  name="TotalFormasPagos[]"readonly value="<?php echo $Usuarios3["totalesdepagotarjeta"]; ?>"></td>
    <td> <input type="text" class="form-control "  name="NombreFormaPago[]"readonly value="Creditos"></td>
    <td><input type="text" class="form-control "  name="TotalFormasPagos[]"readonly value="<?php echo $Usuarios4["totalesdepagoCreditos"]; ?>"></td>
		
</tr>
<?php endwhile;?><?php endwhile;?><?php endwhile;?>
</table>
</div>
</div>  

<div class="table-responsive">
	<table  id="TotalesGeneralesCortes" class="table table-hover">
<thead>


<th>Nombre Servicio</th>

    <th>Total</th>
    
    
    


</thead>
<?php while ($Usuarios12=$query8888->fetch_array()):?>
<tr>



  
    <td> <input type="text" class="form-control " readonly value="Abono dental "></td>
    <td><input type="text" class="form-control "  readonly value="<?php echo $Usuarios12["totalesdepagoCreditosDentales"]; ?>"></td>
   
    
		
</tr> 
<?php endwhile;?>
</table>
</div>
</div> 
<?php else:?>
	<p class="alert alert-warning">Aún no hay totales registrados </p>
<?php endif;?><?php else:?>
	<p class="alert alert-warning">Aún no hay totales registrados </p>
<?php endif;?>
</div>
    
      







<form action="javascript:void(0)" method="post" id="FinalizaAsignacion" >


<input type="text" class="form-control "  id="" name="IDdeCajaAsignada"  readonly value="<?php echo $Especialistas->Fk_Caja; ?>">

<button type="submit"  id="finasignacion"  class="btn btn-warning">Realizar corte <i class="fas fa-money-check-alt"></i></button>
</form>

<!-- CAMBIA ASIGNACION -->
<form action="javascript:void(0)" method="post" id="CierreDeCaja" >


  <input type="text" class="form-control " hidden id="ticketinicial" name="TicketInicial"  readonly value="<?php echo $Especialistas->Folio_Ticket; ?>">
  
  <input type="text" class="form-control " hidden id="numerocaja" name="NumeroCaja"  readonly value="<?php echo $Especialistas->Fk_Caja; ?>">
    
   
   <input type="text" class="form-control "hidden  id="ticketfinal" name="TicketFinal" readonly value="<?php echo $Especialistas2->Folio_Ticket; ?>" aria-describedby="basic-addon1" maxlength="60">            
   <input type="text" class="form-control " hidden id="fksucursall" name="FkSucursalL" readonly value="<?php echo $Especialistas2->Fk_sucursal; ?>" aria-describedby="basic-addon1" maxlength="60">
   
   <div class="row">
<div class="col">
    <label for="exampleFormControlInput1">Total de tickets<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" id="Tarjeta"><i class="fas fa-file-signature"></i></span>
  </div>
  <input type="text" class="form-control "  name="TotalTickets"readonly value="<?php echo $Especialistas3->Total_tickets; ?>" aria-describedby="basic-addon1" maxlength="60">            
  
</div></div>
<div class="col">
    <label for="exampleFormControlInput1">Total de venta<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" id="Tarjeta"><i class="fas fa-file-signature"></i></span>
  </div>
          
  <?php
$ventaTotalEfectivo = $Especialistas33->VentaTotaldeEfectivoPAraElcorte;



// Redondear el valor al número entero más cercano
$ventaTotalEfectivoRedondeado = round($ventaTotalEfectivo);



?>
<input type="number" class="form-control" id="cantidadtotalventas" name="Cantidad" step="any" readonly value="<?= $ventaTotalEfectivoRedondeado; ?>" aria-describedby="basic-addon1">

</div></div>
<div class="col">
    <label for="exampleFormControlInput1">Conteo de billetes<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" id="Tarjeta"><i class="fas fa-file-signature"></i></span>
  </div>
  <input type="number" class="form-control " onfocus="habilitar();" step="any" name="TotalCaja" id="resultadototalventas" readonly   aria-describedby="basic-addon1" >                
  
</div></div>
<div class="col">
    <label for="exampleFormControlInput1">Turno<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" id="Tarjeta"><i class="fas fa-file-signature"></i></span>
  </div>
          
  <input type="text" class="form-control "  id="turno" name="TurnoCorte"  readonly value="<?php echo $Especialistas3->Turno; ?>" aria-describedby="basic-addon1" >  
</div></div>
</div>
</div>


<div class="" id="Ok" role="alert">
  
</div>
</div>
<div id="contador" class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Billetes</label>
    <div class="table-responsive" style="background-color: #2bbbad !important;color: white;">
  <table class="table table-bordered" style="background-color: #2bbbad !important;color: white;">
  <thead>
    <tr>
       <th scope="col" style="background-color: #2bbbad !important;color: white;">Cantidad</th>
       <th scope="col" style="background-color: #2bbbad !important;color: white;">Valor</th>
       <th scope="col" style="background-color: #2bbbad !important;color: white;">Total</th>
    
    </tr>
  </thead>
  <tbody>
    <tr>
<td><input type="number" class="form-control "  id="billetemil" name="BilleteMil"onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td> <input type="number" class="form-control " hidden id="mil" value="1000" aria-describedby="basic-addon1" >$1000.00 </td>
     <td><input type="number" class="subtotal form-control  "  step="any" id="resultadomil" onchange="multiplicar();"  aria-describedby="basic-addon1" ></td>
     
    </tr>
    <tr>
<td><input type="number" class="form-control "  id="billequinie" name="BilleteQuinie"  onchange="multiplicar();"aria-describedby="basic-addon1" ></td>
     <td> <input type="number" class="form-control " hidden id="quinientos" value="500" aria-describedby="basic-addon1" >$500.00 </td>
     <td><input type="number" class="subtotal form-control  "  step="any" readonly id="resultadoquinientos" onchange="multiplicar();"  aria-describedby="basic-addon1" ></td>
     
    </tr>
    <tr>
<td><input type="number" class="form-control "   id="billedos" name="BilleteDos"onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td><input type="number" class="form-control " hidden id="doscientos" value="200" aria-describedby="basic-addon1" > $200.00 </td>
     <td><input type="number" class="form-control " step="any"  readonly id="resultadodoscioentos"aria-describedby="basic-addon1" ></td>
     
    </tr>
    <tr>
<td><input type="number" class="form-control "   id="billecien" name="BilleteCien"onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td><input type="number" class="form-control " hidden id="cien" value="100" aria-describedby="basic-addon1" > $100.00 </td>
     <td><input type="number" class="form-control "  id="resultadocien" readonly aria-describedby="basic-addon1" ></td>  
    </tr>
    <tr>
<td><input type="number" class="form-control "  id="billecincuenta" name="BilleteCincuenta"onchange="multiplicar();"  aria-describedby="basic-addon1" ></td>
     <td> <input type="number" class="form-control " hidden id="cincuenta" value="50" aria-describedby="basic-addon1"> $50.00 </td>
     <td><input type="number" class="form-control " step="any"  id="resultadocincuenta" readonly aria-describedby="basic-addon1" ></td>  
    </tr>
    <tr>
<td><input type="number" class="form-control "  id="billeveinte" name="BilleteVeinte" onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td><input type="number" class="form-control " hidden id="veinte" value="20" aria-describedby="basic-addon1" > $20.00 </td>
     <td><input type="number" class="form-control "   step="any" id="resultadoveinte" readonly aria-describedby="basic-addon1" ></td>  
    </tr>
    <tr>
    <td><input type="number" class="form-control "   id="monedaCincoc" name="MonedaCincoC" onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td><input type="number" class="form-control " hidden id="cincocc"step="any" value="0.05" aria-describedby="basic-addon1" > $0.5 </td>
     <td><input type="number" class="form-control "  step="any" id="resultadocincocc" aria-describedby="basic-addon1" ></td>  
    </tr>
  </tbody>
</table>
</div>
    </div>
    
   
    <div class="col">
    <label for="exampleFormControlInput1">Monedas<span class="text-danger">*</span></label>
    <div class="table-responsive" style="background-color: #2bbbad !important;color: white;">
  <table class="table table-bordered" style="background-color: #2bbbad !important;color: white;">
  <thead>
    <tr>
       <th scope="col" style="background-color: #2bbbad !important;color: white;">Cantidad</th>
       <th scope="col" style="background-color: #2bbbad !important;color: white;">Valor</th>
       <th scope="col" style="background-color: #2bbbad !important;color: white;">Total</th>
    
    </tr>
  </thead>
  <tbody>
    
    <tr>
<td><input type="number" class="form-control "    id="monedadiez" name="MonedaDiez" onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td> <input type="number" class="form-control " hidden id="diez" value="10" aria-describedby="basic-addon1" >$10.00 </td>
     <td><input type="number" class="form-control "  step="any" id="resultadodiez" aria-describedby="basic-addon1" ></td>  
    </tr>
    <tr>
<td><input type="number" class="form-control "    id="modenacinco" name="MonedaCinco" onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td><input type="number" class="form-control " hidden id="cinco" value="5" aria-describedby="basic-addon1" > $5.00 </td>
     <td><input type="number" class="form-control "  step="any"  id="resultadocinco"aria-describedby="basic-addon1" ></td>  
    </tr>
    <tr>
<td><input type="number" class="form-control "   id="monedados" name="MonedaDos" onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td><input type="number" class="form-control " hidden id="dos" value="2" aria-describedby="basic-addon1" > $2.00 </td>
     <td><input type="number" class="form-control "  step="any" id="resultadodos" aria-describedby="basic-addon1" ></td>  
    </tr>
    <tr>
<td><input type="number" class="form-control "  id="monedapeso" name="MonedaPeso" onchange="multiplicar();"  aria-describedby="basic-addon1" ></td>
     <td><input type="number" class="form-control " hidden id="peso" value="1" aria-describedby="basic-addon1" > $1.00 </td>
     <td><input type="number" class="form-control "  step="any" id="resultadopeso" aria-describedby="basic-addon1" ></td>  
    </tr>
    <tr>
<td><input type="number" class="form-control "  id="monedacincuenta" name="MonedaCincuenta" onchange="multiplicar();"  aria-describedby="basic-addon1" ></td>
     <td> <input type="number" class="form-control " hidden id="cincuentac" step="any" value="0.50" aria-describedby="basic-addon1" >$0.50 </td>
     <td><input type="number" class="form-control " step="any"  id="resultadocincuentac"aria-describedby="basic-addon1" ></td>  
    </tr>
    <td><input type="number" class="form-control "  id="monedaveinte" name="MonedaVeinte" onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td><input type="number" class="form-control " hidden id="veintec"step="any" value="0.20" aria-describedby="basic-addon1" > $0.20 </td>
     <td><input type="number" class="form-control "   step="any" id="resultadoveintec"aria-describedby="basic-addon1" ></td>  
    </tr>
    <td><input type="number" class="form-control "   id="monedadiezc" name="MonedaDiezC" onchange="multiplicar();" aria-describedby="basic-addon1" ></td>
     <td><input type="number" class="form-control " hidden id="diezc"step="any" value="0.10" aria-describedby="basic-addon1" > $0.10 </td>
     <td><input type="number" class="form-control "  step="any" id="resultadodiezc" aria-describedby="basic-addon1" ></td>  
    </tr>
  </tbody>
</table>
</div> 
  </div></div>
   
  
<input type="text" class="form-control " hidden  readonly name="Usuario" id="usuario"  readonly value="<?php echo $row['Nombre_Apellidos']?>">
<input type="text" class="form-control "  hidden  readonly id="sistema" name="Sistema" readonly value="POS <?php echo $row['Nombre_rol']?>">
<input type="text" class="form-control "  hidden  readonly id="empresa" name="Empresa" readonly value="<?php echo $row['ID_H_O_D']?>">


<button type="submit"  id="submit"  class="btn btn-warning">Realizar corte <i class="fas fa-money-check-alt"></i></button>
                          
</form>


<script src="js/ContadorDineroCorteNuevo.js"></script>
<script src="js/FinalizaAsignacionNuevo.js"></script>
<script src="js/GuardaCorteNuevo.js"></script>
<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>

<script type="text/javascript">

    function habilitar()

    {

        var camp1= document.getElementById('cantidadtotalventas');
        var camp2= document.getElementById('resultadototalventas');
        var boton= document.getElementById('submit');

        if (camp1.value != camp2.value) {
         
          document.getElementById("Ok").className = "alert alert-danger";
            document.getElementById("Ok").innerHTML="El valor total del conteo de billetes y monedas no coincide con el total de venta, verifica e intentalo de nuevo";
           
            boton.disabled = true;
        a}else {
            boton.disabled = false;
            document.getElementById("Ok").className = "alert alert-success";
            document.getElementById("Ok").innerHTML="El valor total del conteo de billetes y monedas coincide con la suma del total de billetes y monedas";
            // div = document.getElementById('contador');
            // div.style.display = 'none';
           
        }
    }



</script>
<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>