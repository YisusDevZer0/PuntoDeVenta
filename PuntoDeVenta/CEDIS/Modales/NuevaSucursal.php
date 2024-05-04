<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Nueva sucursal</h5>
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
                  <label for="tipouser" class="form-label">Nombre de sucursal</label>
                  <input type="text" class="form-control" name="NombreSucursal" id="tipoUsuario" placeholder="Ingrese el tipo de usuario">
                </div>
                <div class="mb-3">
                  <label for="tipouser" class="form-label">Direccion</label>
                  <input type="text" class="form-control" name="Direccion" id="direccion" placeholder="Ingrese la direccion">
                </div>
                <div class="mb-3">
                  <label for="tipouser" class="form-label">Telefono</label>
                  <input type="text" class="form-control" name="Telefono" id="telefono" placeholder="Ingrese el numero de telefono">
                </div>
                <div class="mb-3">
                  <label for="tipouser" class="form-label">Pin de equipo</label>
                  <input type="text" class="form-control" name="PinEquipo" id="pinequipo" placeholder="Ingrese el pin del equipo">
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