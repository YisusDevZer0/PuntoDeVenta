<!-- Modal Registrar Lote -->
<div class="modal fade" id="modalRegistrarLote" tabindex="-1" aria-labelledby="modalRegistrarLoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalRegistrarLoteLabel">
                    <i class="fa fa-plus-circle me-2"></i>Registrar Nuevo Lote
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistrarLote">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigoBarra" class="form-label">Código de Barra *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="codigoBarra" name="codigoBarra" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="buscarProducto()">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lote" class="form-label">Número de Lote *</label>
                                <input type="text" class="form-control" id="lote" name="lote" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaCaducidad" class="form-label">Fecha de Caducidad *</label>
                                <input type="date" class="form-control" id="fechaCaducidad" name="fechaCaducidad" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cantidad" class="form-label">Cantidad *</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sucursal" class="form-label">Sucursal *</label>
                                <select class="form-select" id="sucursal" name="sucursal" required>
                                    <option value="">Seleccionar sucursal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usuarioRegistro" class="form-label">Usuario Registro</label>
                                <input type="text" class="form-control" id="usuarioRegistro" name="usuarioRegistro" value="1" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>
                    
                    <!-- Información del producto encontrado -->
                    <div id="infoProducto" class="alert alert-info" style="display: none;">
                        <h6>Información del Producto:</h6>
                        <div id="detallesProducto"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarLote()">
                    <i class="fa fa-save me-1"></i>Registrar Lote
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function buscarProducto() {
    const codigoBarra = document.getElementById('codigoBarra').value;
    
    if (!codigoBarra) {
        Swal.fire('Error', 'Ingresa un código de barra', 'error');
        return;
    }
    
    $.get(`api/buscar_producto.php?codigo=${codigoBarra}`, function(data) {
        if (data.success) {
            const producto = data.producto;
            document.getElementById('detallesProducto').innerHTML = `
                <strong>Producto:</strong> ${producto.nombre_producto}<br>
                <strong>Precio Compra:</strong> $${producto.precio_compra}<br>
                <strong>Precio Venta:</strong> $${producto.precio_venta}
            `;
            document.getElementById('infoProducto').style.display = 'block';
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    });
}

function guardarLote() {
    const formData = new FormData(document.getElementById('formRegistrarLote'));
    
    $.ajax({
        url: 'api/registrar_lote.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success');
                // Cerrar modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalRegistrarLote'));
                modal.hide();
                // Recargar tabla
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload();
                }
            } else {
                Swal.fire('Error', data.error, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    });
}
</script>
