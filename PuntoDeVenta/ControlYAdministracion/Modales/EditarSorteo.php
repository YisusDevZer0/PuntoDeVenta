<!-- Modal Editar Sorteo -->
<div class="modal fade" id="modalEditarSorteo" tabindex="-1" aria-labelledby="modalEditarSorteoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #ef7980 !important;">
        <h5 class="modal-title" style="color:white;" id="modalEditarSorteoLabel"><i class="fa-solid fa-edit"></i> Editar Sorteo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              
              <form id="formEditarSorteo">
                <input type="hidden" name="action" value="actualizar">
                <input type="hidden" name="id" id="edit_sorteo_id">
                <input type="hidden" name="actualizado_por" value="<?php echo $row['Nombre_Apellidos']; ?>">
                
                <div class="mb-3">
                  <label for="edit_sorteo_nombre" class="form-label">Nombre del sorteo <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="nombre" id="edit_sorteo_nombre" required>
                </div>

                <div class="mb-3">
                  <label for="edit_sorteo_descripcion" class="form-label">Descripción</label>
                  <textarea class="form-control" name="descripcion" id="edit_sorteo_descripcion" rows="2"></textarea>
                </div>
                
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="edit_sorteo_fecha_inicio" class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="fecha_inicio" id="edit_sorteo_fecha_inicio" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="edit_sorteo_fecha_fin" class="form-label">Fecha de fin <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="fecha_fin" id="edit_sorteo_fecha_fin" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="edit_sorteo_prefijo" class="form-label">Prefijo del folio</label>
                    <input type="text" class="form-control" name="prefijo_folio" id="edit_sorteo_prefijo" maxlength="10">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="edit_sorteo_folio_inicio" class="form-label">Folio inicial</label>
                    <input type="number" class="form-control" name="folio_inicio" id="edit_sorteo_folio_inicio" value="1" min="1">
                  </div>
                </div>

                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="edit_sorteo_aplica_todas" name="aplica_todas_check" checked>
                    <label class="form-check-label" for="edit_sorteo_aplica_todas">
                      Aplica a <strong>todas</strong> las sucursales
                    </label>
                  </div>
                </div>

                <div id="edit_divSucursales" style="display:none;">
                  <div class="mb-3">
                    <label class="form-label">Seleccionar sucursales:</label>
                    <div class="border rounded p-3" style="max-height:200px; overflow-y:auto;">
                      <?php
                      $sqlSucEdit = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";
                      $resSucEdit = mysqli_query($conn, $sqlSucEdit);
                      if ($resSucEdit) {
                          while ($sucE = mysqli_fetch_assoc($resSucEdit)) {
                              echo '<div class="form-check">';
                              echo '<input class="form-check-input edit-sucursal-cb" type="checkbox" value="' . $sucE['ID_Sucursal'] . '" id="edit_suc_' . $sucE['ID_Sucursal'] . '">';
                              echo '<label class="form-check-label" for="edit_suc_' . $sucE['ID_Sucursal'] . '">' . $sucE['Nombre_Sucursal'] . '</label>';
                              echo '</div>';
                          }
                      }
                      ?>
                    </div>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar cambios</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
