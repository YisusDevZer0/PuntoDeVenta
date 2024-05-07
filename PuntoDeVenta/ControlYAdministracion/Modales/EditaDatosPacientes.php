<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id=null;
$sql1= "SELECT dp.ID_Data_Paciente, 
dp.Nombre_Paciente, 
dp.Edad, 
dp.Sexo, 
dp.Telefono, 
dp.Fecha_Nacimiento, 
dp.Fk_Sucursal, 
dp.SucursalVisita,
s.ID_Sucursal, 
s.Nombre_Sucursal
FROM Data_Pacientes dp
INNER JOIN Sucursales s ON dp.Fk_Sucursal = s.ID_Sucursal AND
dp.ID_Data_Paciente= ".$_POST["id"];
$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
?>

<?php if($Especialistas!=null):?>

<form action="javascript:void(0)" method="post" id="ActualizaServicios" >
<div class="form-group">
    <label for="exampleFormControlInput1">Folio</label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control " disabled readonly value="<?php echo $Especialistas->ID_Data_Paciente; ?>">
    </div>
    </div>
    
   
    <div class="form-group">
    <label for="exampleFormControlInput1">Nombre del servicio<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="actnomserv" name="ActNomServ" value="<?php echo $Especialistas->Nombre_Paciente; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>
<div class="form-group">
    <label for="exampleFormControlInput1">Fecha de nacimiento <span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="date" class="form-control "  id="actfechanac" name="ActFechaNac" value="<?php echo $Especialistas->Fecha_Nacimiento; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>

<div class="form-group">
    <label for="exampleFormControlInput1">Telefono <span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="date" class="form-control "  id="acttelefono" name="ActTelefono" value="<?php echo $Especialistas->Fecha_Nacimiento; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>
</div>

    
</div>
    </div>
   
    <input type="text" class="form-control " hidden  readonly id="actusuariocserv" name="ActUsuarioCServ" readonly value="<?php echo $row['Nombre_Apellidos']?>">
<input type="text" class="form-control "  hidden  readonly id="actsistemacserv" name="ActSistemaCServ" readonly value="Administrador">
<input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->ID_Data_Paciente; ?>">
<button type="submit"  id="submit"  class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
                          
</form>
<script src="js/ActualizacionDeMarcas.js"></script>

<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>

