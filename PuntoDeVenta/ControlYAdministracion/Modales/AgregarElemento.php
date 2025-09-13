<div class="modal fade" id="ModalAgregarElemento" tabindex="-1" role="dialog" aria-labelledby="ModalAgregarElementoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="ModalAgregarElementoLabel">Agregar Elemento de Limpieza</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarElemento">
                    <input type="hidden" id="id_bitacora_elemento" name="id_bitacora">
                    <div class="mb-3">
                        <label for="elemento" class="form-label">Elemento de Limpieza <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="elemento" name="elemento" required placeholder="Ej: Limpieza de mostrador, Desinfección de equipos, etc.">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnGuardarElemento">Agregar Elemento</button>
            </div>
        </div>
    </div>
</div>