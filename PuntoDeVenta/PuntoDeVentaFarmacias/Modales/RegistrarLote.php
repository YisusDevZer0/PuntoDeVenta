<!-- Modal para Registrar Lote -->
<div class="modal fade" id="modalRegistrarLote" tabindex="-1" role="dialog" aria-labelledby="modalRegistrarLoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
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
                                <select class="form-select" id="sucursal" name="sucursal" required>
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
                                <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required placeholder="Máx. según stock sin cubrir">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarLote" onclick="guardarLote()">
                    <i class="fa fa-save me-1"></i>Registrar Lote
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var permiteRegistrarLoteActual = false;

function abrirModalRegistrarLote() {
    document.getElementById('formRegistrarLote').reset();
    document.getElementById('infoProducto').style.display = 'none';
    permiteRegistrarLoteActual = false;
    document.getElementById('btnGuardarLote').disabled = false;
    document.getElementById('cantidad').removeAttribute('max');
    document.getElementById('cantidad').disabled = false;
    document.getElementById('lote').disabled = false;
    document.getElementById('fechaCaducidad').disabled = false;

    cargarSucursalesModal();
    var myModal = new bootstrap.Modal(document.getElementById('modalRegistrarLote'));
    myModal.show();
}

function buscarProducto() {
    const codigoBarra = document.getElementById('codigoBarra').value.trim();
    const sucursal = document.getElementById('sucursal').value;
    
    if (!codigoBarra || !sucursal) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Por favor ingresa el código de barras y selecciona la sucursal'
        });
        return;
    }
    
    Swal.fire({
        title: 'Buscando producto...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    const url = `api/buscar_producto_registrar_lote.php?codigo=${encodeURIComponent(codigoBarra)}&sucursal=${encodeURIComponent(sucursal)}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                var p = data.producto;
                permiteRegistrarLoteActual = !!p.permite_registrar_lote;
                
                var msg = '';
                if (permiteRegistrarLoteActual) {
                    msg = `<span class="text-success"><strong>Puede registrar lote.</strong> Stock sin cubrir: ${p.sin_cubrir} unidad(es).</span>`;
                    document.getElementById('cantidad').max = p.sin_cubrir;
                    document.getElementById('cantidad').disabled = false;
                    document.getElementById('lote').disabled = false;
                    document.getElementById('fechaCaducidad').disabled = false;
                    document.getElementById('btnGuardarLote').disabled = false;
                } else {
                    msg = '<span class="text-danger"><strong>Todo el stock tiene lote y fecha de caducidad.</strong> No se permiten más altas.</span>';
                    document.getElementById('cantidad').removeAttribute('max');
                    document.getElementById('cantidad').disabled = true;
                    document.getElementById('lote').disabled = true;
                    document.getElementById('fechaCaducidad').disabled = true;
                    document.getElementById('btnGuardarLote').disabled = true;
                }
                
                document.getElementById('detallesProducto').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nombre:</strong> ${p.nombre_producto || ''}<br>
                            <strong>Código:</strong> ${p.cod_barra || ''}<br>
                            <strong>Existencias:</strong> ${p.existencia_total ?? 0} | En lotes: ${p.en_lotes ?? 0} | Sin cubrir: ${p.sin_cubrir ?? 0}
                        </div>
                        <div class="col-md-6">
                            <strong>Precio Venta:</strong> $${p.precio_venta ?? 0}<br>
                            <strong>Precio Compra:</strong> $${p.precio_compra ?? 0}<br>
                            ${msg}
                        </div>
                    </div>
                `;
                document.getElementById('infoProducto').style.display = 'block';
                document.getElementById('precioVenta').value = p.precio_venta ?? '';
                document.getElementById('precioCompra').value = p.precio_compra ?? '';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Producto no encontrado',
                    text: data.error || 'Error al buscar'
                });
            }
        })
        .catch(function(err) {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al buscar: ' + (err && err.message ? err.message : 'desconocido') });
        });
}

function guardarLote() {
    if (!permiteRegistrarLoteActual) {
        Swal.fire({
            icon: 'warning',
            title: 'No permitido',
            text: 'Todo el stock de este producto tiene lote y caducidad. No se permiten más altas.'
        });
        return;
    }
    
    const form = document.getElementById('formRegistrarLote');
    const formData = new FormData(form);
    const requiredFields = ['codigoBarra', 'sucursal', 'lote', 'fechaCaducidad', 'cantidad'];
    for (var i = 0; i < requiredFields.length; i++) {
        if (!formData.get(requiredFields[i])) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: 'Completa todos los campos obligatorios.'
            });
            return;
        }
    }
    
    Swal.fire({
        title: 'Registrando lote...',
        allowOutsideClick: false,
        didOpen: function() { Swal.showLoading(); }
    });
    
    var formDataToSend = new FormData();
    formDataToSend.append('codigoBarra', formData.get('codigoBarra'));
    formDataToSend.append('sucursal', formData.get('sucursal'));
    formDataToSend.append('lote', formData.get('lote'));
    formDataToSend.append('fechaCaducidad', formData.get('fechaCaducidad'));
    formDataToSend.append('cantidad', formData.get('cantidad'));
    formDataToSend.append('observaciones', formData.get('observaciones') || '');
    
    fetch('api/registrar_lote.php', { method: 'POST', body: formDataToSend })
        .then(function(response) {
            if (!response.ok) throw new Error('Error de red: ' + response.status);
            return response.text().then(function(text) {
                try { return JSON.parse(text); } catch (e) {
                    console.error('Error parsing JSON:', text);
                    throw new Error('Error al procesar respuesta');
                }
            });
        })
        .then(function(data) {
            Swal.close();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Lote registrado',
                    text: data.message || 'Registrado correctamente.'
                }).then(function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalRegistrarLote'));
                    if (modal) modal.hide();
                    if (typeof tabla !== 'undefined') tabla.ajax.reload();
                    if (typeof cargarEstadisticas === 'function') cargarEstadisticas();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'No se pudo registrar el lote.'
                });
            }
        })
        .catch(function(err) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al registrar: ' + (err && err.message ? err.message : 'desconocido')
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
