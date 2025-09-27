<!-- Modal para Errores -->
<div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="modalErrorLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalErrorLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i>Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="mensajeError">
                    <!-- Se llenará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarError(mensaje) {
    document.getElementById('mensajeError').innerHTML = mensaje;
    const modal = new bootstrap.Modal(document.getElementById('modalError'));
    modal.show();
}
</script>