
<!-- Ventana modal -->
<div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header">
        <h5 class="modal-title" id="modalExitoLabel">¡Éxito!</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-center">La información se ha agregado con éxito.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<style>
    /* Estilo para el fondo oscuro del modal */
.modal-content {
  background-color: #000;
}

/* Estilo para el botón de cerrar (botón cruz) */
.btn-close-white {
  color: #fff;
}

/* Estilo para el botón Aceptar */
.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}

.btn-primary:focus {
  box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.5);
}

</style>