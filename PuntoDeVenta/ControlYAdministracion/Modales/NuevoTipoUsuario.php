<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header" style=" background-color: #ef7980 !important;">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Nuevo Tipo De Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario con el estilo proporcionado -->
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              
              <form id="NewTypeUser">
                  <!-- Agrega un campo oculto para el token CSRF -->
    
                <div class="mb-3">
                  <label for="tipouser" class="form-label">Tipo de Usuario</label>
                  <input type="text" class="form-control" name="tipoUsuario" id="tipoUsuario" placeholder="Ingrese el tipo de usuario">
                </div>
                
                <input type="text" hidden class="form-control" name="licencia" id ="licencia" value="<?php echo $row['Licencia']?>">
                <input type="text" hidden class="form-control" name="agrego"id="agrego" value="<?php echo $row['Nombre_Apellidos']?>" >
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