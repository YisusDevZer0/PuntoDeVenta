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
    
<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
