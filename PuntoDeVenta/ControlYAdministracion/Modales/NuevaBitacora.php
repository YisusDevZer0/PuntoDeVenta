<div class="modal fade" id="ModalNuevaBitacora" tabindex="-1" role="dialog" aria-labelledby="ModalNuevaBitacoraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="ModalNuevaBitacoraLabel">Nueva Bitácora de Limpieza</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaBitacora">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sucursal_id" class="form-label">Sucursal <span class="text-danger">*</span></label>
                                <select class="form-select" id="sucursal_id" name="sucursal_id" required>
                                    <option value="">-- Seleccione una sucursal --</option>
                                    <!-- Las opciones se cargarán dinámicamente -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="area" class="form-label">Área <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="area" name="area" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="semana" class="form-label">Semana <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="semana" name="semana" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="responsable" class="form-label">Responsable <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="responsable" name="responsable" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="supervisor" class="form-label">Supervisor</label>
                                <input type="text" class="form-control" id="supervisor" name="supervisor">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="aux_res" class="form-label">Auxiliar Responsable</label>
                                <input type="text" class="form-control" id="aux_res" name="aux_res">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarBitacora">Guardar Bitácora</button>
            </div>
        </div>
    </div>
</div>