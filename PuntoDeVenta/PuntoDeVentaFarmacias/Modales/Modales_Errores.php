<!-- Botón para abrir la ventana modal -->
<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalError">
  Realizar Acción
</button>

<!-- Ventana modal -->
<div class="modal fade" id="modalError" tabindex="-1" aria-labelledby="modalErrorLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalErrorLabel">¡Error!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-center">Lo sentimos, no se puede realizar la acción en este momento.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div> 