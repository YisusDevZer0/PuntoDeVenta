// Sistema de Gestión de Pedidos - JavaScript Moderno
class SistemaPedidos {
    constructor() {
        this.pedidos = [];
        this.productosSeleccionados = [];
        this.sortable = null;
        this.modalPersistenteAbierto = false;
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
        try {
            const datosGuardados = localStorage.getItem('pedidos_productos_seleccionados');
            if (datosGuardados) {
                this.productosSeleccionados = JSON.parse(datosGuardados);
                if (this.modalPersistenteAbierto) {
                    this.actualizarVistaProductosPersistente();
                    this.actualizarResumenPersistente();
                }
            }
        } catch (error) {
            console.log('No hay datos guardados o error al cargar');
        }
    }

    // Guardar datos en localStorage
    guardarDatos() {
        try {
            localStorage.setItem('pedidos_productos_seleccionados', JSON.stringify(this.productosSeleccionados));
        } catch (error) {
            console.log('Error al guardar datos');
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
        $('#btnNuevoPedido').on('click', () => this.abrirModalPersistente());
        $('#btnRefresh').on('click', () => this.cargarPedidos());
        $('#btnStockBajo').on('click', () => this.mostrarProductosStockBajo());
        $('#btnFiltrar').on('click', () => this.aplicarFiltros());
        $('#btnLimpiar').on('click', () => this.limpiarFiltros());
        $('#btnCrearPrimerPedido').on('click', () => this.abrirModalPersistente());

        // Búsqueda en tiempo real
        let timeoutBusqueda;
        $('#busqueda').on('input', (e) => {
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(() => {
                this.aplicarFiltros();
            }, 500);
        });

        // Eventos del modal persistente
        $('#btnBuscarProductoPersistente').on('click', () => this.buscarProductosPersistente());
        $('#btnBuscarEncargosPersistente').on('click', () => this.buscarEncargosPersistente());
        $('#btnGuardarPedidoPersistente').on('click', () => this.guardarPedidoPersistente());
        $('#btnLimpiarPedido').on('click', () => this.limpiarPedidoPersistente());
        
        $('#busqueda-producto-persistente').on('keypress', (e) => {
            if (e.which === 13) this.buscarProductosPersistente();
        });

        // Eventos del modal persistente
        $('#modalPedidoPersistente').on('shown.bs.modal', () => {
            this.modalPersistenteAbierto = true;
            this.cargarDatosGuardados();
        });

        $('#modalPedidoPersistente').on('hidden.bs.modal', () => {
            this.modalPersistenteAbierto = false;
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
                this.abrirModalPersistente();
            }
            
            // Ctrl/Cmd + R para refrescar
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                this.cargarPedidos();
            }
        });

