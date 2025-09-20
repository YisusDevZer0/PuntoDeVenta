// Sistema de Gestión de Pedidos - JavaScript Moderno
class SistemaPedidos {
    constructor() {
        this.pedidos = [];
        this.sortable = null;
        this.modalAbierto = false;
        this.pedidoEnProceso = false;
        
        // Usar el módulo de productos
        this.productosModule = productosModule;
        
        // Verificar que el módulo esté disponible
        if (!this.productosModule) {
            console.error('Módulo de productos no disponible');
            return;
        }
        
        this.init();
    }

    init() {
        this.cargarPedidos();
        this.setupEventListeners();
        this.setupSortable();
        this.cargarEstadisticas();
        this.setupAnimations();
        this.cargarDatosGuardados();
    }

    // Cargar datos guardados en localStorage
    cargarDatosGuardados() {
        if (this.productosModule) {
            this.productosModule.cargarDatosGuardados();
        }
        this.actualizarIndicadorCarrito();
    }

    // Guardar datos en localStorage
    guardarDatos() {
        if (this.productosModule) {
            this.productosModule.guardarDatos();
        }
        this.actualizarIndicadorCarrito();
    }

    // Limpiar datos guardados
    limpiarDatosGuardados() {
        if (this.productosModule) {
            this.productosModule.limpiarDatosGuardados();
        }
        this.actualizarIndicadorCarrito();
    }

    // Actualizar indicador del carrito
    actualizarIndicadorCarrito() {
        const cantidad = this.productosModule ? this.productosModule.getProductosSeleccionados().length : 0;
        const indicador = $('#carrito-indicador');
        const cantidadSpan = $('#carrito-cantidad');
        
        if (cantidad > 0) {
            cantidadSpan.text(cantidad);
            indicador.show();
            
            // Agregar animación de pulso si es nuevo
            if (!indicador.hasClass('pulse-animation')) {
                indicador.addClass('pulse-animation');
                setTimeout(() => {
                    indicador.removeClass('pulse-animation');
                }, 2000);
            }
        } else {
            indicador.hide();
        }
    }

    setupAnimations() {
        // Agregar animaciones de entrada
        $('.stats-card').addClass('fade-in');
        
        // Animación para los pedidos al cargar
        $(document).on('pedidosLoaded', () => {
            $('.pedido-item').each((index, element) => {
                setTimeout(() => {
                    $(element).addClass('fade-in');
                }, index * 100);
            });
        });
    }

