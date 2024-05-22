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
    
                  <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tipoUserSelect = document.getElementById('tipouser');
            const mensajeDiv = document.getElementById('mensajeDiv');
            const recordatorioDiv = document.getElementById('recordatorioDiv');

            tipoUserSelect.addEventListener('change', function () {
                const selectedValue = this.value;
                mensajeDiv.style.display = 'none';
                recordatorioDiv.style.display = 'none';

                if (selectedValue === 'mensaje') {
                    mensajeDiv.style.display = 'block';
                } else if (selectedValue === 'recordatorio') {
                    recordatorioDiv.style.display = 'block';
                }
            });
        });
    </script>

    <div class="container mt-5">
        <div class="mb-3">
            <label for="tipouser" class="form-label">Tipo</label>
            <select class="form-control" id="tipouser" name="tipouser">
                <option value="">Seleccione una opción</option>
                <option value="mensaje">Mensaje</option>
                <option value="recordatorio">Recordatorio</option>
            </select>
        </div>

        <div id="mensajeDiv" class="mb-3" style="display: none;">
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea class="form-control" id="mensaje" name="mensaje" placeholder="Escriba su mensaje aquí"></textarea>
        </div>

        <div id="recordatorioDiv" class="mb-3" style="display: none;">
            <label for="tipoRecordatorio" class="form-label">Tipo de Recordatorio</label>
            <select class="form-control" id="tipoRecordatorio" name="tipoRecordatorio">
                <option value="">Seleccione un tipo de recordatorio</option>
                <option value="cumpleaños">Cumpleaños</option>
                <option value="reunión">Reunión</option>
                <option value="otro">Otro</option>
            </select>
            <label for="contenidoRecordatorio" class="form-label mt-3">Contenido del Recordatorio</label>
            <textarea class="form-control" id="contenidoRecordatorio" name="contenidoRecordatorio" placeholder="Escriba el contenido del recordatorio aquí"></textarea>
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