/**
 * Sistema de Escaneo Múltiple para Devoluciones
 * Doctor Pez - Sistema de Clínicas y Farmacias
 */

class DevolucionesScanner {
    constructor() {
        this.productos = [];
        this.scannerActivo = false;
        this.camara = null;
        this.stream = null;
        this.ultimoEscaneo = 0;
        this.debounceTime = 1000; // 1 segundo entre escaneos
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupScanner();
        this.cargarProductos();
    }
    
    setupEventListeners() {
        // Enter en código de barras
        $('#codigo-barras').on('keypress', (e) => {
            if (e.which === 13) {
                this.buscarProducto();
            }
        });
        
        // Cambio en cantidad
        $(document).on('change', '.cantidad-input', (e) => {
            const itemKey = $(e.target).closest('.product-item').data('key');
            const nuevaCantidad = parseInt($(e.target).val());
            this.actualizarCantidad(itemKey, nuevaCantidad);
        });
        
        // Eliminar producto
        $(document).on('click', '.btn-eliminar', (e) => {
            const itemKey = $(e.target).closest('.product-item').data('key');
            this.eliminarProducto(itemKey);
        });
        
        // Procesar devolución
        $('#btn-procesar').on('click', () => {
            this.procesarDevolucion();
        });
        
        // Cancelar devolución
        $('#btn-cancelar').on('click', () => {
            this.cancelarDevolucion();
        });
        
        // Activar cámara
        $('#btn-camara').on('click', () => {
            this.toggleCamara();
        });
        
        // Cerrar modal de producto
        $('#modalProducto').on('hidden.bs.modal', () => {
            this.limpiarModalProducto();
        });
    }
    
    setupScanner() {
        // Configurar QuaggaJS para escaneo de códigos de barras
        if (typeof Quagga !== 'undefined') {
            this.configurarQuagga();
        } else {
            console.warn('QuaggaJS no está cargado. El escaneo por cámara no estará disponible.');
        }
    }
    
