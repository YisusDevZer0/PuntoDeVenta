<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #28a745 !important;">
        <h5 class="modal-title" style="color:white;" id="modalNuevoClienteLabel"><i class="fa-solid fa-user-plus"></i> Nuevo Cliente</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              
              <form id="formNuevoCliente">
                
                <div class="mb-3">
                  <label for="cliente_nombre" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="nombre" id="cliente_nombre" placeholder="Nombre del cliente" required>
                </div>

                <div class="mb-3">
                  <label for="cliente_telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                  <input type="tel" class="form-control" name="telefono" id="cliente_telefono" placeholder="10 dígitos" maxlength="15" required>
                </div>
                
                <div class="mb-3">
                  <label for="cliente_fecha_nac" class="form-label">Fecha de nacimiento</label>
                  <input type="date" class="form-control" name="fecha_nacimiento" id="cliente_fecha_nac">
                </div>

                <input type="hidden" name="sucursal" value="<?php echo $row['Fk_Sucursal']; ?>">
                <input type="hidden" name="sucursal_nombre" value="<?php echo $row['Nombre_Sucursal']; ?>">
                <input type="hidden" name="licencia" value="<?php echo $row['Licencia']; ?>">
                <input type="hidden" name="ingreso" value="<?php echo $row['Nombre_Apellidos']; ?>">

                <button type="submit" class="btn btn-success"><i class="fa-solid fa-save"></i> Registrar cliente</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
