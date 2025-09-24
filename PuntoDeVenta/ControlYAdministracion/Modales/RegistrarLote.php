<!-- Modal para Registrar Lote -->
<div class="modal fade" id="modalRegistrarLote" tabindex="-1" role="dialog" aria-labelledby="modalRegistrarLoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrarLoteLabel">
                    <i class="fa fa-plus-circle mr-2"></i>Registrar Nuevo Lote
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formRegistrarLote">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigoBarra" class="form-label">Código de Barras *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="codigoBarra" name="codigoBarra" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="buscarProducto()">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Escanea o ingresa el código de barras del producto</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sucursal" class="form-label">Sucursal *</label>
                                <select class="form-control" id="sucursal" name="sucursal" required>
                                    <option value="">Seleccionar sucursal</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lote" class="form-label">Número de Lote *</label>
                                <input type="text" class="form-control" id="lote" name="lote" required>
                                <small class="form-text text-muted">Ingresa el número de lote del producto</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaCaducidad" class="form-label">Fecha de Caducidad *</label>
                                <input type="date" class="form-control" id="fechaCaducidad" name="fechaCaducidad" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cantidad" class="form-label">Cantidad *</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="proveedor" class="form-label">Proveedor</label>
                                <input type="text" class="form-control" id="proveedor" name="proveedor">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precioCompra" class="form-label">Precio de Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precioCompra" name="precioCompra" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precioVenta" class="form-label">Precio de Venta</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precioVenta" name="precioVenta" step="0.01" min="0" readonly>
                                </div>
                                <small class="form-text text-muted">Precio automático del sistema</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>
                    
                    <!-- Información del producto encontrado -->
                    <div id="infoProducto" class="alert alert-info" style="display: none;">
                        <h6><i class="fa-solid fa-info-circle me-2"></i>Información del Producto</h6>
                        <div id="detallesProducto"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarLote()">
                    <i class="fa fa-save mr-1"></i>Registrar Lote
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalRegistrarLote() {
    // Limpiar formulario
    document.getElementById('formRegistrarLote').reset();
    document.getElementById('infoProducto').style.display = 'none';
    
    // Cargar sucursales
    cargarSucursalesModal();
    
    // Mostrar modal
    $('#modalRegistrarLote').modal('show');
}

function buscarProducto() {
    const codigoBarra = document.getElementById('codigoBarra').value;
    const sucursal = document.getElementById('sucursal').value;
    
    if (!codigoBarra || !sucursal) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Por favor ingresa el código de barras y selecciona la sucursal'
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Buscando producto...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`api/buscar_producto.php?codigo=${codigoBarra}&sucursal=${sucursal}`)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                // Mostrar información del producto
                document.getElementById('detallesProducto').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nombre:</strong> ${data.producto.nombre}<br>
                            <strong>Código:</strong> ${data.producto.codigo}
                        </div>
                        <div class="col-md-6">
                            <strong>Precio Venta:</strong> $${data.producto.precio_venta}<br>
                            <strong>Stock Actual:</strong> ${data.producto.stock}
                        </div>
                    </div>
                `;
                document.getElementById('infoProducto').style.display = 'block';
                document.getElementById('precioVenta').value = data.producto.precio_venta;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Producto no encontrado',
                    text: data.error
                });
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al buscar el producto: ' + error.message
            });
        });
}

function guardarLote() {
    const form = document.getElementById('formRegistrarLote');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    const requiredFields = ['codigoBarra', 'sucursal', 'lote', 'fechaCaducidad', 'cantidad'];
    for (let field of requiredFields) {
        if (!formData.get(field)) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: `Por favor completa el campo: ${field}`
            });
            return;
        }
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Registrando lote...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Preparar datos
    const datos = {
        cod_barra: formData.get('codigoBarra'),
        sucursal_id: formData.get('sucursal'),
        lote: formData.get('lote'),
        fecha_caducidad: formData.get('fechaCaducidad'),
        cantidad: formData.get('cantidad'),
        proveedor: formData.get('proveedor'),
        precio_compra: formData.get('precioCompra'),
        observaciones: formData.get('observaciones'),
        usuario_registro: <?php echo isset($row['Id_PvUser']) ? $row['Id_PvUser'] : 1; ?>
    };
    
    fetch('api/registrar_lote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Lote registrado',
                text: data.message
            }).then(() => {
                // Cerrar modal y recargar datos
                $('#modalRegistrarLote').modal('hide');
                cargarProductosCaducados();
                cargarEstadisticas();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al registrar el lote: ' + error.message
        });
    });
}

function cargarSucursalesModal() {
    fetch('api/obtener_sucursales.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('sucursal');
            select.innerHTML = '<option value="">Seleccionar sucursal</option>';
            
            data.sucursales.forEach(sucursal => {
                const option = document.createElement('option');
                option.value = sucursal.id;
                option.textContent = sucursal.nombre;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar sucursales:', error);
        });
}
</script>
