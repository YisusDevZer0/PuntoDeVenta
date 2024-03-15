<?php
include "../Controlador/db_connect.php.php";
include "../Controlador/ControladorUsuario.php";

$user_id=null;
$sql1= "SELECT * FROM Servicios_POS WHERE ID_H_O_D ='".$row['ID_H_O_D']."' AND Servicio_ID = ".$_POST["id"];
$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
?>

<? if($Especialistas!=null):?>

<form action="javascript:void(0)" method="post" id="ActualizaServicios" >
<div class="form-group">
    <label for="exampleFormControlInput1">Folio</label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta"><i class="fas fa-receipt"></i></span>
  </div>
  <input type="text" class="form-control " disabled readonly value="<? echo $Especialistas->Servicio_ID; ?>">
    </div>
    </div>
    
   
    <div class="form-group">
    <label for="exampleFormControlInput1">Nombre de categoría<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  
    <span class="input-group-text" id="Tarjeta"><i class="fas fa-file-signature"></i></span>
  </div>
  <input type="text" class="form-control "  id="actnomserv" name="ActNomServ" value="<? echo $Especialistas->Nom_Serv; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div></div>

    
<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Vigencia categoría</label>
    <div class="input-group mb-3">
  <div class="input-group-prepend">  <span class="input-group-text" id="Tarjeta"> <i class="fas fa-info-circle"></i></span>
  </div>
  <select name="ActVigenciaServ" class="form-control" id="actualizavigenciaserv" onchange="ActualizaTipoVigenciaServicio();">
                 
                    
                   <option  value="<? echo $Especialistas->Cod_Estado; ?>"><? echo $Especialistas->Estado; ?></option>		
              <option  value="background-color:#2BBB1D!important;">Vigente</option>		
              <option  value="background-color:#828681!important;">Descontinuado</option>						  						  				  
             </select>
    </div>
    </div>
    <div class="col">
    
<div class="table-responsive">
  <table class="table table-bordered">
  <thead>
    <tr>
       <th scope="col" style="background-color: #4285f4 !important;">Estatus fondo</th>
    
    </tr>
  </thead>
  <tbody>
    <tr>
<td>
<button id="VigenciaBDServ" class="btn btn-default btn-sm" style=<? echo $Especialistas->Cod_Estado; ?>><? echo $Especialistas->Estado; ?></button> 
     <button id="SiVigenteServAct" class="divOcultoServAct btn btn-default btn-sm" style="background-color:#2BBB1D!important;">Vigente</button> 
      <button id="NoVigenteServAct" class="divOcultoServAct btn btn-default btn-sm" style="background-color:#828681!important;">Descontinuado</button>
      <button id="QuizasproximoServAct" class="divOcultoServAct btn btn-default btn-sm" style="background-color:#ff6c0c!important;">Próximamente</button></td>
    </tr>
    
  
  </tbody>
</table>
</div>           
  </div></div>
    </div>
    <input type="text"  class="form-control " hidden readonly name="ActVigEstServ" id="vigenciaservactserv">
    <input type="text" class="form-control " hidden  readonly id="actusuariocserv" name="ActUsuarioCServ" readonly value="<?echo $row['Nombre_Apellidos']?>">
<input type="text" class="form-control "  hidden  readonly id="actsistemacserv" name="ActSistemaCServ" readonly value="POS <?echo $row['Nombre_rol']?>">
<input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->Servicio_ID; ?>">
<button type="submit"  id="submit"  class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
                          
</form>
<script src="js/ActualizaServicio.js"></script>

<? else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<? endif;?>
<script type="text/javascript">
  

  function ActualizaTipoVigenciaServicio() {


/* Para obtener el texto */
var combo = document.getElementById("actualizavigenciaserv");
var selected = combo.options[combo.selectedIndex].text;
$("#vigenciaservactserv").val(selected);
}


$(function() {
  
    
$("#actualizavigenciaserv").on('change', function() {

  var selectValue = $(this).val();
  switch (selectValue) {

    case "background-color:#2BBB1D!important;":
        $("#SiVigenteServAct").show();
                        
                        $("#NoVigenteServAct").hide();
                        $("#QuizasproximoServAct").hide();   
                        $("#VigenciaBDServ").hide(); 
     
      break;

    case "background-color:#828681!important;":
        $("#NoVigenteServAct").show();

        $("#SiVigenteServAct").hide();
        $("#QuizasproximoServAct").hide();    
        $("#VigenciaBDServ").hide();
      
      break;
      case "background-color:#ff6c0c!important;":
        $("#QuizasproximoServAct").show();    
        $("#NoVigenteServAct").hide();
        $("#SiVigenteServAct").hide();
        $("#VigenciaBDServ").hide();
     
      
      break;
      case "":
        $("#NoVigenteServAct").hide();
        $("#QuizasproximoServAct").hide();    
        $("#SiVigenteServAct").hide();
        $("#VigenciaBDServ").hide();
        
     
      
      break;
      case "<? echo $Especialistas->Cod_Estado; ?>":
  
        $("#VigenciaBDServ").show();
        $("#NoVigenteServAct").hide();
        $("#QuizasproximoServAct").hide();    
        $("#SiVigenteServAct").hide();
       
     
      
      break;

    

  }
 
}).change();

});

</script>

<style>
    			.divOcultoServAct {
			display: none;
		}
</style>