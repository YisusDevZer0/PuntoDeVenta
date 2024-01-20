<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario con el estilo proporcionado -->
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              <h6 class="mb-4">Formulario de Usuario</h6>
              <form>
                <div class="mb-3">
                  <label for="tipoUsuario" class="form-label">Tipo de Usuario</label>
                  <input type="text" class="form-control" id="tipoUsuario" placeholder="Ingrese el tipo de usuario">
                </div>
                <div class="mb-3">
                  <label for="licencia" class="form-label">Licencia</label>
                  <input type="text" class="form-control" id="licencia" placeholder="Ingrese la licencia" style="display:none;">
                </div>
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