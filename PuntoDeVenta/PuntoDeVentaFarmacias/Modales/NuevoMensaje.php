<!-- Modal -->
<div class="modal fade" id="ModalRecordatoriosMensajes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #ef7980 !important;">
        <h5 class="modal-title" style="color:white;">Dejar un nuevo mensaje</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario con el estilo proporcionado -->
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              <form id="NuevoMensajeSucursales" method="POST" action="tu_archivo_php.php">
                <!-- Agrega un campo oculto para el token CSRF -->
                
                <div class="mb-3">
                  <label for="Encabezado" class="form-label">Encabezado</label>
                  <input type="text" class="form-control" id="Encabezado" name="Encabezado" placeholder="Encabezado del mensaje">
                </div>

                <div class="mb-3">
                  <label for="TipoMensaje" class="form-label">Tipo de Mensaje</label>
                  <input type="text" class="form-control" id="TipoMensaje" name="TipoMensaje" placeholder="Tipo de mensaje">
                </div>

                <div class="mb-3" id="mensajeDiv">
                  <label for="Mensaje_Recordatorio" class="form-label">Mensaje</label>
                  <textarea class="form-control" id="Mensaje_Recordatorio" name="Mensaje_Recordatorio" placeholder="Escriba su mensaje aquí"></textarea>
                </div>

                <input type="hidden" class="form-control" name="Registrado" id="Registrado" value="<?php echo $row['Nombre_Apellidos']?>">
                <input type="hidden" class="form-control" name="Sucursal" id="Sucursal" value="<?php echo $row['Fk_Sucursal']?>">
                <input type="hidden" class="form-control" name="Estado" id="Estado" value="Vigente">
                <input type="hidden" class="form-control" name="Sistema" id="Sistema" value="<?php echo $row['Tipo_Usuario']?>">
                <input type="hidden" class="form-control" name="Licencia" id="Licencia" value="<?php echo $row['Licencia']?>">

                <button type="submit" class="btn btn-primary">Guardar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
