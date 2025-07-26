// Sistema de Gestión de Pedidos - JavaScript Moderno
class SistemaPedidos {
    constructor() {
        this.pedidos = [];
        this.productosSeleccionados = [];
        this.sortable = null;
        this.init();
    }

    init() {
        this.cargarPedidos();
        this.setupEventListeners();
        this.setupSortable();
        this.cargarEstadisticas();
    }

    setupEventListeners() {
        // Botones principales
        $('#btnNuevoPedido').on('click', () => this.abrirModalNuevoPedido());
        $('#btnRefresh').on('click', () => this.cargarPedidos());
        $('#btnStockBajo').on('click', () => this.mostrarProductosStockBajo());
        $('#btnFiltrar').on('click', () => this.aplicarFiltros());
        $('#btnLimpiar').on('click', () => this.limpiarFiltros());
        $('#btnCrearPrimerPedido').on('click', () => this.abrirModalNuevoPedido());

        // Búsqueda en tiempo real
        let timeoutBusqueda;
        $('#busqueda').on('input', (e) => {
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(() => {
                this.aplicarFiltros();
            }, 500);
        });

        // Búsqueda de productos
        $('#btnBuscarProducto').on('click', () => this.buscarProductos());
        $('#busqueda-producto').on('keypress', (e) => {
            if (e.which === 13) this.buscarProductos();
        });

        // Guardar pedido
        $('#btnGuardarPedido').on('click', () => this.guardarPedido());

        // Filtros
        $('#filtro-estado, #filtro-fecha-inicio, #filtro-fecha-fin').on('change', () => {
            this.aplicarFiltros();
        });
    }

    setupSortable() {
        // Configurar drag & drop para productos del pedido
        this.sortable = new Sortable(document.getElementById('productos-pedido'), {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: () => this.actualizarResumen()
        });
    }

    async cargarPedidos() {
        try {
            this.mostrarLoading(true);
            
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'listar_pedidos',
                busqueda: $('#busqueda').val(),
                filtro_estado: $('#filtro-estado').val(),
                filtro_fecha_inicio: $('#filtro-fecha-inicio').val(),
                filtro_fecha_fin: $('#filtro-fecha-fin').val()
            });

