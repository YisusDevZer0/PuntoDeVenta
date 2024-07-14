<!-- Modal -->
<div class="modal fade" id="ModalRecordatoriosMensajes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header" style=" background-color: #ef7980 !important;">
        <h5 class="modal-title" style="color:white;">Dejar un nuevo mensaje o recordatorio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario con el estilo proporcionado -->
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              
              <form id="NewTypeUser">
                  <!-- Agrega un campo oculto para el token CSRF -->
    
         

       

        <div id="mensajeDiv" class="mb-3" >
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea class="form-control" id="mensaje" name="mensaje" placeholder="Escriba su mensaje aquí"></textarea>
        </div>

      
        
    </div>
                
                <input type="text" hidden class="form-control" name="licencia" id ="licencia" value="<?php echo $row['Licencia']?>">
                <input type="text" hidden class="form-control" name="estado" id ="estado" value="Vigente">
                <input type="text" hidden class="form-control" name="agregoPor"id="agrego" value="<?php echo $row['Nombre_Apellidos']?>" >
                <input type="text" hidden class="form-control" name="Sistema"id="sistema" value="<?php echo $row['Tipo_Usuario']?>" >
                               <!-- Agrega los otros campos del formulario de manera similar -->
                <!-- ... -->

                <button type="submit" class="btn btn-primary">Guardar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>