    setupEventListeners() {
        // Botones principales
        $('#btnNuevoPedido').on('click', () => this.abrirModalNuevoPedido());
        $('#btnRefresh').on('click', () => this.cargarPedidos());
        $('#btnStockBajo').on('click', () => this.mostrarProductosStockBajo());
        $('#btnListadoPedidos').on('click', () => this.abrirModalListadoPedidos());
        $('#btnDescargarExcel').on('click', () => this.descargarExcel());
        $('#btnAplicarFiltros').on('click', () => this.aplicarFiltros());
        $('#btnLimpiarFiltros').on('click', () => this.limpiarFiltros());
        $('#btnCrearPrimerPedido').on('click', () => this.abrirModalNuevoPedido());
        $('#btnContinuarPedido').on('click', () => this.abrirModalNuevoPedido());

        // Búsqueda en tiempo real
        let timeoutBusqueda;
        $('#busqueda').on('input', (e) => {
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(() => {
                this.aplicarFiltros();
            }, 500);
        });

        // Eventos del modal nuevo pedido
        $('#btnBuscarProductoNuevo').on('click', () => {
            console.log('Botón buscar clickeado');
            const query = $('#busqueda-producto-nuevo').val().trim();
            console.log('Query:', query);
            if (this.productosModule) {
                this.productosModule.buscarProductos(query, 'nuevo');
            } else {
                console.error('Módulo de productos no disponible');
            }
        });
        $('#btnGuardarPedidoNuevo').on('click', () => this.confirmarGuardarPedidoNuevo());
        $('#btnLimpiarPedidoNuevo').on('click', () => this.confirmarLimpiarPedidoNuevo());
        
        $('#busqueda-producto-nuevo').on('keypress', (e) => {
            if (e.which === 13) {
                console.log('Enter presionado');
                const query = $('#busqueda-producto-nuevo').val().trim();
                console.log('Query:', query);
                if (this.productosModule) {
                    this.productosModule.buscarProductos(query, 'nuevo');
                } else {
                    console.error('Módulo de productos no disponible');
                }
            }
        });

        // Eventos del modal nuevo pedido
        $('#modalNuevoPedido').on('shown.bs.modal', () => {
            this.modalAbierto = true;
            this.pedidoEnProceso = true;
            this.cargarDatosGuardados();
            if (this.productosModule) {
                this.productosModule.actualizarVistaProductos('nuevo');
                this.productosModule.actualizarResumen('nuevo');
            }
            
            // Reinicializar Sortable cuando el modal se abra (con delay para asegurar que el DOM esté listo)
            if (typeof Sortable !== 'undefined') {
                setTimeout(() => {
                    this.inicializarSortable();
                }, 200);
            }
        });

        $('#modalNuevoPedido').on('hidden.bs.modal', () => {
            this.modalAbierto = false;
            // No limpiar datos al cerrar, mantener persistencia
            
            // Limpiar Sortable cuando el modal se cierre
            if (this.sortableNuevo) {
                this.sortableNuevo.destroy();
                this.sortableNuevo = null;
            }
        });

        // Filtros
        $('#filtro-estado, #filtro-fecha-inicio, #filtro-fecha-fin').on('change', () => {
            this.aplicarFiltros();
        });

        // Eventos adicionales para mejor UX
        $(document).on('keydown', (e) => {
            // Ctrl/Cmd + N para nuevo pedido
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                this.abrirModalSimple();
            }
            
            // Ctrl/Cmd + R para refrescar
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                this.cargarPedidos();
            }
        });

        // Tooltips para mejor UX
        $('[data-toggle="tooltip"]').tooltip();

        // Event listener para cuando se agregue un producto
        $(document).on('productoAgregado', (e, data) => {
            this.actualizarIndicadorCarrito();
            
            // Reinicializar Sortable después de agregar productos (solo si está disponible)
            if (typeof Sortable !== 'undefined') {
                setTimeout(() => {
                    this.inicializarSortable();
                }, 100);
            }
        });
    }

    setupSortable() {
        // Configurar drag & drop para productos del pedido nuevo
        // Solo inicializar si Sortable está disponible
        if (typeof Sortable !== 'undefined') {
            this.inicializarSortable();
        } else {
            console.warn('Sortable.js no está disponible, el drag & drop no funcionará');
        }
    }

    inicializarSortable() {
        // Verificar que Sortable esté disponible
        if (typeof Sortable === 'undefined') {
            console.warn('Sortable.js no está disponible');
            return;
        }
        
        const productosContainer = document.getElementById('productos-pedido-nuevo');
        
        if (productosContainer) {
            // Destruir instancia anterior si existe
            if (this.sortableNuevo) {
                this.sortableNuevo.destroy();
            }
            
            this.sortableNuevo = new Sortable(productosContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: () => {
                    if (this.productosModule) {
                        this.productosModule.actualizarResumen('nuevo');
                    }
                    this.guardarDatos();
                },
                onStart: () => {
                    $('.sortable-ghost').addClass('shadow-lg');
                }
            });
            console.log('Sortable inicializado para productos del pedido nuevo');
        } else {
            console.warn('Elemento #productos-pedido-nuevo no encontrado, Sortable no se inicializará');
        }
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
                this.pedidos = response.data || [];
                this.renderizarPedidos();
                this.cargarEstadisticas();
                
                // Disparar evento para animaciones
                $(document).trigger('pedidosLoaded');
            } else {
                this.mostrarError('Error al cargar pedidos: ' + (response.msg || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error en cargarPedidos:', error);
            this.mostrarError('Error de conexión al cargar pedidos');
        } finally {
            this.mostrarLoading(false);
        }
    }

    renderizarPedidos() {
        const container = $('#lista-pedidos');
        
        if (!this.pedidos || this.pedidos.length === 0) {
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
        
        // Calcular tiempo transcurrido
        const tiempoTranscurrido = this.calcularTiempoTranscurrido(pedido.fecha_creacion);
        
        return `
            <div class="pedido-item bg-white border rounded p-3 mb-3 shadow-sm" data-pedido-id="${pedido.id}">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <strong class="text-dark">${pedido.folio}</strong>
                        <br>
                        <small class="text-muted">${fecha}</small>
                        <br>
                        <small class="text-secondary">${tiempoTranscurrido}</small>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <span class="badge ${estadoClass} me-2">${pedido.estado}</span>
                            <span class="badge ${prioridadClass}">${pedido.prioridad}</span>
                        </div>
                        <br>
                        <small class="text-muted">${pedido.usuario_nombre || 'N/A'}</small>
                    </div>
                    <div class="col-md-2">
                        <strong class="text-dark">${pedido.total_productos || 0}</strong> productos
                        <br>
                        <small class="text-muted">${pedido.total_cantidad || 0} unidades</small>
                    </div>
                    <div class="col-md-2">
                        <strong class="text-dark">$${parseFloat(pedido.total_estimado || 0).toFixed(2)}</strong>
                        <br>
                        <small class="text-muted">${pedido.Nombre_Sucursal}</small>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary btn-sm ver-detalle" data-pedido-id="${pedido.id}" 
                                    data-toggle="tooltip" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${pedido.estado === 'aprobado' ? `
                                <button class="btn btn-outline-success btn-sm descargar-excel-pedido" data-pedido-id="${pedido.id}"
                                        data-toggle="tooltip" title="Descargar Excel">
                                    <i class="fas fa-file-excel"></i>
                                </button>
                            ` : ''}
                            ${pedido.estado === 'pendiente' ? `
                                <button class="btn btn-outline-success btn-sm aprobar-pedido" data-pedido-id="${pedido.id}"
                                        data-toggle="tooltip" title="Aprobar pedido">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm rechazar-pedido" data-pedido-id="${pedido.id}"
                                        data-toggle="tooltip" title="Rechazar pedido">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-outline-warning btn-sm eliminar-pedido" data-pedido-id="${pedido.id}"
                                        data-toggle="tooltip" title="Eliminar pedido">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    calcularTiempoTranscurrido(fechaCreacion) {
        const ahora = new Date();
        const fecha = new Date(fechaCreacion);
        const diffMs = ahora - fecha;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffDays > 0) return `${diffDays} día(s)`;
        if (diffHours > 0) return `${diffHours} hora(s)`;
        if (diffMins > 0) return `${diffMins} minuto(s)`;
        return 'Hace un momento';
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
            this.confirmarCambiarEstado(pedidoId, 'aprobado');
        });

        // Rechazar pedido
        $('.rechazar-pedido').on('click', (e) => {
            const pedidoId = $(e.currentTarget).data('pedido-id');
            this.confirmarCambiarEstado(pedidoId, 'rechazado');
        });

        // Eliminar pedido
        $('.eliminar-pedido').on('click', (e) => {
            const pedidoId = $(e.currentTarget).data('pedido-id');
            this.confirmarEliminarPedido(pedidoId);
        });

        // Descargar Excel individual
        $('.descargar-excel-pedido').on('click', (e) => {
            const pedidoId = $(e.currentTarget).data('pedido-id');
            this.descargarExcelPedido(pedidoId);
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

        let detallesHtml = '';
        detalles.forEach(detalle => {
            detallesHtml += `
                <tr>
                    <td>${detalle.Nombre_Prod}</td>
                    <td>${detalle.cantidad_solicitada}</td>
                    <td>$${parseFloat(detalle.precio_unitario).toFixed(2)}</td>
                    <td>$${parseFloat(detalle.subtotal).toFixed(2)}</td>
                </tr>
            `;
        });

        let historialHtml = '';
        historial.forEach(hist => {
            const fecha = new Date(hist.fecha_cambio).toLocaleString('es-ES');
            historialHtml += `
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">${hist.estado_anterior || 'Nuevo'} → ${hist.estado_nuevo}</h6>
                        <p class="mb-1 small text-muted">${fecha}</p>
                        <p class="mb-0 small">${hist.comentario || 'Sin comentarios'}</p>
                        <small class="text-muted">Por: ${hist.usuario_nombre || 'Sistema'}</small>
                    </div>
                </div>
            `;
        });

        $('#detalle-pedido-folio').text(pedido.folio);
        $('#detalle-pedido-estado').text(pedido.estado);
        $('#detalle-pedido-fecha').text(new Date(pedido.fecha_creacion).toLocaleString('es-ES'));
        $('#detalle-pedido-usuario').text(pedido.usuario_nombre);
        $('#detalle-pedido-sucursal').text(pedido.Nombre_Sucursal);
        $('#detalle-pedido-total').text(`$${parseFloat(pedido.total_estimado).toFixed(2)}`);
        $('#detalle-pedido-observaciones').text(pedido.observaciones || 'Sin observaciones');
        
        $('#detalle-productos-tbody').html(detallesHtml);
        $('#detalle-historial').html(historialHtml);

        $('#modalDetallePedido').modal('show');
    }

    // Confirmación mejorada para cambiar estado
    async confirmarCambiarEstado(pedidoId, nuevoEstado) {
        const estados = {
            'aprobado': {
                title: '¿Aprobar pedido?',
                text: '¿Estás seguro de que quieres aprobar este pedido?',
                icon: 'question',
                confirmButtonColor: '#28a745'
            },
            'rechazado': {
                title: '¿Rechazar pedido?',
                text: '¿Estás seguro de que quieres rechazar este pedido?',
                icon: 'warning',
                confirmButtonColor: '#dc3545'
            }
        };

        const config = estados[nuevoEstado] || {
            title: '¿Confirmar cambio?',
            text: '¿Estás seguro de que quieres cambiar el estado?',
            icon: 'question'
        };

        const result = await Swal.fire({
            title: config.title,
            text: config.text,
            icon: config.icon,
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: config.confirmButtonColor,
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        });

        // Solo ejecutar si se confirma
        if (result.isConfirmed) {
            await this.cambiarEstadoPedido(pedidoId, nuevoEstado);
        }
    }

    async cambiarEstadoPedido(pedidoId, nuevoEstado) {
        try {
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'cambiar_estado',
                pedido_id: pedidoId,
                nuevo_estado: nuevoEstado,
                comentario: `Estado cambiado a ${nuevoEstado}`
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

    // Confirmación mejorada para eliminar pedido
    async confirmarEliminarPedido(pedidoId) {
        const result = await Swal.fire({
            title: '¿Eliminar pedido?',
            text: 'Esta acción no se puede deshacer. ¿Estás seguro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        });

        // Solo ejecutar si se confirma
        if (result.isConfirmed) {
            await this.eliminarPedido(pedidoId);
        }
    }

    async eliminarPedido(pedidoId) {
        try {
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
        } catch (error) {
            this.mostrarError('Error de conexión');
        }
    }

    // Funciones del modal nuevo pedido
    abrirModalNuevoPedido() {
        $('#modalNuevoPedido').modal('show');
        this.pedidoEnProceso = true;
    }

    abrirModalListadoPedidos() {
        $('#modalListadoPedidos').modal('show');
        this.cargarPedidosEnModal();
    }

    async cargarPedidosEnModal() {
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
                this.renderizarPedidosEnModal(response.data || []);
            } else {
                this.mostrarError('Error al cargar pedidos: ' + (response.msg || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error en cargarPedidosEnModal:', error);
            this.mostrarError('Error de conexión al cargar pedidos');
        } finally {
            this.mostrarLoading(false);
        }
    }

    renderizarPedidosEnModal(pedidos) {
        const container = $('#lista-pedidos-modal');
        
        if (!pedidos || pedidos.length === 0) {
            container.html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <h4>No hay pedidos</h4>
                    <p>No se encontraron pedidos con los filtros aplicados</p>
                </div>
            `);
            return;
        }

        let html = '<div class="row">';
        pedidos.forEach(pedido => {
            html += this.crearHTMLPedido(pedido);
        });
        html += '</div>';
        
        container.html(html);
        this.setupPedidoEventListeners();
    }

    async buscarProductosSimple() {
        const query = $('#busqueda-producto-simple').val().trim();
        
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
                this.mostrarResultadosBusquedaSimple(response.data);
            } else {
                this.mostrarError('Error al buscar productos: ' + (response.msg || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error en buscarProductosSimple:', error);
            this.mostrarError('Error de conexión');
        }
    }

    // Confirmación para guardar pedido
    async confirmarGuardarPedido() {
        if (this.productosSeleccionados.length === 0) {
            this.mostrarError('Debes agregar al menos un producto al pedido');
            return;
        }

        const observaciones = $('#observaciones-pedido-simple').val().trim();
        const prioridad = $('#prioridad-pedido-simple').val();

        const result = await Swal.fire({
            title: '¿Confirmar pedido?',
            html: `
                <div class="text-left">
                    <p><strong>Productos:</strong> ${this.productosSeleccionados.length}</p>
                    <p><strong>Cantidad total:</strong> ${this.productosSeleccionados.reduce((sum, p) => sum + p.cantidad, 0)}</p>
                    <p><strong>Total estimado:</strong> $${this.productosSeleccionados.reduce((sum, p) => sum + (p.precio * p.cantidad), 0).toFixed(2)}</p>
                    <p><strong>Prioridad:</strong> ${prioridad}</p>
                    ${observaciones ? `<p><strong>Observaciones:</strong> ${observaciones}</p>` : ''}
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar pedido',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        });

        // Solo ejecutar si se confirma
        if (result.isConfirmed) {
            await this.guardarPedidoSimple(observaciones, prioridad);
        }
    }

    // Funciones para el modal nuevo pedido
    async confirmarGuardarPedidoNuevo() {
        const productos = this.productosModule ? this.productosModule.getProductosSeleccionados() : [];
        if (productos.length === 0) {
            this.mostrarError('Debes agregar al menos un producto al pedido');
            return;
        }

        const observaciones = $('#observaciones-pedido-nuevo').val().trim();
        const prioridad = $('#prioridad-pedido-nuevo').val();

        const result = await Swal.fire({
            title: '¿Confirmar pedido?',
            html: `
                <div class="text-left">
                    <p><strong>Productos:</strong> ${productos.length}</p>
                    <p><strong>Cantidad total:</strong> ${productos.reduce((sum, p) => sum + p.cantidad, 0)}</p>
                    <p><strong>Total estimado:</strong> $${productos.reduce((sum, p) => sum + (p.precio * p.cantidad), 0).toFixed(2)}</p>
                    <p><strong>Prioridad:</strong> ${prioridad}</p>
                    ${observaciones ? `<p><strong>Observaciones:</strong> ${observaciones}</p>` : ''}
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar pedido',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        });

        // Solo ejecutar si se confirma
        if (result.isConfirmed) {
            await this.guardarPedidoNuevo(observaciones, prioridad);
        }
    }

    async guardarPedidoNuevo(observaciones, prioridad) {
        try {
            const productos = this.productosModule ? this.productosModule.getProductosSeleccionados() : [];
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'crear_pedido',
                productos: JSON.stringify(productos),
                observaciones: observaciones,
                prioridad: prioridad
            });

            if (response.status === 'ok') {
                this.mostrarExito('Pedido creado exitosamente');
                $('#modalNuevoPedido').modal('hide');
                this.limpiarPedidoNuevo();
                this.cargarPedidos();
            } else {
                this.mostrarError('Error al crear pedido: ' + response.msg);
            }
        } catch (error) {
            console.error('Error en guardarPedidoNuevo:', error);
            this.mostrarError('Error de conexión');
        }
    }

    async confirmarLimpiarPedidoNuevo() {
        const productos = this.productosModule ? this.productosModule.getProductosSeleccionados() : [];
        if (productos.length === 0) {
            this.mostrarError('No hay productos para limpiar');
            return;
        }

        const result = await Swal.fire({
            title: '¿Limpiar pedido?',
            text: 'Se eliminarán todos los productos del pedido actual. ¿Estás seguro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        });

        // Solo ejecutar si se confirma
        if (result.isConfirmed) {
            this.limpiarPedidoNuevo();
        }
    }

    limpiarPedidoNuevo() {
        if (this.productosModule) {
            this.productosModule.limpiarPedido('nuevo');
        }
    }

    // Funciones para agregar desde stock bajo
    async mostrarProductosStockBajo() {
        if (this.productosModule) {
            this.productosModule.cargarProductosStockBajo();
        } else {
            console.error('Módulo de productos no disponible');
        }
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
                const stats = response.data || [];
                let pendientes = 0, aprobados = 0, proceso = 0, total = 0;

                stats.forEach(stat => {
                    switch(stat.estado) {
                        case 'pendiente':
                            pendientes = stat.total;
                            break;
                        case 'aprobado':
                            aprobados = stat.total;
                            break;
                        case 'proceso':
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
            $('#loading-overlay').show();
        } else {
            $('#loading-overlay').hide();
        }
    }

    mostrarExito(mensaje) {
        Swal.fire({
            title: '¡Éxito!',
            text: mensaje,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    mostrarError(mensaje) {
        Swal.fire({
            title: 'Error',
            text: mensaje,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }

    // Función para descargar Excel general
    async descargarExcel() {
        try {
            const btnDescargar = $('#btnDescargarExcel');
            const iconoOriginal = btnDescargar.html();
            
            // Mostrar estado de descarga
            btnDescargar.addClass('downloading');
            btnDescargar.html('<i class="fas fa-spinner fa-spin me-2"></i>Generando Excel...');
            btnDescargar.prop('disabled', true);
            
            // Obtener filtros actuales
            const filtros = {
                estado: $('#filtro-estado').val(),
                fecha_inicio: $('#filtro-fecha-inicio').val(),
                fecha_fin: $('#filtro-fecha-fin').val(),
                busqueda: $('#busqueda').val()
            };
            
            // Crear URL con parámetros
            const params = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    params.append(key, filtros[key]);
                }
            });
            
            const url = `api/exportar_pedidos_excel.php?${params.toString()}`;
            
            // Verificar si hay pedidos para exportar
            if (this.pedidos && this.pedidos.length === 0) {
                this.mostrarError('No hay pedidos para exportar. Ajusta los filtros e intenta nuevamente.');
                return;
            }
            
            // Crear enlace temporal para descarga
            const link = document.createElement('a');
            link.href = url;
            link.download = `pedidos_${new Date().toISOString().slice(0, 10)}.xlsx`;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Simular un pequeño delay para mostrar el estado de carga
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Ejecutar descarga
            link.click();
            document.body.removeChild(link);
            
            // Mostrar mensaje de éxito
            this.mostrarExito(`Archivo Excel generado correctamente (${this.pedidos ? this.pedidos.length : 0} pedidos)`);
            
        } catch (error) {
            console.error('Error al descargar Excel:', error);
            this.mostrarError('Error al generar el archivo Excel');
        } finally {
            // Restaurar botón
            const btnDescargar = $('#btnDescargarExcel');
            btnDescargar.removeClass('downloading');
            btnDescargar.html('<i class="fas fa-file-excel me-2"></i>Descargar Excel');
            btnDescargar.prop('disabled', false);
        }
    }

    // Función para descargar Excel de un pedido específico
    async descargarExcelPedido(pedidoId) {
        try {
            // Encontrar el botón específico y mostrar estado de carga
            const btnDescargar = $(`.descargar-excel-pedido[data-pedido-id="${pedidoId}"]`);
            const iconoOriginal = btnDescargar.html();
            
            // Mostrar estado de descarga
            btnDescargar.html('<i class="fas fa-spinner fa-spin"></i>');
            btnDescargar.prop('disabled', true);
            
            // Crear URL para descarga individual
            const url = `api/exportar_pedido_excel.php?pedido_id=${pedidoId}`;
            
            // Crear enlace temporal para descarga
            const link = document.createElement('a');
            link.href = url;
            link.download = `pedido_${pedidoId}_${new Date().toISOString().slice(0, 10)}.xlsx`;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Simular un pequeño delay para mostrar el estado de carga
            await new Promise(resolve => setTimeout(resolve, 300));
            
            // Ejecutar descarga
            link.click();
            document.body.removeChild(link);
            
            // Mostrar mensaje de éxito
            this.mostrarExito('Archivo Excel del pedido generado correctamente');
            
        } catch (error) {
            console.error('Error al descargar Excel del pedido:', error);
            this.mostrarError('Error al generar el archivo Excel del pedido');
        } finally {
            // Restaurar botón
            const btnDescargar = $(`.descargar-excel-pedido[data-pedido-id="${pedidoId}"]`);
            btnDescargar.html('<i class="fas fa-file-excel"></i>');
            btnDescargar.prop('disabled', false);
        }
    }

    // Función para descargar Excel general
    async descargarExcel() {
        try {
            const btnDescargar = $('#btnDescargarExcel');
            const iconoOriginal = btnDescargar.html();
            
            // Mostrar estado de descarga
            btnDescargar.addClass('downloading');
            btnDescargar.html('<i class="fas fa-spinner fa-spin me-2"></i>Generando Excel...');
            btnDescargar.prop('disabled', true);
            
            // Obtener filtros actuales
            const filtros = {
                estado: $('#filtro-estado').val(),
                fecha_inicio: $('#filtro-fecha-inicio').val(),
                fecha_fin: $('#filtro-fecha-fin').val(),
                busqueda: $('#busqueda').val()
            };
            
            // Crear URL con parámetros
            const params = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    params.append(key, filtros[key]);
                }
            });
            
            const url = `api/exportar_pedidos_excel.php?${params.toString()}`;
            
            // Verificar si hay pedidos para exportar
            if (this.pedidos && this.pedidos.length === 0) {
                this.mostrarError('No hay pedidos para exportar. Ajusta los filtros e intenta nuevamente.');
                return;
            }
            
            // Crear enlace temporal para descarga
            const link = document.createElement('a');
            link.href = url;
            link.download = `pedidos_${new Date().toISOString().slice(0, 10)}.xlsx`;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Simular un pequeño delay para mostrar el estado de carga
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Ejecutar descarga
            link.click();
            document.body.removeChild(link);
            
            // Mostrar mensaje de éxito
            this.mostrarExito(`Archivo Excel generado correctamente (${this.pedidos ? this.pedidos.length : 0} pedidos)`);
            
        } catch (error) {
            console.error('Error al descargar Excel:', error);
            this.mostrarError('Error al generar el archivo Excel');
        } finally {
            // Restaurar botón
            const btnDescargar = $('#btnDescargarExcel');
            btnDescargar.removeClass('downloading');
            btnDescargar.html('<i class="fas fa-file-excel me-2"></i>Descargar Excel');
            btnDescargar.prop('disabled', false);
        }
    }

    // Función para descargar Excel de un pedido específico
    async descargarExcelPedido(pedidoId) {
        try {
            // Encontrar el botón específico y mostrar estado de carga
            const btnDescargar = $(`.descargar-excel-pedido[data-pedido-id="${pedidoId}"]`);
            const iconoOriginal = btnDescargar.html();
            
            // Mostrar estado de descarga
            btnDescargar.html('<i class="fas fa-spinner fa-spin"></i>');
            btnDescargar.prop('disabled', true);
            
            // Crear URL para descarga individual
            const url = `api/exportar_pedido_excel.php?pedido_id=${pedidoId}`;
            
            // Crear enlace temporal para descarga
            const link = document.createElement('a');
            link.href = url;
            link.download = `pedido_${pedidoId}_${new Date().toISOString().slice(0, 10)}.xlsx`;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Simular un pequeño delay para mostrar el estado de carga
            await new Promise(resolve => setTimeout(resolve, 300));
            
            // Ejecutar descarga
            link.click();
            document.body.removeChild(link);
            
            // Mostrar mensaje de éxito
            this.mostrarExito('Archivo Excel del pedido generado correctamente');
            
        } catch (error) {
            console.error('Error al descargar Excel del pedido:', error);
            this.mostrarError('Error al generar el archivo Excel del pedido');
        } finally {
            // Restaurar botón
            const btnDescargar = $(`.descargar-excel-pedido[data-pedido-id="${pedidoId}"]`);
            btnDescargar.html('<i class="fas fa-file-excel"></i>');
            btnDescargar.prop('disabled', false);
        }
    }
}

// Inicializar cuando el DOM esté listo
$(document).ready(() => {
    window.sistemaPedidos = new SistemaPedidos();
});