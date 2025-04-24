<?php
include "../Controladores/ControladorUsuario.php";
?>
<div class="modal fade" id="SugerenciaPedidoModal" tabindex="-1" role="dialog" aria-labelledby="SugerenciaPedidoModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="SugerenciaPedidoModalLabel">Sugerencia de Pedido</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formSugerenciaPedido">
          <div class="form-group">
            <label for="nombreProducto">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombreProducto" readonly>
          </div>
          <div class="form-group">
            <label for="codigoBarras">Código de Barras</label>
            <input type="text" class="form-control" id="codigoBarras" readonly>
          </div>
          <div class="form-group">
            <label for="existenciaActual">Existencia Actual</label>
            <input type="number" class="form-control" id="existenciaActual" readonly>
          </div>
          <div class="form-group">
            <label for="cantidadSugerida">Cantidad Sugerida a Pedir</label>
            <input type="number" class="form-control" id="cantidadSugerida" required>
          </div>
          <input type="hidden" id="idProducto">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="guardarSugerencia">Guardar Sugerencia</button>
      </div>
    </div>
  </div>
</div> 