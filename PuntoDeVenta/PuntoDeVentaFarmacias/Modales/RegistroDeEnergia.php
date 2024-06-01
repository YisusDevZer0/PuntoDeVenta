
<div class="modal fade" id="RegistroEnergiaVentanaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-="true">
  <div class="modal-dialog  modal-notify modal-success">
    <div class="modal-content">
      <div class="modal-header" style=" background-color: #ef7980 !important;">
        <h5 class="modal-title" style="color:white;" id="exampleModalLabel">Agregar Nuevo Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
	        <div class="modal-body">
          <div class="text-center">
          <form enctype="multipart/form-data" id="RegistroDiarioEnergiaWats">
<div class="form-group">
    <label for="exampleFormControlInput1">Ingrese el valor de kilowatts </label>
     <div class="input-group mb-3">
  
  <input type="number" class="form-control"   name="RegistroEnergia" id="registroenergia" aria-describedby="basic-addon1" required>
  <?php
        // Obtener la fecha actual en formato 'YYYY-MM-DD'
        $currentDate = date('Y-m-d');
    ?>
  <input type="date"  hidden name="Fecha" value="<?php echo $currentDate?>" >
</div>
<div>
</div>
    </div>
    <div class="form-group">
    <label for="exampleFormControlInput1">Comentario</label>
     <div class="input-group mb-3">
  
  <textarea class="form-control" id="Comentario" name="Comentario" rows="3"></textarea>
</div>

    </div>
    <div class="form-group">
    <label for="exampleFormControlInput1">Fotografia</label>
     <div class="input-group mb-3">
 
  <input type="file" required class="form-control " name="file" id="file" aria-describedby="basic-addon1" >          
</div>
<div>

</div>
    </div>
    
    </div>
   
    
 
  
<!-- INICIA DATA DE AGENDA -->


   
    
<input type="text" class="form-control" name="Registro" id="registro" hidden value="<?php echo $row['Nombre_Apellidos']?>"  readonly >
  
  <input type="text" class="form-control" name="Sucursal" id="sucursal" hidden  value="<?php echo $row['Nombre_Sucursal']?>"   readonly >
  <input type="text" class="form-control" name="Empresa" id="Empresa"  hidden value="<?php echo $row['Licencia']?>"   readonly >

     
    
    <div class="text-center">
<button type="submit"  name="submit_AgeExt" id="submit_AgeExt"  class="btn btn-success">Confirmar datos <i class="fas fa-user-check"></i></button>
    </div>    </div></div>
<!-- FINALIZA DATA DE AGENDA -->
                  
</form>


</div></>




        
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->