    configurarQuagga() {
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#scanner-container'),
                constraints: {
                    width: 640,
                    height: 480,
                    facingMode: "environment"
                },
            },
            decoder: {
                readers: [
                    "code_128_reader",
                    "ean_reader",
                    "ean_8_reader",
                    "code_39_reader",
                    "code_39_vin_reader",
                    "codabar_reader",
                    "upc_reader",
                    "upc_e_reader",
                    "i2of5_reader"
                ]
            },
            locate: true,
            locator: {
                patchSize: "medium",
                halfSample: true
            },
            numOfWorkers: 2,
            frequency: 10,
            debug: {
                drawBoundingBox: false,
                showFrequency: false,
                drawScanline: false,
                showPatch: false
            },
            multiple: false
        }, (err) => {
            if (err) {
                console.error('Error al inicializar Quagga:', err);
                return;
            }
            console.log("QuaggaJS inicializado correctamente");
        });
        
        // Detectar códigos escaneados
        Quagga.onDetected((data) => {
            const codigo = data.codeResult.code;
            const ahora = Date.now();
            
            // Evitar escaneos duplicados
            if (ahora - this.ultimoEscaneo < this.debounceTime) {
                return;
            }
            
            this.ultimoEscaneo = ahora;
            this.procesarCodigoEscaneado(codigo);
        });
    }
    
    procesarCodigoEscaneado(codigo) {
        console.log('Código escaneado:', codigo);
        
        // Mostrar feedback visual
        this.mostrarFeedbackEscaneo(codigo);
        
        // Buscar producto
        this.buscarProductoPorCodigo(codigo);
    }
    
    mostrarFeedbackEscaneo(codigo) {
        const feedback = $(`
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999;">
                <i class="fa-solid fa-check-circle me-2"></i>
                Código escaneado: <strong>${codigo}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(feedback);
        
        // Auto-remover después de 3 segundos
        setTimeout(() => {
            feedback.alert('close');
        }, 3000);
    }
    
    buscarProductoPorCodigo(codigo) {
        $.ajax({
            url: 'api/devoluciones_api.php',
            method: 'POST',
            data: {
                action: 'buscar_producto',
                codigo_barras: codigo
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    this.mostrarModalProducto(response.producto, response.ventas_recientes);
                } else {
                    this.mostrarError('Producto no encontrado: ' + response.message);
                }
            },
            error: () => {
                this.mostrarError('Error al buscar producto');
            }
        });
    }
    
    buscarProducto() {
        const codigoBarras = $('#codigo-barras').val().trim();
        if (!codigoBarras) {
            this.mostrarError('Ingrese un código de barras');
            return;
        }
        
        this.buscarProductoPorCodigo(codigoBarras);
    }
    
    mostrarModalProducto(producto, ventasRecientes = []) {
        $('#modal-codigo-barras').val(producto.Cod_Barra);
        $('#modal-cantidad').val(1);
        $('#modal-tipo-devolucion').val('');
        $('#modal-observaciones').val('');
        
        // Mostrar información del producto
        const info = this.crearInfoProducto(producto, ventasRecientes);
        $('#modalProducto .modal-body').prepend(info);
        
        $('#modalProducto').modal('show');
    }
    
    crearInfoProducto(producto, ventasRecientes) {
        const diasParaCaducar = this.calcularDiasParaCaducar(producto.Fecha_Caducidad);
        const alertaCaducidad = this.crearAlertaCaducidad(diasParaCaducar);
        
        let html = `
            <div class="alert alert-info">
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="mb-1">${producto.Nombre_Prod}</h6>
                        <p class="mb-1"><strong>Código:</strong> ${producto.Cod_Barra}</p>
                        <p class="mb-1"><strong>Lote:</strong> ${producto.Lote}</p>
                        <p class="mb-1"><strong>Caducidad:</strong> ${producto.Fecha_Caducidad}</p>
                        <p class="mb-1"><strong>Existencias:</strong> ${producto.Existencias_R}</p>
                        <p class="mb-0"><strong>Precio:</strong> $${parseFloat(producto.Precio_Venta).toFixed(2)}</p>
                    </div>
                    <div class="col-md-4">
                        ${alertaCaducidad}
                    </div>
                </div>
            </div>
        `;
        
        if (ventasRecientes.length > 0) {
            html += this.crearHistorialVentas(ventasRecientes);
        }
        
        return html;
    }
    
    calcularDiasParaCaducar(fechaCaducidad) {
        const hoy = new Date();
        const caducidad = new Date(fechaCaducidad);
        const diffTime = caducidad - hoy;
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }
    
    crearAlertaCaducidad(dias) {
        if (dias < 0) {
            return '<span class="badge bg-danger">CADUCADO</span>';
        } else if (dias <= 7) {
            return '<span class="badge bg-warning">Próximo a caducar (' + dias + ' días)</span>';
        } else if (dias <= 30) {
            return '<span class="badge bg-info">Caduca en ' + dias + ' días</span>';
        } else {
            return '<span class="badge bg-success">Vigente</span>';
        }
    }
    
    crearHistorialVentas(ventas) {
        let html = `
            <div class="mt-3">
                <h6>Ventas Recientes:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Ticket</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        ventas.forEach(venta => {
            html += `
                <tr>
                    <td>${this.formatearFecha(venta.Fecha_venta)}</td>
                    <td>${venta.Folio_Ticket}</td>
                    <td>${venta.Cantidad_Venta}</td>
                    <td>$${parseFloat(venta.Total_Venta).toFixed(2)}</td>
                </tr>
            `;
        });
        
        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        return html;
    }
    
    agregarProductoModal() {
        const codigoBarras = $('#modal-codigo-barras').val();
        const cantidad = parseInt($('#modal-cantidad').val());
        const tipoDevolucion = $('#modal-tipo-devolucion').val();
        const observaciones = $('#modal-observaciones').val();
        
        if (!codigoBarras || !cantidad || !tipoDevolucion) {
            this.mostrarError('Complete todos los campos requeridos');
            return;
        }
        
        $.ajax({
            url: 'api/devoluciones_api.php',
            method: 'POST',
            data: {
                action: 'agregar_producto',
                codigo_barras: codigoBarras,
                cantidad: cantidad,
                tipo_devolucion: tipoDevolucion,
                observaciones: observaciones
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    $('#modalProducto').modal('hide');
                    $('#codigo-barras').val('');
                    this.cargarProductos();
                    this.actualizarTotales();
                    this.mostrarExito('Producto agregado correctamente');
                } else {
                    this.mostrarError(response.message);
                }
            },
            error: () => {
                this.mostrarError('Error al agregar producto');
            }
        });
    }
    
    cargarProductos() {
        $.ajax({
            url: 'api/devoluciones_api.php',
            method: 'POST',
            data: {
                action: 'obtener_productos_temp'
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    this.mostrarProductos(response.productos);
                }
            }
        });
    }
    
    mostrarProductos(productos) {
        const container = $('#productos-container');
        container.empty();
        
        if (productos.length === 0) {
            container.html(`
                <div class="alert alert-info text-center">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    No hay productos agregados para devolución
                </div>
            `);
            return;
        }
        
        productos.forEach((item, index) => {
            const producto = item.producto;
            const tipoDevolucion = this.getTipoDevolucionTexto(item.tipo_devolucion);
            const colorTipo = this.getTipoColor(item.tipo_devolucion);
            
            const html = `
                <div class="product-item" data-key="${item.codigo_barras}_${producto.Lote}">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <h6 class="mb-1">${producto.Nombre_Prod}</h6>
                            <small class="text-muted">
                                Código: ${item.codigo_barras} | Lote: ${producto.Lote}
                            </small>
                            ${item.observaciones ? `<br><small class="text-muted"><strong>Obs:</strong> ${item.observaciones}</small>` : ''}
                        </div>
                        <div class="col-md-2">
                            <span class="badge tipo-badge bg-${colorTipo}">${tipoDevolucion}</span>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="number" class="form-control cantidad-input" 
                                       value="${item.cantidad}" min="1" max="${producto.Existencias_R}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <strong>$${parseFloat(item.valor_total).toFixed(2)}</strong>
                        </div>
                        <div class="col-md-1 text-end">
                            <button class="btn btn-sm btn-outline-danger btn-eliminar" 
                                    title="Eliminar producto">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.append(html);
        });
    }
    
    actualizarCantidad(itemKey, nuevaCantidad) {
        $.ajax({
            url: 'api/devoluciones_api.php',
            method: 'POST',
            data: {
                action: 'actualizar_cantidad',
                item_key: itemKey,
                cantidad: nuevaCantidad
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    this.actualizarTotales();
                } else {
                    this.mostrarError(response.message);
                }
            }
        });
    }
    
    eliminarProducto(itemKey) {
        if (confirm('¿Está seguro de eliminar este producto?')) {
            $.ajax({
                url: 'api/devoluciones_api.php',
                method: 'POST',
                data: {
                    action: 'eliminar_producto',
                    item_key: itemKey
                },
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        this.cargarProductos();
                        this.actualizarTotales();
                        this.mostrarExito('Producto eliminado correctamente');
                    } else {
                        this.mostrarError(response.message);
                    }
                }
            });
        }
    }
    
    actualizarTotales() {
        $.ajax({
            url: 'api/devoluciones_api.php',
            method: 'POST',
            data: {
                action: 'obtener_totales'
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    $('#total-productos').text(response.total_productos);
                    $('#total-unidades').text(response.total_unidades);
                    $('#valor-total').text('$' + parseFloat(response.valor_total).toFixed(2));
                }
            }
        });
    }
    
    procesarDevolucion() {
        const observaciones = $('#observaciones-generales').val();
        
        if (confirm('¿Está seguro de procesar esta devolución?')) {
            $.ajax({
                url: 'api/devoluciones_api.php',
                method: 'POST',
                data: {
                    action: 'procesar_devolucion',
                    observaciones_generales: observaciones
                },
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        this.mostrarExito('Devolución procesada correctamente. Folio: ' + response.folio);
                        this.cancelarDevolucion();
                        this.cargarDevoluciones();
                    } else {
                        this.mostrarError(response.message);
                    }
                },
                error: () => {
                    this.mostrarError('Error al procesar devolución');
                }
            });
        }
    }
    
    cancelarDevolucion() {
        if (confirm('¿Está seguro de cancelar la devolución actual?')) {
            $.ajax({
                url: 'api/devoluciones_api.php',
                method: 'POST',
                data: {
                    action: 'cancelar_devolucion'
                },
                dataType: 'json',
                success: (response) => {
                    $('#scanner-section').hide();
                    $('#productos-lista').hide();
                    $('#lista-devoluciones').show();
                    $('#codigo-barras').val('');
                    $('#observaciones-generales').val('');
                }
            });
        }
    }
    
    toggleCamara() {
        if (this.scannerActivo) {
            this.detenerCamara();
        } else {
            this.activarCamara();
        }
    }
    
    activarCamara() {
        if (typeof Quagga === 'undefined') {
            this.mostrarError('El escáner de cámara no está disponible');
            return;
        }
        
        this.scannerActivo = true;
        $('#btn-camara').html('<i class="fa-solid fa-stop me-2"></i>Detener Cámara');
        $('#btn-camara').removeClass('btn-primary').addClass('btn-danger');
        
        Quagga.start();
        
        // Mostrar contenedor del scanner
        $('#scanner-container').show();
    }
    
    detenerCamara() {
        this.scannerActivo = false;
        $('#btn-camara').html('<i class="fa-solid fa-camera me-2"></i>Activar Cámara');
        $('#btn-camara').removeClass('btn-danger').addClass('btn-primary');
        
        Quagga.stop();
        
        // Ocultar contenedor del scanner
        $('#scanner-container').hide();
    }
    
    limpiarModalProducto() {
        $('#modalProducto .alert').remove();
    }
    
    getTipoDevolucionTexto(tipo) {
        const tipos = {
            'no_facturado': 'No Facturado',
            'danado_recibir': 'Dañado al Recibir',
            'proximo_caducar': 'Próximo a Caducar',
            'caducado': 'Caducado',
            'danado_roto': 'Dañado/Roto',
            'solicitado_admin': 'Solicitado por Admin',
            'error_etiquetado': 'Error en Etiquetado',
            'defectuoso': 'Defectuoso',
            'sobrante': 'Sobrante',
            'otro': 'Otro'
        };
        return tipos[tipo] || tipo;
    }
    
    getTipoColor(tipo) {
        const colores = {
            'no_facturado': 'primary',
            'danado_recibir': 'danger',
            'proximo_caducar': 'warning',
            'caducado': 'dark',
            'danado_roto': 'danger',
            'solicitado_admin': 'info',
            'error_etiquetado': 'secondary',
            'defectuoso': 'danger',
            'sobrante': 'success',
            'otro': 'light'
        };
        return colores[tipo] || 'secondary';
    }
    
    formatearFecha(fecha) {
        return new Date(fecha).toLocaleDateString('es-ES');
    }
    
    mostrarExito(mensaje) {
        this.mostrarNotificacion(mensaje, 'success');
    }
    
    mostrarError(mensaje) {
        this.mostrarNotificacion(mensaje, 'danger');
    }
    
    mostrarNotificacion(mensaje, tipo) {
        const notificacion = $(`
            <div class="alert alert-${tipo} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999;">
                <i class="fa-solid fa-${tipo === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notificacion);
        
        setTimeout(() => {
            notificacion.alert('close');
        }, 5000);
    }
}

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    window.devolucionesScanner = new DevolucionesScanner();
});
