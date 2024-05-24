
<div class="modal fade" id="RegistroEnergiaVentanaModal" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="editModalLabel" aria-hidden="true">
  <div id="Di"class="modal-dialog  modal-notify modal-success">
      <div class="modal-content">
      <div class="modal-header">
         <p class="heading lead" id="Titulo">Control de energia</p>

         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true" class="white-text">&times;</span>
         </button>
       </div>
        <div id="Mensaje "class="alert alert-info alert-styled-left text-blue-800 content-group">
						                <span id="Aviso" class="text-semibold">Estimado usuario, 
                            Verifique los campos antes de realizar alguna accion</span>
						                <button type="button" class="close" data-dismiss="alert">×</button>
                            </div>
	        <div class="modal-body">
          <div class="text-center">
          <form enctype="multipart/form-data" id="RegistroDiarioEnergiaWats">
<div class="form-group">
    <label for="exampleFormControlInput1">Ingrese el valor de kilowatts </label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  <span class="input-group-text" id="Tarjeta"><i class="fas fa-lightbulb"></i></span>
  </div>
  <input type="number" class="form-control"   name="RegistroEnergia" id="registroenergia" aria-describedby="basic-addon1" required>
  <input type="text"  hidden name="Fecha" value="<?php echo date('Y-m-d'); ?> ">
</div>
<div>
</div>
    </div>
    <div class="form-group">
    <label for="exampleFormControlInput1">Comentario</label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  <span class="input-group-text" id="Tarjeta"><i class="fas fa-comment"></i></span>
  </div>
  <textarea class="form-control" id="Comentario" name="Comentario" rows="3"></textarea>
</div>

    </div>
    <div class="form-group">
    <label for="exampleFormControlInput1">Fotografia</label>
     <div class="input-group mb-3">
  <div class="input-group-prepend">
  <span class="input-group-text" id="Tarjeta"><i class="fas fa-comment"></i></span>
  </div>
  <input type="file" required class="form-control " name="file" id="file" aria-describedby="basic-addon1" >          
</div>
<div>

</div>
    </div>
    
    </div>
   
    
 
  
<!-- INICIA DATA DE AGENDA -->


   
    
    
     
    <input type="text" class="form-control" name="Registro" id="registro" hidden value="<?php echo $row['Nombre_Apellidos']?>"  readonly >
  
    <input type="text" class="form-control" name="Sucursal" id="sucursal"  hidden value="<?php echo $row['Nombre_Sucursal']?>"   readonly >
    <input type="text" class="form-control" name="Empresa" id="Empresa" hidden value="Doctor Consulta"   readonly >
    <div class="text-center">
<button type="submit"  name="submit_AgeExt" id="submit_AgeExt"  class="btn btn-success">Confirmar datos <i class="fas fa-user-check"></i></button>
    </div>    </div></div>
<!-- FINALIZA DATA DE AGENDA -->
                  
</form>


</div></div>




        
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->