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
<style>
  .input-negro {
    color: black;
}

</style>
<form action="javascript:void(0)" method="post" id="OpenCaja" >
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Cantidad asignada en fondo de caja </label>
    <div class="input-group mb-3">
 
  <input type="text" class="form-control " hidden name="FkFondo" id="fkfondo" readonly value="<?php echo $Especialistas->ID_Fon_Caja; ?>">
  <input type="number" class="form-control input-negro "  id="cantidad" name="Cantidad" step="any" readonly value="<?php echo $Especialistas->Fondo_Caja; ?>" aria-describedby="basic-addon1" >  
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Empleado<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
 
  <input type="text" class="form-control input-negro" readonly  name="Empleado" id="empleado" value="<?php echo $row['Nombre_Apellidos']?>" aria-describedby="basic-addon1" >            
</div></div></div>

<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Sucursal </label>
    <div class="input-group mb-3">
 
  <input type="text" class="form-control input-negro" readonly  value="<?php echo $Especialistas->Nombre_Sucursal; ?>" aria-describedby="basic-addon1" >       
  <input type="text" class="form-control " readonly name="Sucursal" id="sucursal" hidden value="<?php echo $Especialistas->Fk_Sucursal; ?>" aria-describedby="basic-addon1" >       
    </div>
    </div>
    
   
    <div class="col">
    <label for="exampleFormControlInput1">Fecha<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
 
  <input type="text" class="form-control input-negro" readonly name="Fecha" id="fecha" value="<?php echo $fcha; ?>" aria-describedby="basic-addon1" >   
  <input type="text" class="form-control " hidden readonly name="Asignacion" id="asignacion" value="1" aria-describedby="basic-addon1" >            
</div></div></div>

<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Turno </label>
    <div class="input-group mb-3">
 
  <select name="Turno" id="turno"  onchange="TurnoElegido();"class="form-control">
  <option value="">Escoge un turno</option>
 
  <option value="Matutino">Matutino</option>
  <option value="Vespertino">Vespertino</option>
  <option value="Nocturno">Nocturno</option>
  </select>      
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Cantidad total en caja<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
 
  <input type="number" class="form-control input-negro " step="any" name="TotalCaja" id="resultado"  aria-describedby="basic-addon1" >     
  </div>
</div><label for="resultado" class="error"></div>
<div class="" id="Ok" role="alert">
  
</div>
<input type="text" hidden name="Empresa" value="<?php echo $row['ID_H_O_D']?>">
 
 <input type="text" hidden name="Estatus" value="Abierta">
 <input type="text" hidden name="CodEstatus" value="background-color: #2BBB1D !important;">
 <input type="text"  hidden name="Sistema" value="POS <?php echo $row['Nombre_rol']?>">
<button type="submit"  id="submit"  class="btn btn-info">Abrir caja <i class="fas fa-check"></i></button>

</form>

<form method="post"  id="GeneraTicketAperturaCaja">

   
      <input type="text" class="form-control "   readonly name="VendedorTicket"  readonly value="<?php echo $row['Nombre_Apellidos']?>">
      <input type="text" class="form-control "   readonly name="TurnoTicket" id="turnoticket"  >
      <input type="text" readonly name="EstadoColor" value="#157347">
      <input type="number" class="form-control "   name="FondoBase" step="any" readonly value="<?php echo $Especialistas->Fondo_Caja; ?>" aria-describedby="basic-addon1" >  
      <input type="number" class="form-control "  step="any" name="TotalCajaDeApertura" id="resultadoticket" readonly   aria-describedby="basic-addon1" >    
     
      <input type="datetime" name="Horadeimpresion" value="<?php echo date('h:i:s A');?>">
      <input type="text" class="form-control" name="SucursalApertura" readonly  value="<?php echo $Especialistas->Nombre_Sucursal; ?>" aria-describedby="basic-addon1" >     
      <button type="submit"  id="EnviaTicket"  class="btn btn-info">Realizar abono <i class="fas fa-money-check-alt"></i></button>
</form>

<script src="js/AbreCaja.js"></script>

    <?php else:?>
  <p class="alert alert-danger"><i class="fas fa-exclamation-triangle fa-2x" style="color: #f50909;"></i> No encontramos algún fondo de caja asignado, por favor verifica e intenta de nuevo <i class="fas fa-exclamation-triangle fa-2x" style="color: #f50909;"></i></p>
<?php endif;?>