            if (response.status === 'ok') {
                this.pedidos = response.data;
                this.renderizarPedidos();
                this.cargarEstadisticas();
            } else {
                this.mostrarError('Error al cargar pedidos: ' + response.msg);
            }
        } catch (error) {
            this.mostrarError('Error de conexión al cargar pedidos');
        } finally {
            this.mostrarLoading(false);
        }
    }

    renderizarPedidos() {
        const container = $('#lista-pedidos');
        
        if (this.pedidos.length === 0) {
            $('#empty-state').show();
            container.hide();
            return;
        }

        $('#empty-state').hide();
        container.show();

        let html = '';
        this.pedidos.forEach(pedido => {
            html += this.crearHTMLPedido(pedido);
        });

        container.html(html);

        // Agregar event listeners a los botones de acción
        this.setupPedidoEventListeners();
    }

    crearHTMLPedido(pedido) {
        const fecha = new Date(pedido.fecha_creacion).toLocaleDateString('es-ES');
        const estadoClass = `estado-${pedido.estado}`;
        const prioridadClass = `prioridad-${pedido.prioridad}`;
        
        return `
            <div class="pedido-item" data-pedido-id="${pedido.id}">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <strong>${pedido.folio}</strong>
                        <br>
                        <small class="text-muted">${fecha}</small>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <span class="estado-badge ${estadoClass} me-2">${pedido.estado}</span>
                            <span class="prioridad-badge ${prioridadClass}">${pedido.prioridad}</span>
                        </div>
                        <br>
                        <small class="text-muted">${pedido.usuario_nombre || 'N/A'}</small>
                    </div>
                    <div class="col-md-2">
                        <strong>${pedido.total_productos || 0}</strong> productos
                        <br>
                        <small class="text-muted">${pedido.total_cantidad || 0} unidades</small>
                    </div>
                    <div class="col-md-2">
                        <strong>$${parseFloat(pedido.total_estimado || 0).toFixed(2)}</strong>
                        <br>
                        <small class="text-muted">${pedido.Nombre_Sucursal}</small>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary btn-sm ver-detalle" data-pedido-id="${pedido.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${pedido.estado === 'pendiente' ? `
                                <button class="btn btn-outline-success btn-sm aprobar-pedido" data-pedido-id="${pedido.id}">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm rechazar-pedido" data-pedido-id="${pedido.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-outline-warning btn-sm eliminar-pedido" data-pedido-id="${pedido.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    setupPedidoEventListeners() {
        // Ver detalle
        $('.ver-detalle').on('click', (e) => {
            const pedidoId = $(e.currentTarget).data('pedido-id');
            this.verDetallePedido(pedidoId);
        });

        // Aprobar pedido
        $('.aprobar-pedido').on('click', (e) => {
            const pedidoId = $(e.currentTarget).data('pedido-id');
            this.cambiarEstadoPedido(pedidoId, 'aprobado');
        });

        // Rechazar pedido
        $('.rechazar-pedido').on('click', (e) => {
            const pedidoId = $(e.currentTarget).data('pedido-id');
            this.cambiarEstadoPedido(pedidoId, 'rechazado');
        });

        // Eliminar pedido
        $('.eliminar-pedido').on('click', (e) => {
            const pedidoId = $(e.currentTarget).data('pedido-id');
            this.eliminarPedido(pedidoId);
        });
    }

    async verDetallePedido(pedidoId) {
        try {
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'detalle_pedido',
                pedido_id: pedidoId
            });

            if (response.status === 'ok') {
                this.mostrarModalDetalle(response);
            } else {
                this.mostrarError('Error al cargar detalle del pedido');
            }
        } catch (error) {
            this.mostrarError('Error de conexión');
        }
    }

    mostrarModalDetalle(data) {
        const pedido = data.pedido;
        const detalles = data.detalles;
        const historial = data.historial;

        let html = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Información del Pedido</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Folio:</strong></td><td>${pedido.folio}</td></tr>
                        <tr><td><strong>Estado:</strong></td><td><span class="estado-badge estado-${pedido.estado}">${pedido.estado}</span></td></tr>
                        <tr><td><strong>Prioridad:</strong></td><td><span class="prioridad-badge prioridad-${pedido.prioridad}">${pedido.prioridad}</span></td></tr>
                        <tr><td><strong>Sucursal:</strong></td><td>${pedido.Nombre_Sucursal}</td></tr>
                        <tr><td><strong>Usuario:</strong></td><td>${pedido.usuario_nombre}</td></tr>
                        <tr><td><strong>Fecha:</strong></td><td>${new Date(pedido.fecha_creacion).toLocaleString('es-ES')}</td></tr>
                        <tr><td><strong>Total:</strong></td><td>$${parseFloat(pedido.total_estimado || 0).toFixed(2)}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Observaciones</h6>
                    <p class="text-muted">${pedido.observaciones || 'Sin observaciones'}</p>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Productos del Pedido</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Cantidad Solicitada</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
        `;

        detalles.forEach(detalle => {
            html += `
                <tr>
                    <td>${detalle.Nombre_Prod}</td>
                    <td>${detalle.Cod_Barra || 'N/A'}</td>
                    <td>${detalle.cantidad_solicitada}</td>
                    <td>$${parseFloat(detalle.precio_unitario || 0).toFixed(2)}</td>
                    <td>$${parseFloat(detalle.subtotal || 0).toFixed(2)}</td>
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;

        if (historial.length > 0) {
            html += `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Historial de Cambios</h6>
                        <div class="timeline">
            `;

            historial.forEach(cambio => {
                const fecha = new Date(cambio.fecha_cambio).toLocaleString('es-ES');
                html += `
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <p class="mb-1"><strong>${cambio.usuario_nombre}</strong> - ${fecha}</p>
                            <p class="mb-1">${cambio.estado_anterior || 'Nuevo'} → ${cambio.estado_nuevo}</p>
                            ${cambio.comentario ? `<small class="text-muted">${cambio.comentario}</small>` : ''}
                        </div>
                    </div>
                `;
            });

            html += `
                        </div>
                    </div>
                </div>
            `;
        }

        $('#detalle-pedido-content').html(html);
        $('#modalDetallePedido').modal('show');
    }

    async cambiarEstadoPedido(pedidoId, nuevoEstado) {
        try {
            const { value: comentario } = await Swal.fire({
                title: `Cambiar estado a ${nuevoEstado}`,
                input: 'textarea',
                inputLabel: 'Comentario (opcional)',
                inputPlaceholder: 'Escribe un comentario sobre este cambio...',
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar'
            });

            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'cambiar_estado',
                pedido_id: pedidoId,
                nuevo_estado: nuevoEstado,
                comentario: comentario || ''
            });

            if (response.status === 'ok') {
                this.mostrarExito('Estado actualizado correctamente');
                this.cargarPedidos();
            } else {
                this.mostrarError('Error al actualizar estado: ' + response.msg);
            }
        } catch (error) {
            this.mostrarError('Error de conexión');
        }
    }

    async eliminarPedido(pedidoId) {
        try {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                const response = await $.post('Controladores/PedidosController.php', {
                    accion: 'eliminar_pedido',
                    pedido_id: pedidoId
                });

                if (response.status === 'ok') {
                    this.mostrarExito('Pedido eliminado correctamente');
                    this.cargarPedidos();
                } else {
                    this.mostrarError('Error al eliminar pedido: ' + response.msg);
                }
            }
        } catch (error) {
            this.mostrarError('Error de conexión');
        }
    }

    abrirModalNuevoPedido() {
        this.limpiarFormularioNuevoPedido();
        $('#modalNuevoPedido').modal('show');
    }

    limpiarFormularioNuevoPedido() {
        $('#busqueda-producto').val('');
        $('#observaciones-pedido').val('');
        $('#prioridad-pedido').val('normal');
        $('#resultados-busqueda').empty();
        $('#productos-pedido').html(`
            <div class="text-center text-muted py-4">
                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                <p>Arrastra productos aquí o busca productos para agregar</p>
            </div>
        `);
        this.productosSeleccionados = [];
        this.actualizarResumen();
    }

    async buscarProductos() {
        const query = $('#busqueda-producto').val().trim();
        
        if (query.length < 3) {
            this.mostrarError('Ingresa al menos 3 caracteres para buscar');
            return;
        }

        try {
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'buscar_producto',
                q: query
            });

            if (response.status === 'ok') {
                this.mostrarResultadosBusqueda(response.data);
            } else {
                this.mostrarError('Error al buscar productos');
            }
        } catch (error) {
            this.mostrarError('Error de conexión');
        }
    }

    mostrarResultadosBusqueda(productos) {
        const container = $('#resultados-busqueda');
        
        if (productos.length === 0) {
            container.html('<p class="text-muted">No se encontraron productos</p>');
            return;
        }

        let html = '<h6>Resultados de búsqueda:</h6><div class="row">';
        
        productos.forEach(producto => {
            const yaSeleccionado = this.productosSeleccionados.some(p => p.id === producto.ID_Prod_POS);
            const stockClass = producto.Existencias_R < producto.Min_Existencia ? 'text-danger' : 'text-success';
            
            html += `
                <div class="col-md-6 mb-2">
                    <div class="producto-card ${yaSeleccionado ? 'border-success' : ''}" 
                         data-producto='${JSON.stringify(producto)}'>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${producto.Nombre_Prod}</h6>
                                <p class="mb-1 small text-muted">
                                    Código: ${producto.Cod_Barra || 'N/A'} | 
                                    Clave: ${producto.Clave_adicional || 'N/A'}
                                </p>
                                <p class="mb-1 small">
                                    Stock: <span class="${stockClass}">${producto.Existencias_R}</span> | 
                                    Mín: ${producto.Min_Existencia} | 
                                    Máx: ${producto.Max_Existencia}
                                </p>
                                <p class="mb-0 small">
                                    <strong>Precio: $${parseFloat(producto.Precio_Venta || 0).toFixed(2)}</strong>
                                </p>
                            </div>
                            <div class="ms-2">
                                ${yaSeleccionado ? 
                                    '<span class="badge bg-success">Agregado</span>' : 
                                    '<button class="btn btn-primary btn-sm agregar-producto">Agregar</button>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.html(html);

        // Event listeners para agregar productos
        $('.agregar-producto').on('click', (e) => {
            const card = $(e.currentTarget).closest('.producto-card');
            const producto = JSON.parse(card.data('producto'));
            this.agregarProductoAPedido(producto);
        });
    }

    agregarProductoAPedido(producto) {
        // Verificar si ya está agregado
        if (this.productosSeleccionados.some(p => p.id === producto.ID_Prod_POS)) {
            this.mostrarError('Este producto ya está en el pedido');
            return;
        }

        // Agregar al array
        this.productosSeleccionados.push({
            id: producto.ID_Prod_POS,
            nombre: producto.Nombre_Prod,
            codigo: producto.Cod_Barra,
            precio: parseFloat(producto.Precio_Venta || 0),
            cantidad: 1,
            stock: producto.Existencias_R,
            min_stock: producto.Min_Existencia
        });

        // Actualizar la vista
        this.actualizarVistaProductos();
        this.actualizarResumen();
    }

    actualizarVistaProductos() {
        const container = $('#productos-pedido');
        
        if (this.productosSeleccionados.length === 0) {
            container.html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <p>Arrastra productos aquí o busca productos para agregar</p>
                </div>
            `);
            return;
        }

        let html = '';
        this.productosSeleccionados.forEach((producto, index) => {
            const stockClass = producto.stock < producto.min_stock ? 'text-danger' : 'text-success';
            
            html += `
                <div class="producto-card" data-index="${index}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-grip-vertical drag-handle me-2"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${producto.nombre}</h6>
                            <p class="mb-1 small text-muted">
                                Código: ${producto.codigo || 'N/A'} | 
                                Stock: <span class="${stockClass}">${producto.stock}</span>
                            </p>
                        </div>
                        <div class="d-flex align-items-center">
                            <input type="number" class="form-control form-control-sm me-2" 
                                   style="width: 80px;" min="1" value="${producto.cantidad}"
                                   onchange="sistemaPedidos.actualizarCantidad(${index}, this.value)">
                            <span class="me-2">$${producto.precio.toFixed(2)}</span>
                            <button class="btn btn-danger btn-sm" onclick="sistemaPedidos.eliminarProducto(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
    }

    actualizarCantidad(index, cantidad) {
        if (this.productosSeleccionados[index]) {
            this.productosSeleccionados[index].cantidad = parseInt(cantidad) || 1;
            this.actualizarResumen();
        }
    }

    eliminarProducto(index) {
        this.productosSeleccionados.splice(index, 1);
        this.actualizarVistaProductos();
        this.actualizarResumen();
    }

    actualizarResumen() {
        const totalProductos = this.productosSeleccionados.length;
        const totalCantidad = this.productosSeleccionados.reduce((sum, p) => sum + p.cantidad, 0);
        const totalPrecio = this.productosSeleccionados.reduce((sum, p) => sum + (p.precio * p.cantidad), 0);

        $('#total-productos').text(totalProductos);
        $('#total-cantidad').text(totalCantidad);
        $('#total-precio').text(`$${totalPrecio.toFixed(2)}`);
        $('#total-final').text(`$${totalPrecio.toFixed(2)}`);
    }

    async guardarPedido() {
        if (this.productosSeleccionados.length === 0) {
            this.mostrarError('Debes agregar al menos un producto al pedido');
            return;
        }

        try {
            const observaciones = $('#observaciones-pedido').val();
            const prioridad = $('#prioridad-pedido').val();

            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'crear_pedido',
                productos: JSON.stringify(this.productosSeleccionados),
                observaciones: observaciones,
                prioridad: prioridad
            });

            if (response.status === 'ok') {
                this.mostrarExito(`Pedido creado exitosamente. Folio: ${response.folio}`);
                $('#modalNuevoPedido').modal('hide');
                this.cargarPedidos();
            } else {
                this.mostrarError('Error al crear pedido: ' + response.msg);
            }
        } catch (error) {
            this.mostrarError('Error de conexión');
        }
    }

    async mostrarProductosStockBajo() {
        try {
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'productos_stock_bajo'
            });

            if (response.status === 'ok') {
                this.mostrarModalStockBajo(response.data);
            } else {
                this.mostrarError('Error al cargar productos con stock bajo');
            }
        } catch (error) {
            this.mostrarError('Error de conexión');
        }
    }

    mostrarModalStockBajo(productos) {
        let html = `
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                    Productos con Stock Bajo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Cantidad Necesaria</th>
                                <th>Precio</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        productos.forEach(producto => {
            const cantidadNecesaria = producto.cantidad_necesaria;
            html += `
                <tr>
                    <td>${producto.Nombre_Prod}</td>
                    <td>${producto.Cod_Barra || 'N/A'}</td>
                    <td><span class="text-danger">${producto.Existencias_R}</span></td>
                    <td>${producto.Min_Existencia}</td>
                    <td><strong>${cantidadNecesaria}</strong></td>
                    <td>$${parseFloat(producto.Precio_Venta || 0).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="sistemaPedidos.agregarProductoStockBajo(${JSON.stringify(producto).replace(/"/g, '&quot;')})">
                            Agregar al Pedido
                        </button>
                    </td>
                </tr>
            `;
        });

        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        // Crear modal dinámico
        const modalId = 'modalStockBajo';
        if ($('#' + modalId).length) {
            $('#' + modalId).remove();
        }

        const modal = $(`
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        ${html}
                    </div>
                </div>
            </div>
        `);

        $('body').append(modal);
        $('#' + modalId).modal('show');
    }

    agregarProductoStockBajo(producto) {
        // Cerrar modal de stock bajo
        $('#modalStockBajo').modal('hide');
        
        // Abrir modal de nuevo pedido si no está abierto
        if (!$('#modalNuevoPedido').hasClass('show')) {
            this.abrirModalNuevoPedido();
        }
        
        // Agregar producto con la cantidad necesaria
        this.productosSeleccionados.push({
            id: producto.ID_Prod_POS,
            nombre: producto.Nombre_Prod,
            codigo: producto.Cod_Barra,
            precio: parseFloat(producto.Precio_Venta || 0),
            cantidad: producto.cantidad_necesaria,
            stock: producto.Existencias_R,
            min_stock: producto.Min_Existencia
        });

        this.actualizarVistaProductos();
        this.actualizarResumen();
    }

    aplicarFiltros() {
        this.cargarPedidos();
    }

    limpiarFiltros() {
        $('#busqueda').val('');
        $('#filtro-estado').val('');
        $('#filtro-fecha-inicio').val('');
        $('#filtro-fecha-fin').val('');
        this.cargarPedidos();
    }

    async cargarEstadisticas() {
        try {
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'estadisticas'
            });

            if (response.status === 'ok') {
                const stats = response.data;
                let pendientes = 0, aprobados = 0, proceso = 0, total = 0;

                stats.forEach(stat => {
                    switch (stat.estado) {
                        case 'pendiente':
                            pendientes = stat.total;
                            break;
                        case 'aprobado':
                            aprobados = stat.total;
                            break;
                        case 'en_proceso':
                            proceso = stat.total;
                            break;
                    }
                    total += parseFloat(stat.total_valor || 0);
                });

                $('#stats-pendientes').text(pendientes);
                $('#stats-aprobados').text(aprobados);
                $('#stats-proceso').text(proceso);
                $('#stats-total').text(`$${total.toFixed(2)}`);
            }
        } catch (error) {
            console.error('Error al cargar estadísticas:', error);
        }
    }

    mostrarLoading(mostrar) {
        if (mostrar) {
            $('#loading-spinner').show();
            $('#lista-pedidos').hide();
        } else {
            $('#loading-spinner').hide();
            $('#lista-pedidos').show();
        }
    }

    mostrarExito(mensaje) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: mensaje,
            timer: 3000,
            showConfirmButton: false
        });
    }

    mostrarError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje
        });
    }
}

// Inicializar el sistema cuando el documento esté listo
$(document).ready(function() {
    window.sistemaPedidos = new SistemaPedidos();
}); 