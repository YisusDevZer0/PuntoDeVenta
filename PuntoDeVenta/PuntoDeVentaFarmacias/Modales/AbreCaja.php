<?php
date_default_timezone_set("America/Monterrey");
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

$fcha = date("Y-m-d");
$user_id=null;
$sql1= "SELECT Fondos_Cajas.ID_Fon_Caja,Fondos_Cajas.Fk_Sucursal,Fondos_Cajas.Fondo_Caja,Fondos_Cajas.Licencia, 
Fondos_Cajas.Estatus, Sucursales.ID_Sucursal,Sucursales.Nombre_Sucursal FROM Fondos_Cajas,Sucursales 
where Fondos_Cajas.Fk_Sucursal = Sucursales.ID_Sucursal AND  Fondos_Cajas.ID_Fon_Caja = ".$_POST["id"];
$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
  $hora = date('G');
?>

<?php if($Especialistas!=null):?>

<form action="javascript:void(0)" method="post" id="OpenCaja" >
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Cantidad asignada en fondo de caja </label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta"><i class="fas fa-receipt"></i></span>
  </div>
  <input type="text" class="form-control " hidden name="FkFondo" id="fkfondo" readonly value="<?php echo $Especialistas->ID_Fon_Caja; ?>">
  <input type="number" class="form-control "  id="cantidad" name="Cantidad" step="any" readonly value="<?php echo $Especialistas->Fondo_Caja; ?>" aria-describedby="basic-addon1" >  
    </div>
    </div>

    <?php else:?>
  <p class="alert alert-danger"><i class="fas fa-exclamation-triangle fa-2x" style="color: #f50909;"></i> No encontramos algún fondo de caja asignado, por favor verifica e intenta de nuevo <i class="fas fa-exclamation-triangle fa-2x" style="color: #f50909;"></i></p>
<?php endif;?>