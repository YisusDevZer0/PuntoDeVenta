<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id=null;
$sql1= "SELECT * FROM Proveedores WHERE Licencia='".$row['Licencia']."' AND ID_Proveedor = ".$_POST["id"];
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
  
  <input type="text" class="form-control " disabled readonly value="<?php echo $Especialistas->Presentacion_ID; ?>">
    </div>
    </div>
    
   
    <div class="form-group">
    <label for="exampleFormControlInput1">Nombre del proveedor<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="actnomserv" name="ActNomServ" value="<?php echo $Especialistas->Nom_Presentacion; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>
<div class="form-group">
    <label for="exampleFormControlInput1">Telefono<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="telefonoserv" name="ActNomServ" value="<?php echo $Especialistas->Numero_Contacto; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>
<div class="form-group">
    <label for="exampleFormControlInput1">Correo<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="correoserv" name="ActNomServ" value="<?php echo $Especialistas->Correo_Electronico; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>
<div class="form-group">
    <label for="exampleFormControlInput1">Clave o identificador<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="clavserv" name="ActNomServ" value="<?php echo $Especialistas->Clave_Proveedor; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>
</div>

    
</div>
    </div>
   
    <input type="text" class="form-control " hidden  readonly id="actusuariocserv" name="ActUsuarioCServ" readonly value="<?php echo $row['Nombre_Apellidos']?>">
<input type="text" class="form-control "  hidden  readonly id="actsistemacserv" name="ActSistemaCServ" readonly value="Administrador">
<input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->ID_Proveedor; ?>">
<button type="submit"  id="submit"  class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
                          
</form>
<script src="js/ActualizaProveedores.js"></script>

<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>
