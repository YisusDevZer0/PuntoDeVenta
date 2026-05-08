<!-- Modal Nuevo Sorteo -->
<div class="modal fade" id="modalNuevoSorteo" tabindex="-1" aria-labelledby="modalNuevoSorteoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #ef7980 !important;">
        <h5 class="modal-title" style="color:white;" id="modalNuevoSorteoLabel"><i class="fa-solid fa-gift"></i> Nuevo Sorteo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div class="bg-light rounded p-4">
              
              <form id="formNuevoSorteo">
                <input type="hidden" name="action" value="crear">
                <input type="hidden" name="creado_por" value="<?php echo $row['Nombre_Apellidos']; ?>">
                
                <div class="mb-3">
                  <label for="sorteo_nombre" class="form-label">Nombre del sorteo <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="nombre" id="sorteo_nombre" placeholder="Ej: Sorteo Día de las Madres 2026" required>
                </div>

                <div class="mb-3">
                  <label for="sorteo_descripcion" class="form-label">Descripción</label>
                  <textarea class="form-control" name="descripcion" id="sorteo_descripcion" rows="2" placeholder="Descripción del sorteo (opcional)"></textarea>
                </div>
                
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="sorteo_fecha_inicio" class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="fecha_inicio" id="sorteo_fecha_inicio" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="sorteo_fecha_fin" class="form-label">Fecha de fin <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="fecha_fin" id="sorteo_fecha_fin" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="sorteo_prefijo" class="form-label">Prefijo del folio (personalizable)</label>
                    <input type="text" class="form-control" name="prefijo_folio" id="sorteo_prefijo" placeholder="Ej: MAMA, NAVIDAD" maxlength="10">
                    <small class="text-muted">Dejar vacío para usar el prefijo de sucursal por defecto</small>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="sorteo_folio_inicio" class="form-label">Folio inicial</label>
                    <input type="number" class="form-control" name="folio_inicio" id="sorteo_folio_inicio" value="1" min="1">
                  </div>
                </div>

                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="sorteo_aplica_todas" name="aplica_todas_check" checked onchange="document.getElementById('divSucursalesNuevo').style.display = this.checked ? 'none' : 'block';">
                    <label class="form-check-label" for="sorteo_aplica_todas">
                      Aplica a <strong>todas</strong> las sucursales
                    </label>
                  </div>
                </div>

                <div id="divSucursalesNuevo" style="display:none;">
                  <div class="mb-3">
                    <label class="form-label">Seleccionar sucursales:</label>
                    <div class="border rounded p-3" style="max-height:200px; overflow-y:auto;">
                      <?php
                      $sqlSucModal = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";
                      $resSucModal = mysqli_query($conn, $sqlSucModal);
                      if ($resSucModal) {
                          while ($sucM = mysqli_fetch_assoc($resSucModal)) {
                              echo '<div class="form-check">';
                              echo '<input class="form-check-input nuevo-sucursal-cb" type="checkbox" value="' . $sucM['ID_Sucursal'] . '" id="nuevo_suc_' . $sucM['ID_Sucursal'] . '">';
                              echo '<label class="form-check-label" for="nuevo_suc_' . $sucM['ID_Sucursal'] . '">' . $sucM['Nombre_Sucursal'] . '</label>';
                              echo '</div>';
                          }
                      }
                      ?>
                    </div>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
