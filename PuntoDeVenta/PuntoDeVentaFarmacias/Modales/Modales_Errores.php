<!-- Modal de Errores Comunes -->
<div class="modal fade" id="modalError" tabindex="-1" aria-labelledby="modalErrorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalErrorLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i>Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="mensajeError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalConfirmacionLabel">
                    <i class="fa fa-question-circle me-2"></i>Confirmación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="mensajeConfirmacion"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnConfirmar">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarError(mensaje) {
    document.getElementById('mensajeError').innerHTML = mensaje;
    var modal = new bootstrap.Modal(document.getElementById('modalError'));
    modal.show();
}

function mostrarConfirmacion(mensaje, callback) {
    document.getElementById('mensajeConfirmacion').innerHTML = mensaje;
    document.getElementById('btnConfirmar').onclick = function() {
        callback();
        var modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacion'));
        modal.hide();
    };
    var modal = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
    modal.show();
}
</script>