        // Tooltips para mejor UX
        $('[data-toggle="tooltip"]').tooltip();
    }

    setupSortable() {
        // Configurar drag & drop para productos del pedido persistente
        this.sortable = new Sortable(document.getElementById('productos-pedido-persistente'), {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: () => {
                this.actualizarResumenPersistente();
                this.guardarDatos();
            },
            onStart: () => {
                $('.sortable-ghost').addClass('shadow-lg');
            }
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
            <div class="pedido-item ${prioridadClass}" data-pedido-id="${pedido.id}">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <strong>${pedido.folio}</strong>
                        <br>
                        <small class="text-muted">${fecha}</small>
                        <br>
                        <small class="text-info">${tiempoTranscurrido}</small>
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
                            <button class="btn btn-outline-primary btn-sm ver-detalle" data-pedido-id="${pedido.id}" 
                                    data-toggle="tooltip" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
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

    async cambiarEstadoPedido(pedidoId, nuevoEstado) {
        const estados = {
            'aprobado': '¿Estás seguro de que quieres aprobar este pedido?',
            'rechazado': '¿Estás seguro de que quieres rechazar este pedido?',
            'completado': '¿Estás seguro de que quieres marcar como completado este pedido?'
        };

        const confirmacion = await Swal.fire({
            title: 'Confirmar acción',
            text: estados[nuevoEstado] || '¿Estás seguro?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmacion.isConfirmed) return;

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

    async eliminarPedido(pedidoId) {
        const confirmacion = await Swal.fire({
            title: '¿Eliminar pedido?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        });

        if (!confirmacion.isConfirmed) return;

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

    // Funciones del modal persistente
    abrirModalPersistente() {
        $('#modalPedidoPersistente').modal('show');
        this.cargarDatosGuardados();
    }

    async buscarProductosPersistente() {
        const query = $('#busqueda-producto-persistente').val().trim();
        
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
                this.mostrarResultadosBusquedaPersistente(response.data);
            } else {
                this.mostrarError('Error al buscar productos: ' + (response.msg || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error en buscarProductosPersistente:', error);
            this.mostrarError('Error de conexión');
        }
    }

    async buscarEncargosPersistente() {
        const query = $('#busqueda-producto-persistente').val().trim();
        
        if (query.length < 2) {
            this.mostrarError('Ingresa al menos 2 caracteres para buscar encargos');
            return;
        }

        try {
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'obtener_encargos',
                busqueda: query
            });

            if (response.status === 'ok') {
                this.mostrarResultadosEncargosPersistente(response.data);
            } else {
                this.mostrarError('Error al buscar encargos: ' + (response.msg || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error en buscarEncargosPersistente:', error);
            this.mostrarError('Error de conexión');
        }
    }

    mostrarResultadosBusquedaPersistente(productos) {
        const container = $('#resultados-busqueda-persistente');
        
        if (!productos || productos.length === 0) {
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
                                    '<button class="btn btn-primary btn-sm agregar-producto-persistente">Agregar</button>'
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
        $('.agregar-producto-persistente').on('click', (e) => {
            const card = $(e.currentTarget).closest('.producto-card');
            const producto = JSON.parse(card.data('producto'));
            this.agregarProductoAPedidoPersistente(producto);
        });
    }

    mostrarResultadosEncargosPersistente(encargos) {
        const container = $('#resultados-busqueda-persistente');
        
        if (!encargos || encargos.length === 0) {
            container.html('<p class="text-muted">No se encontraron encargos previos</p>');
            return;
        }

        let html = '<h6>Encargos previos (más solicitados):</h6><div class="row">';
        
        encargos.forEach(encargo => {
            const yaSeleccionado = this.productosSeleccionados.some(p => p.id === encargo.ID_Prod_POS);
            const stockClass = encargo.Existencias_R < encargo.Min_Existencia ? 'text-danger' : 'text-success';
            
            html += `
                <div class="col-md-6 mb-2">
                    <div class="producto-card ${yaSeleccionado ? 'border-success' : ''}" 
                         data-producto='${JSON.stringify(encargo)}'>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${encargo.Nombre_Prod}</h6>
                                <p class="mb-1 small text-muted">
                                    Código: ${encargo.Cod_Barra || 'N/A'} | 
                                    Clave: ${encargo.Clave_adicional || 'N/A'}
                                </p>
                                <p class="mb-1 small">
                                    Stock: <span class="${stockClass}">${encargo.Existencias_R}</span> | 
                                    Mín: ${encargo.Min_Existencia} | 
                                    Máx: ${encargo.Max_Existencia}
                                </p>
                                <p class="mb-1 small">
                                    <strong>Precio: $${parseFloat(encargo.Precio_Venta || 0).toFixed(2)}</strong>
                                </p>
                                <p class="mb-0 small text-info">
                                    <i class="fas fa-history"></i> 
                                    Solicitado ${encargo.veces_solicitado} veces | 
                                    Promedio: ${Math.round(encargo.cantidad_promedio)} unidades
                                </p>
                            </div>
                            <div class="ms-2">
                                ${yaSeleccionado ? 
                                    '<span class="badge bg-success">Agregado</span>' : 
                                    '<button class="btn btn-info btn-sm agregar-producto-persistente">Agregar</button>'
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
        $('.agregar-producto-persistente').on('click', (e) => {
            const card = $(e.currentTarget).closest('.producto-card');
            const producto = JSON.parse(card.data('producto'));
            this.agregarProductoAPedidoPersistente(producto);
        });
    }

    agregarProductoAPedidoPersistente(producto) {
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
        this.actualizarVistaProductosPersistente();
        this.actualizarResumenPersistente();
        this.guardarDatos();
        
        // Mostrar notificación
        this.mostrarExito('Producto agregado al pedido');
    }

    actualizarVistaProductosPersistente() {
        const container = $('#productos-pedido-persistente');
        
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
                                   onchange="sistemaPedidos.actualizarCantidadPersistente(${index}, this.value)">
                            <span class="me-2">$${producto.precio.toFixed(2)}</span>
                            <button class="btn btn-danger btn-sm" onclick="sistemaPedidos.eliminarProductoPersistente(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
    }

    actualizarCantidadPersistente(index, cantidad) {
        if (this.productosSeleccionados[index]) {
            this.productosSeleccionados[index].cantidad = parseInt(cantidad) || 1;
            this.actualizarResumenPersistente();
            this.guardarDatos();
        }
    }

    eliminarProductoPersistente(index) {
        this.productosSeleccionados.splice(index, 1);
        this.actualizarVistaProductosPersistente();
        this.actualizarResumenPersistente();
        this.guardarDatos();
    }

    actualizarResumenPersistente() {
        const totalProductos = this.productosSeleccionados.length;
        const totalCantidad = this.productosSeleccionados.reduce((sum, p) => sum + p.cantidad, 0);
        const totalPrecio = this.productosSeleccionados.reduce((sum, p) => sum + (p.precio * p.cantidad), 0);

        $('#total-productos-persistente').text(totalProductos);
        $('#total-cantidad-persistente').text(totalCantidad);
        $('#total-precio-persistente').text(`$${totalPrecio.toFixed(2)}`);
    }

    async guardarPedidoPersistente() {
        if (this.productosSeleccionados.length === 0) {
            this.mostrarError('Debes agregar al menos un producto al pedido');
            return;
        }

        const observaciones = $('#observaciones-pedido-persistente').val().trim();
        const prioridad = $('#prioridad-pedido-persistente').val();

        try {
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'crear_pedido',
                productos: JSON.stringify(this.productosSeleccionados),
                observaciones: observaciones,
                prioridad: prioridad
            });

            if (response.status === 'ok') {
                this.mostrarExito('Pedido creado exitosamente');
                $('#modalPedidoPersistente').modal('hide');
                this.limpiarPedidoPersistente();
                this.cargarPedidos();
            } else {
                this.mostrarError('Error al crear pedido: ' + response.msg);
            }
        } catch (error) {
            console.error('Error en guardarPedidoPersistente:', error);
            this.mostrarError('Error de conexión');
        }
    }

    limpiarPedidoPersistente() {
        $('#busqueda-producto-persistente').val('');
        $('#resultados-busqueda-persistente').html(`
            <p class="text-muted text-center">
                <i class="fas fa-search fa-2x mb-2"></i><br>
                Busca productos para agregar al pedido
            </p>
        `);
        $('#observaciones-pedido-persistente').val('');
        $('#prioridad-pedido-persistente').val('normal');
        this.productosSeleccionados = [];
        this.actualizarVistaProductosPersistente();
        this.actualizarResumenPersistente();
        this.guardarDatos();
    }

    // Funciones para agregar desde stock bajo
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
        if (!productos || productos.length === 0) {
            this.mostrarError('No hay productos con stock bajo');
            return;
        }

        let html = '<div class="row">';
        productos.forEach(producto => {
            const yaSeleccionado = this.productosSeleccionados.some(p => p.id === producto.ID_Prod_POS);
            
            html += `
                <div class="col-md-6 mb-2">
                    <div class="producto-card ${yaSeleccionado ? 'border-success' : 'border-warning'}" 
                         data-producto='${JSON.stringify(producto)}'>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${producto.Nombre_Prod}</h6>
                                <p class="mb-1 small text-muted">
                                    Código: ${producto.Cod_Barra || 'N/A'}
                                </p>
                                <p class="mb-1 small text-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Stock: ${producto.Existencias_R} | Mín: ${producto.Min_Existencia}
                                </p>
                                <p class="mb-0 small">
                                    <strong>Precio: $${parseFloat(producto.Precio_Venta || 0).toFixed(2)}</strong>
                                </p>
                            </div>
                            <div class="ms-2">
                                ${yaSeleccionado ? 
                                    '<span class="badge bg-success">Agregado</span>' : 
                                    '<button class="btn btn-warning btn-sm agregar-stock-bajo">Agregar</button>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        $('#stock-bajo-content').html(html);

        // Event listeners para agregar productos
        $('.agregar-stock-bajo').on('click', (e) => {
            const card = $(e.currentTarget).closest('.producto-card');
            const producto = JSON.parse(card.data('producto'));
            this.agregarProductoStockBajo(producto);
        });

        $('#modalStockBajo').modal('show');
    }

    agregarProductoStockBajo(producto) {
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

        // Actualizar la vista del modal persistente si está abierto
        if (this.modalPersistenteAbierto) {
            this.actualizarVistaProductosPersistente();
            this.actualizarResumenPersistente();
        }
        this.guardarDatos();
        
        // Mostrar notificación
        this.mostrarExito('Producto agregado al pedido');
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
}

// Inicializar cuando el DOM esté listo
$(document).ready(() => {
    window.sistemaPedidos = new SistemaPedidos();
}); 