<?php
include_once "../Controladores/ControladorUsuario.php";
?>

<div class="modal fade" id="ModalOrdenSugerida" tabindex="-1" role="dialog" aria-labelledby="ModalOrdenSugeridaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalOrdenSugeridaLabel">Orden de Compra Sugerida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formOrdenSugerida">
          <input type="hidden" id="folio_prod_stock" name="folio_prod_stock">
          <div class="form-group mb-3">
            <label for="nombre_producto">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombre_producto" readonly>
          </div>
          <div class="form-group mb-3">
            <label for="codigo_barra">Código de Barras</label>
            <input type="text" class="form-control" id="codigo_barra" readonly>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="existencias">Existencias Actuales</label>
              <input type="number" class="form-control" id="existencias" readonly>
            </div>
            <div class="col">
              <label for="min_existencia">Mínimo</label>
              <input type="number" class="form-control" id="min_existencia" readonly>
            </div>
            <div class="col">
              <label for="max_existencia">Máximo</label>
              <input type="number" class="form-control" id="max_existencia" readonly>
            </div>
          </div>
          <div class="form-group mb-3">
            <label for="cantidad_sugerida">Cantidad Sugerida a Ordenar</label>
            <input type="number" class="form-control" id="cantidad_sugerida" name="cantidad_sugerida">
            <small class="form-text text-muted">Esta cantidad llevará el stock al nivel máximo establecido</small>
          </div>
          <div class="form-group mb-3">
            <label for="proveedor">Proveedor</label>
            <input type="text" class="form-control" id="proveedor" readonly>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarOrden">Guardar Orden</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    // Manejador para el botón de orden sugerida
    $(document).on('click', '.btn-orden-sugerida', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var codigo = $(this).data('codigo');
        var cantidad = $(this).data('cantidad');
        var min = $(this).data('min');
        var max = $(this).data('max');
        var existencias = $(this).data('existencias');
        var proveedor = $(this).data('proveedor');

        $('#folio_prod_stock').val(id);
        $('#nombre_producto').val(nombre);
        $('#codigo_barra').val(codigo);
        $('#cantidad_sugerida').val(cantidad);
        $('#min_existencia').val(min);
        $('#max_existencia').val(max);
        $('#existencias').val(existencias);
        $('#proveedor').val(proveedor);

        $('#ModalOrdenSugerida').modal('show');
    });

    // Manejador para guardar la orden
    $('#btnGuardarOrden').click(function() {
        var datos = {
            folio_prod_stock: $('#folio_prod_stock').val(),
            cantidad_sugerida: $('#cantidad_sugerida').val(),
            existencias_actuales: $('#existencias').val(),
            min_existencia: $('#min_existencia').val(),
            max_existencia: $('#max_existencia').val(),
            proveedor: $('#proveedor').val()
        };

        $.ajax({
            url: 'Controladores/GuardarOrdenSugerida.php',
            type: 'POST',
            data: datos,
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'La orden ha sido guardada correctamente'
                    }).then((result) => {
                        $('#ModalOrdenSugerida').modal('hide');
                        // Recargar la tabla de stocks
                        $('#DataDeServicios').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo guardar la orden'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al procesar la solicitud'
                });
            }
        });
    });
});
</script> 