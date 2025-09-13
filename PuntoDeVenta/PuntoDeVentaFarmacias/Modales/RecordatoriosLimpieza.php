<div class="modal fade" id="ModalRecordatoriosLimpieza" tabindex="-1" role="dialog" aria-labelledby="ModalRecordatoriosLimpiezaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="ModalRecordatoriosLimpiezaLabel">Recordatorios de Limpieza</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Crear Nuevo Recordatorio</h6>
                        <form id="formRecordatorio">
                            <div class="mb-3">
                                <label for="titulo_recordatorio" class="form-label">Título del Recordatorio</label>
                                <input type="text" class="form-control" id="titulo_recordatorio" name="titulo_recordatorio" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion_recordatorio" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion_recordatorio" name="descripcion_recordatorio" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_recordatorio" class="form-label">Fecha del Recordatorio</label>
                                <input type="datetime-local" class="form-control" id="fecha_recordatorio" name="fecha_recordatorio" required>
                            </div>
                            <div class="mb-3">
                                <label for="prioridad_recordatorio" class="form-label">Prioridad</label>
                                <select class="form-select" id="prioridad_recordatorio" name="prioridad_recordatorio">
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary" id="btnGuardarRecordatorio">Guardar Recordatorio</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <h6>Recordatorios Activos</h6>
                        <div id="lista-recordatorios" style="max-height: 400px; overflow-y: auto;">
                            <!-- Los recordatorios se cargarán aquí -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
