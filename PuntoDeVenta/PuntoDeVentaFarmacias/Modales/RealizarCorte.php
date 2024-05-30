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
// CONSULTA OK
            $sql14="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja,
            Ventas_POS.AgregadoEl,Sucursales.ID_Sucursal,Sucursales.Nombre_Sucursal,
            Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totaldeservicios FROM
             Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja ='".$_POST['id']."' AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
             AND Ventas_POS.Fk_sucursal=Sucursales.ID_Sucursal  AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' 
              GROUP by Servicios_POS.Servicio_ID";
            $query = $conn->query($sql14);
            $Especialistas14 = null;
            if($query->num_rows>0){
            while ($r=$query->fetch_object()){
              $Especialistas14=$r;
              break;
            }
            
              }

              


// CONSULTA OK

              $sql20="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
              Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,Sucursales.ID_Sucursal,Sucursales.
              Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as VentaTotalDeEfectivo
               FROM Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' 
               AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=Sucursales.ID_Sucursal AND Ventas_POS.FormaDePago='Efectivo'AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
            $query = $conn->query($sql20);
            $Especialistas20 = null;
            if($query->num_rows>0){
            while ($r=$query->fetch_object()){
              $Especialistas20=$r;
              break;
            }
            
              }


              $sql21="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
              Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,Sucursales.ID_Sucursal,Sucursales.
              Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as VentaTotalTarjeta
               FROM Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' 
               AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=Sucursales.ID_Sucursal AND Ventas_POS.FormaDePago='Tarjeta'";
            $query = $conn->query($sql21);
            $Especialistas21 = null;
            if($query->num_rows>0){
            while ($r=$query->fetch_object()){
              $Especialistas21=$r;
              break;
            }
            
              }


              $sql22="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
              Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,Sucursales.ID_Sucursal,Sucursales.
              Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as VentaTotalCreditosGlobales
               FROM Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' 
               AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=Sucursales.ID_Sucursal AND Ventas_POS.FormaDePago!='Efectivo' AND Ventas_POS.FormaDePago!='Tarjeta' AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
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
Ventas_POS.AgregadoEl,Sucursales.ID_SucursalC,Sucursales.Nombre_Sucursal,
Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totaldeservicios FROM
 Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
 AND Ventas_POS.Fk_sucursal=Sucursales.ID_SucursalC  AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' 
  GROUP by Servicios_POS.Servicio_ID";
$query = $conn->query($sql5);
// Aqui es donde se genera el codigo para la forma de pago como efectivo
$sql8="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,Sucursales.ID_SucursalC,Sucursales.
Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totalesdepagoEfectivo
 FROM Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' 
 AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=Sucursales.ID_SucursalC AND Ventas_POS.FormaDePago='Efectivo'AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
$query8 = $conn->query($sql8);
// Aqui es donde se genera el codigo para la forma de pago como tarjeta
$sql88="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,Sucursales.ID_SucursalC,Sucursales.
Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totalesdepagotarjeta
 FROM Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' 
 AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=Sucursales.ID_SucursalC AND Ventas_POS.FormaDePago='Tarjeta'";
$query88 = $conn->query($sql88);
// Aqui es donde se genera el codigo para la forma de pago global de los Creditos 
$sql888="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta,
Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, Ventas_POS.AgregadoEl,Sucursales.ID_SucursalC,Sucursales.
Nombre_Sucursal,Ventas_POS.FormaDePago, Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv,SUM(Ventas_POS.Importe) as totalesdepagoCreditos
 FROM Ventas_POS,Servicios_POS,Sucursales WHERE Fk_Caja = '".$_POST['id']."' 
 AND Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID AND Ventas_POS.Fk_sucursal=Sucursales.ID_SucursalC AND Ventas_POS.FormaDePago!='Efectivo' AND Ventas_POS.FormaDePago!='Tarjeta' AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' ";
$query888 = $conn->query($sql888);

// Aqui es donde se genera el codigo para la forma de pago global de los abonos dentales


$sql8888="SELECT Ventas_POS.Identificador_tipo,Ventas_POS.Fk_sucursal,Ventas_POS.ID_H_O_D,Ventas_POS.Fecha_venta, Ventas_POS.AgregadoPor,Ventas_POS.Fk_Caja, 
Ventas_POS.AgregadoEl,Ventas_POS.FormaDePago,SUM(Ventas_POS.Importe) as totalesdepagoCreditosDentales FROM Ventas_POS WHERE Fk_Caja = '".$_POST['id']."' 
 AND Ventas_POS.Nombre_Prod='Abono de cr&eacute;dito'  ";
$query8888 = $conn->query($sql8888);

?>

<?php if($Especialistas!=null):?>

  
<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>