<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id=null;
$sql1= "SELECT * FROM Servicios_POS WHERE Licencia='".$row['Licencia']."' AND Servicio_ID = ".$_POST["id"];
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
  
  <input type="text" class="form-control " disabled readonly value="<?php echo $Especialistas->Servicio_ID; ?>">
    </div>
    </div>
    
   
    <div class="form-group">
    <label for="exampleFormControlInput1">Nombre del servicio<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="actnomserv" name="ActNomServ" value="<?php echo $Especialistas->Nom_Serv; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div></div>

    
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Estado del servicio</label>
    <div class="input-group mb-3">

  <select name="ActVigenciaServ" class="form-control" id="actualizavigenciaserv">
                 
                    
                   <option  value="<?php echo $Especialistas->Estado; ?>"><?php echo $Especialistas->Estado; ?></option>		
              <option  value="Vigente">Vigente</option>		
              <option  value="Descontinuado">Descontinuado</option>						  						  				  
             </select>
    </div>
    </div>
               
  </div></div>
    </div>
   
    <input type="text" class="form-control " hidden  readonly id="actusuariocserv" name="ActUsuarioCServ" readonly value="<?php echo $row['Nombre_Apellidos']?>">
<input type="text" class="form-control "  hidden  readonly id="actsistemacserv" name="ActSistemaCServ" readonly value="Administrador">
<input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->Servicio_ID; ?>">
<button type="submit"  id="submit"  class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
                          
</form>


<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>
