// Sistema de Gestión de Pedidos Administrativos
class PedidosAdministrativos {
    constructor() {
        this.pedidos = [];
        this.productosSeleccionados = [];
        this.encargosDisponibles = [];
        this.productosStockBajo = [];
        this.paginaActual = 1;
        this.productosPorPagina = 10;
        this.pedidoEnProceso = false;
        this.sortable = null;
        
        // Usar el módulo de productos existente
        this.productosModule = window.productosModule;
        
        this.init();
    }

    init() {
        this.cargarDatosGuardados();
        this.setupEventListeners();
        this.cargarEstadisticas();
        this.cargarPedidos();
        this.actualizarIndicadorCarrito();
    }

    // Configurar event listeners
    setupEventListeners() {
        // Búsqueda de productos con botón y Enter
        $('#btnBuscarProductoNuevo').on('click', () => {
            const query = $('#busqueda-producto-nuevo').val().trim();
            if (query) {
                this.buscarProductos(query);
            } else {
                this.buscarProductos(''); // Mostrar productos populares
            }
        });

        // Búsqueda al presionar Enter
        $('#busqueda-producto-nuevo').on('keypress', (e) => {
            if (e.which === 13) {
                const query = $('#busqueda-producto-nuevo').val().trim();
                if (query) {
                    this.buscarProductos(query);
                } else {
                    this.buscarProductos(''); // Mostrar productos populares
                }
            }
        });

        // Búsqueda de encargos
        $('#btnBuscarEncargosNuevo').on('click', () => {
            $('#modalEncargos').modal('show');
        });

        // Eventos del modal nuevo pedido
        $('#modalNuevoPedido').on('shown.bs.modal', () => {
            this.pedidoEnProceso = true;
            this.cargarDatosGuardados();
            this.actualizarVistaProductos();
            this.actualizarResumen();
            // Cargar productos populares al abrir
            this.buscarProductos('');
        });

        $('#modalNuevoPedido').on('hidden.bs.modal', () => {
            this.pedidoEnProceso = false;
        });

        // Eventos del modal stock bajo
        $('#modalStockBajo').on('shown.bs.modal', () => {
            this.cargarProductosStockBajo();
        });

        // Eventos del modal encargos
        $('#modalEncargos').on('shown.bs.modal', () => {
            this.cargarEncargosDisponibles();
        });

        // Filtros
        $('#filtro-estado, #filtro-fecha-inicio, #filtro-fecha-fin').on('change', () => {
            this.aplicarFiltros();
        });

        // Búsqueda de pedidos
        $('#busqueda-pedidos').on('input', () => {
            this.aplicarFiltros();
        });

        // Eventos de confirmación
        $('#btn-aprobar-pedido').on('click', () => {
            this.aprobarPedido();
        });

        $('#btn-cancelar-pedido').on('click', () => {
            this.cancelarPedido();
        });
    }

    // Cargar datos guardados en localStorage
    cargarDatosGuardados() {
        const datos = localStorage.getItem('productosSeleccionados');
        if (datos) {
            this.productosSeleccionados = JSON.parse(datos);
        }
    }

    // Guardar datos en localStorage
    guardarDatos() {
        localStorage.setItem('productosSeleccionados', JSON.stringify(this.productosSeleccionados));
        this.actualizarIndicadorCarrito();
    }

    // Actualizar indicador del carrito
    actualizarIndicadorCarrito() {
        const badge = $('#carrito-badge');
        if (this.productosSeleccionados.length > 0) {
            badge.text(this.productosSeleccionados.length).show();
        } else {
            badge.hide();
        }
    }

    // Buscar productos
    buscarProductos(query) {
        this.mostrarLoading('Buscando productos...');
        
        $.ajax({
            url: 'test_buscador.php', // Cambiar a 'api/buscar_productos.php' cuando funcione
            method: 'POST',
            data: { query: query },
            success: (response) => {
                this.ocultarLoading();
                if (response.success) {
                    this.mostrarResultadosBusqueda(response.productos);
                } else {
                    this.mostrarError(response.message || 'Error en la búsqueda');
                }
            },
            error: (xhr, status, error) => {
                this.ocultarLoading();
                console.error('Error en búsqueda:', error);
                this.mostrarError('Error de conexión en la búsqueda');
            }
        });
    }

    // Mostrar resultados de búsqueda
    mostrarResultadosBusqueda(productos) {
        const container = $('#resultados-busqueda-nuevo');
        
        if (!productos || productos.length === 0) {
            container.html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <p>No se encontraron productos</p>
                    <small>Intenta con otros términos de búsqueda</small>
                </div>
            `);
            return;
        }

        let html = '<div class="row">';
        productos.forEach(producto => {
            const yaSeleccionado = this.productosSeleccionados.some(p => p.id === producto.ID_Prod_POS);
            const stockClass = this.getStockClass(producto.stock_status);
            const stockIcon = this.getStockIcon(producto.stock_status);
            
            html += `
                <div class="col-md-6 mb-2">
                    <div class="producto-card ${yaSeleccionado ? 'border-success' : 'border-warning'}" 
                         data-producto='${JSON.stringify(producto).replace(/'/g, "&apos;")}'>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${producto.Nombre_Prod}</h6>
                                <p class="mb-1 small text-muted">
                                    Código: ${producto.Cod_Barra || 'N/A'}
                                </p>
                                <p class="mb-1 small ${stockClass}">
                                    ${stockIcon} Stock: ${producto.Existencias_R} | Mín: ${producto.Min_Existencia}
                                </p>
                                <p class="mb-0 small">
                                    <strong>Precio: $${parseFloat(producto.Precio_Venta || 0).toFixed(2)}</strong>
                                </p>
                            </div>
                            <div class="ms-2">
                                ${yaSeleccionado ? 
                                    '<span class="badge bg-success">Agregado</span>' : 
                                    `<button class="btn btn-primary btn-sm agregar-producto">Agregar</button>`
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
            const producto = card.data('producto');
            this.agregarProducto(producto);
        });
    }

    // Agregar producto al pedido
    agregarProducto(producto) {
        if (this.productosSeleccionados.some(p => p.id === producto.ID_Prod_POS)) {
            this.mostrarError('Este producto ya está en el pedido');
            return;
        }

        this.productosSeleccionados.push({
            id: producto.ID_Prod_POS,
            nombre: producto.Nombre_Prod,
            codigo: producto.Cod_Barra,
            precio: parseFloat(producto.Precio_Venta || 0),
            cantidad: 1,
            stock: producto.Existencias_R,
            min_stock: producto.Min_Existencia
        });

        this.actualizarVistaProductos();
        this.actualizarResumen();
        this.guardarDatos();
        this.mostrarExito('Producto agregado al pedido');
    }

    // Actualizar vista de productos
    actualizarVistaProductos() {
        const container = $('#productos-pedido-nuevo');
        
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
                                   onchange="pedidosAdmin.actualizarCantidad(${index}, this.value)">
                            <span class="me-2">$${producto.precio.toFixed(2)}</span>
                            <button class="btn btn-danger btn-sm" onclick="pedidosAdmin.eliminarProducto(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.html(html);
        this.inicializarSortable();
    }

    // Actualizar cantidad de producto
    actualizarCantidad(index, cantidad) {
        if (this.productosSeleccionados[index]) {
            this.productosSeleccionados[index].cantidad = parseInt(cantidad) || 1;
            this.actualizarResumen();
            this.guardarDatos();
        }
    }

    // Eliminar producto
    eliminarProducto(index) {
        this.productosSeleccionados.splice(index, 1);
        this.actualizarVistaProductos();
        this.actualizarResumen();
        this.guardarDatos();
        this.mostrarExito('Producto eliminado del pedido');
    }

    // Actualizar resumen
    actualizarResumen() {
        const total = this.productosSeleccionados.reduce((sum, producto) => {
            return sum + (producto.precio * producto.cantidad);
        }, 0);
        
        $('#total-pedido-nuevo').text(`$${total.toFixed(2)}`);
    }

    // Inicializar Sortable
    inicializarSortable() {
        const container = document.getElementById('productos-pedido-nuevo');
        if (container && typeof Sortable !== 'undefined') {
            if (this.sortable) {
                this.sortable.destroy();
            }
            
            this.sortable = new Sortable(container, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: (evt) => {
                    // Reordenar array según el nuevo orden
                    const newOrder = [];
                    $(container).find('.producto-card').each((index, element) => {
                        const dataIndex = $(element).data('index');
                        newOrder.push(this.productosSeleccionados[dataIndex]);
                    });
                    this.productosSeleccionados = newOrder;
                    this.guardarDatos();
                }
            });
        }
    }

    // Cargar productos con bajo stock
    cargarProductosStockBajo() {
        this.mostrarLoading('Cargando productos con bajo stock...');
        
        $.ajax({
            url: 'api/productos_stock_bajo.php',
            method: 'GET',
            success: (response) => {
                this.ocultarLoading();
                if (response.success) {
                    this.productosStockBajo = response.productos;
                    this.mostrarProductosStockBajo();
                    
                    // Mostrar información de debugging si está disponible
                    if (response.debug) {
                        console.log('Debug info:', response.debug);
                        if (response.debug.con_bajo_stock === 0) {
                            this.mostrarError(`No se encontraron productos con bajo stock. 
                                Total productos: ${response.debug.total_productos}, 
                                Sin existencias: ${response.debug.sin_existencias}, 
                                Sin mínimo: ${response.debug.sin_min_existencia}`);
                        }
                    }
                } else {
                    this.mostrarError(response.message || 'Error al cargar productos con bajo stock');
                }
            },
            error: () => {
                this.ocultarLoading();
                this.mostrarError('Error de conexión al cargar productos con bajo stock');
            }
        });
    }

    // Mostrar productos con bajo stock
    mostrarProductosStockBajo() {
        const container = $('#productos-stock-bajo');
        
        if (!this.productosStockBajo || this.productosStockBajo.length === 0) {
            container.html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
                    <p>No hay productos con bajo stock</p>
                    <small>Esto puede significar que:</small>
                    <ul class="list-unstyled mt-2">
                        <li><small>• Todos los productos tienen stock suficiente</small></li>
                        <li><small>• Los productos no tienen configurado un mínimo de existencia</small></li>
                        <li><small>• No hay productos activos en el sistema</small></li>
                    </ul>
                </div>
            `);
            return;
        }

        const totalPaginas = Math.ceil(this.productosStockBajo.length / this.productosPorPagina);
        const inicio = (this.paginaActual - 1) * this.productosPorPagina;
        const fin = inicio + this.productosPorPagina;
        const productosPagina = this.productosStockBajo.slice(inicio, fin);

        let html = '<div class="row">';
        productosPagina.forEach(producto => {
            const yaSeleccionado = this.productosSeleccionados.some(p => p.id === producto.ID_Prod_POS);
            const deficit = producto.Min_Existencia - producto.Existencias_R;
            
            html += `
                <div class="col-md-6 mb-2">
                    <div class="producto-card ${yaSeleccionado ? 'border-success' : 'border-warning'}" 
                         data-producto='${JSON.stringify(producto).replace(/'/g, "&apos;")}'>
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
                                <p class="mb-1 small text-info">
                                    <i class="fas fa-info-circle"></i>
                                    Déficit: ${deficit} unidades
                                </p>
                                <p class="mb-0 small">
                                    <strong>Precio: $${parseFloat(producto.Precio_Venta || 0).toFixed(2)}</strong>
                                </p>
                            </div>
                            <div class="ms-2">
                                ${yaSeleccionado ? 
                                    '<span class="badge bg-success">Agregado</span>' : 
                                    `<button class="btn btn-warning btn-sm agregar-stock-bajo">Agregar</button>`
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        container.html(html);
        this.generarPaginacionStockBajo(totalPaginas);

        // Event listeners para agregar productos desde stock bajo
        $('.agregar-stock-bajo').on('click', (e) => {
            const card = $(e.currentTarget).closest('.producto-card');
            const producto = card.data('producto');
            this.agregarProductoStockBajo(producto);
        });
    }

    // Generar paginación para stock bajo
    generarPaginacionStockBajo(totalPaginas) {
        const container = $('#paginacion-stock-bajo');
        let html = '';
        
        if (totalPaginas > 1) {
            html += `<li class="page-item ${this.paginaActual === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="pedidosAdmin.cambiarPaginaStockBajo(${this.paginaActual - 1})">Anterior</a>
                     </li>`;
            
            for (let i = 1; i <= totalPaginas; i++) {
                html += `<li class="page-item ${this.paginaActual === i ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="pedidosAdmin.cambiarPaginaStockBajo(${i})">${i}</a>
                         </li>`;
            }
            
            html += `<li class="page-item ${this.paginaActual === totalPaginas ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="pedidosAdmin.cambiarPaginaStockBajo(${this.paginaActual + 1})">Siguiente</a>
                     </li>`;
        }
        
        container.html(html);
    }

    // Cambiar página de stock bajo
    cambiarPaginaStockBajo(pagina) {
        if (pagina >= 1 && pagina <= Math.ceil(this.productosStockBajo.length / this.productosPorPagina)) {
            this.paginaActual = pagina;
            this.mostrarProductosStockBajo();
        }
    }

    // Agregar producto desde stock bajo
    agregarProductoStockBajo(producto) {
        if (this.productosSeleccionados.some(p => p.id === producto.ID_Prod_POS)) {
            this.mostrarError('Este producto ya está en el pedido');
            return;
        }

        this.productosSeleccionados.push({
            id: producto.ID_Prod_POS,
            nombre: producto.Nombre_Prod,
            codigo: producto.Cod_Barra,
            precio: parseFloat(producto.Precio_Venta || 0),
            cantidad: 1,
            stock: producto.Existencias_R,
            min_stock: producto.Min_Existencia
        });

        // Si el modal nuevo no está abierto, abrirlo
        if (!$('#modalNuevoPedido').hasClass('show')) {
            $('#modalNuevoPedido').modal('show');
        }

        this.actualizarVistaProductos();
        this.actualizarResumen();
        this.guardarDatos();
        this.mostrarExito('Producto agregado al pedido');
        
        // Cerrar el modal de stock bajo
        $('#modalStockBajo').modal('hide');
    }

    // Agregar todos los productos de stock bajo
    agregarTodosStockBajo() {
        let agregados = 0;
        this.productosStockBajo.forEach(producto => {
            if (!this.productosSeleccionados.some(p => p.id === producto.ID_Prod_POS)) {
                this.productosSeleccionados.push({
                    id: producto.ID_Prod_POS,
                    nombre: producto.Nombre_Prod,
                    codigo: producto.Cod_Barra,
                    precio: parseFloat(producto.Precio_Venta || 0),
                    cantidad: 1,
                    stock: producto.Existencias_R,
                    min_stock: producto.Min_Existencia
                });
                agregados++;
            }
        });

        if (agregados > 0) {
            this.actualizarVistaProductos();
            this.actualizarResumen();
            this.guardarDatos();
            this.mostrarExito(`${agregados} productos agregados al pedido`);
            
            // Abrir modal nuevo pedido si no está abierto
            if (!$('#modalNuevoPedido').hasClass('show')) {
                $('#modalNuevoPedido').modal('show');
            }
        } else {
            this.mostrarError('No hay productos nuevos para agregar');
        }
    }

    // Cargar encargos disponibles
    cargarEncargosDisponibles() {
        this.mostrarLoading('Cargando encargos disponibles...');
        
        $.ajax({
            url: 'api/encargos_disponibles.php',
            method: 'GET',
            success: (response) => {
                this.ocultarLoading();
                if (response.success) {
                    this.encargosDisponibles = response.encargos;
                    this.mostrarEncargosDisponibles(response.encargos);
                } else {
                    this.mostrarError(response.message || 'Error al cargar encargos');
                }
            },
            error: () => {
                this.ocultarLoading();
                this.mostrarError('Error de conexión al cargar encargos');
            }
        });
    }

    // Mostrar encargos disponibles
    mostrarEncargosDisponibles(encargos) {
        this.encargosDisponibles = encargos;
        const container = $('#encargos-disponibles');
        
        if (!this.encargosDisponibles || this.encargosDisponibles.length === 0) {
            container.html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-clipboard-list fa-2x mb-2 text-info"></i>
                    <p>No hay encargos disponibles</p>
                    <small>No se encontraron encargos pendientes en los últimos 30 días</small>
                </div>
            `);
            return;
        }

        let html = '<div class="row">';
        this.encargosDisponibles.forEach(encargo => {
            const saldoPendiente = parseFloat(encargo.precio) - parseFloat(encargo.abono_parcial || 0);
            const fechaFormateada = new Date(encargo.fecha).toLocaleDateString('es-ES');
            const yaSeleccionado = this.productosSeleccionados.some(p => p.es_encargo && p.encargo_id === encargo.id);
            
            // Determinar color del estado
            const estadoClass = encargo.estado === 'pendiente' ? 'bg-warning' : 'bg-success';
            const estadoIcon = encargo.estado === 'pendiente' ? 'fa-clock' : 'fa-check';
            
            // Determinar color del saldo
            const saldoClass = saldoPendiente > 0 ? 'text-danger' : 'text-success';
            const saldoIcon = saldoPendiente > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle';
            
            html += `
                <div class="col-md-6 mb-3">
                    <div class="encargo-card ${yaSeleccionado ? 'border-success' : 'border-info'}" 
                         data-encargo='${JSON.stringify(encargo).replace(/'/g, "&apos;")}'>
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-pills text-primary me-2"></i>
                                    ${encargo.descripcion || 'Medicamento sin especificar'}
                                </h6>
                                <span class="badge ${estadoClass}">
                                    <i class="fas ${estadoIcon} me-1"></i>
                                    ${encargo.estado || 'Pendiente'}
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1 small">
                                            <i class="fas fa-user text-info me-1"></i>
                                            <strong>Paciente:</strong>
                                        </p>
                                        <p class="mb-2 small text-muted">${encargo.cliente || 'N/A'}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1 small">
                                            <i class="fas fa-hashtag text-secondary me-1"></i>
                                            <strong>Cantidad:</strong>
                                        </p>
                                        <p class="mb-2 small text-muted">${encargo.cantidad || 1}</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1 small">
                                            <i class="fas fa-calendar text-warning me-1"></i>
                                            <strong>Fecha:</strong>
                                        </p>
                                        <p class="mb-2 small text-muted">${fechaFormateada}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1 small">
                                            <i class="fas fa-dollar-sign text-success me-1"></i>
                                            <strong>Precio:</strong>
                                        </p>
                                        <p class="mb-2 small text-muted">$${parseFloat(encargo.precio || 0).toFixed(2)}</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1 small">
                                            <i class="fas fa-ticket-alt text-info me-1"></i>
                                            <strong>Ticket:</strong>
                                        </p>
                                        <p class="mb-2 small text-muted">${encargo.num_ticket || 'N/A'}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1 small">
                                            <i class="fas fa-user-md text-primary me-1"></i>
                                            <strong>Empleado:</strong>
                                        </p>
                                        <p class="mb-2 small text-muted">${encargo.empleado || 'N/A'}</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-1 small">
                                            <i class="fas ${saldoIcon} ${saldoClass} me-1"></i>
                                            <strong>Saldo Pendiente:</strong>
                                        </p>
                                        <p class="mb-2 small ${saldoClass}">$${saldoPendiente.toFixed(2)}</p>
                                    </div>
                                </div>
                                
                                ${encargo.observaciones ? `
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="mb-1 small">
                                                <i class="fas fa-sticky-note text-muted me-1"></i>
                                                <strong>Observaciones:</strong>
                                            </p>
                                            <p class="mb-2 small text-muted">${encargo.observaciones}</p>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                            <div class="card-footer text-center">
                                ${yaSeleccionado ? 
                                    '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Agregado</span>' : 
                                    `<button class="btn btn-info btn-sm agregar-encargo">
                                        <i class="fas fa-plus me-1"></i>Agregar
                                    </button>`
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        container.html(html);

        // Event listeners para agregar encargos
        $('.agregar-encargo').on('click', (e) => {
            const card = $(e.currentTarget).closest('.encargo-card');
            const encargo = card.data('encargo');
            this.agregarEncargo(encargo.id);
        });
    }

    // Agregar encargo al pedido
    agregarEncargo(encargoId) {
        const encargo = this.encargosDisponibles.find(e => e.id === encargoId);
        if (!encargo) {
            this.mostrarError('Encargo no encontrado');
            return;
        }
        
        const productoEspecial = {
            id: `encargo_${encargo.id}`,
            nombre: encargo.descripcion || 'Medicamento especial',
            codigo: `ENC-${encargo.id}`,
            precio: parseFloat(encargo.precio || 0),
            cantidad: parseInt(encargo.cantidad || 1),
            stock: 0,
            min_stock: 0,
            es_encargo: true,
            encargo_id: encargo.id,
            cliente: encargo.cliente,
            observaciones: `Paciente: ${encargo.cliente} | Ticket: ${encargo.num_ticket} | Empleado: ${encargo.empleado}`,
            saldo_pendiente: parseFloat(encargo.precio || 0) - parseFloat(encargo.abono_parcial || 0)
        };

        // Verificar si ya está agregado
        const yaExiste = this.productosSeleccionados.some(p => p.es_encargo && p.encargo_id === encargo.id);
        if (yaExiste) {
            this.mostrarError('Este encargo ya está agregado al pedido');
            return;
        }

        this.productosSeleccionados.push(productoEspecial);
        this.guardarDatos();
        this.actualizarVistaProductos();
        this.actualizarResumen();
        
        // Actualizar la vista de encargos
        this.mostrarEncargosDisponibles(this.encargosDisponibles);
        
        this.mostrarExito('Encargo agregado al pedido');
    }

    // Abrir resumen de pedido
    abrirResumenPedido() {
        if (this.productosSeleccionados.length === 0) {
            this.mostrarError('No hay productos en el pedido');
            return;
        }

        this.mostrarResumenPedido();
        $('#modalResumenPedido').modal('show');
    }

    // Mostrar resumen de pedido
    mostrarResumenPedido() {
        const container = $('#resumen-productos');
        
        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
        
        let total = 0;
        this.productosSeleccionados.forEach((producto, index) => {
            const subtotal = producto.precio * producto.cantidad;
            total += subtotal;
            
            const esEncargo = producto.es_encargo ? ' <span class="badge bg-info">Encargo</span>' : '';
            const infoAdicional = producto.es_encargo ? 
                `<br><small class="text-muted">Paciente: ${producto.cliente || 'N/A'}</small>` : '';
            
            html += `
                <tr>
                    <td>
                        ${producto.nombre}${esEncargo}${infoAdicional}
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm" 
                               style="width: 80px;" min="1" value="${producto.cantidad}"
                               onchange="pedidosAdmin.actualizarCantidadResumen(${index}, this.value)">
                    </td>
                    <td>$${producto.precio.toFixed(2)}</td>
                    <td>$${subtotal.toFixed(2)}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        html += `<div class="text-end"><h5>Total: $${total.toFixed(2)}</h5></div>`;
        
        container.html(html);
    }

    // Actualizar cantidad en resumen
    actualizarCantidadResumen(index, cantidad) {
        if (this.productosSeleccionados[index]) {
            this.productosSeleccionados[index].cantidad = parseInt(cantidad) || 1;
            this.guardarDatos();
            this.mostrarResumenPedido();
        }
    }

    // Confirmar pedido
    confirmarPedido() {
        if (this.productosSeleccionados.length === 0) {
            this.mostrarError('No hay productos en el pedido');
            return;
        }

        const observaciones = $('#observaciones-pedido').val();
        const prioridad = $('#prioridad-pedido').val();

        this.mostrarLoading('Guardando pedido...');
        
        $.ajax({
            url: 'api/guardar_pedido.php',
            method: 'POST',
            data: {
                productos: JSON.stringify(this.productosSeleccionados),
                observaciones: observaciones,
                prioridad: prioridad
            },
            success: (response) => {
                this.ocultarLoading();
                if (response.success) {
                    this.mostrarExito('Pedido guardado exitosamente');
                    this.limpiarPedido();
                    $('#modalResumenPedido').modal('hide');
                    $('#modalNuevoPedido').modal('hide');
                    this.cargarPedidos();
                    this.cargarEstadisticas();
                } else {
                    this.mostrarError(response.message || 'Error al guardar el pedido');
                }
            },
            error: () => {
                this.ocultarLoading();
                this.mostrarError('Error de conexión al guardar el pedido');
            }
        });
    }

    // Guardar pedido (método alternativo)
    guardarPedido() {
        this.abrirResumenPedido();
    }

    // Limpiar pedido
    limpiarPedido() {
        this.productosSeleccionados = [];
        this.guardarDatos();
        this.actualizarVistaProductos();
        this.actualizarResumen();
    }

    // Cargar estadísticas
    cargarEstadisticas() {
        $.ajax({
            url: 'api/estadisticas_pedidos.php',
            method: 'GET',
            success: (response) => {
                if (response.success) {
                    $('#total-pedidos').text(response.total_pedidos || 0);
                    $('#pedidos-espera').text(response.pedidos_espera || 0);
                    $('#pedidos-completados').text(response.pedidos_completados || 0);
                    $('#total-hoy').text(`$${(response.total_hoy || 0).toFixed(2)}`);
                }
            }
        });
    }

    // Cargar pedidos
    cargarPedidos() {
        this.mostrarLoading('Cargando pedidos...');
        
        $.ajax({
            url: 'api/pedidos_administrativos.php',
            method: 'GET',
            success: (response) => {
                this.ocultarLoading();
                if (response.success) {
                    this.pedidos = response.pedidos || [];
                    this.mostrarPedidos();
                } else {
                    this.mostrarError(response.message || 'Error al cargar pedidos');
                }
            },
            error: () => {
                this.ocultarLoading();
                this.mostrarError('Error de conexión al cargar pedidos');
            }
        });
    }

    // Mostrar pedidos
    mostrarPedidos() {
        const tbody = $('#tbody-pedidos');
        
        if (this.pedidos.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                        <p>No hay pedidos registrados</p>
                    </td>
                </tr>
            `);
            return;
        }

        let html = '';
        this.pedidos.forEach(pedido => {
            const estadoClass = this.getEstadoClass(pedido.estado);
            const estadoText = this.getEstadoText(pedido.estado);
            
            html += `
                <tr>
                    <td>${pedido.folio}</td>
                    <td>${pedido.fecha}</td>
                    <td>${pedido.cantidad_productos} productos</td>
                    <td>$${parseFloat(pedido.total).toFixed(2)}</td>
                    <td><span class="badge ${estadoClass}">${estadoText}</span></td>
                    <td>${pedido.solicitante}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="pedidosAdmin.verDetallesPedido(${pedido.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${pedido.estado === 'pendiente' ? `
                            <button class="btn btn-success btn-sm" onclick="pedidosAdmin.aprobarPedido(${pedido.id})">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="pedidosAdmin.cancelarPedido(${pedido.id})">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        });
        
        tbody.html(html);
    }

    // Obtener clase CSS para estado
    getEstadoClass(estado) {
        const clases = {
            'pendiente': 'bg-warning',
            'aprobado': 'bg-info',
            'completado': 'bg-success',
            'cancelado': 'bg-danger'
        };
        return clases[estado] || 'bg-secondary';
    }

    // Obtener texto para estado
    getEstadoText(estado) {
        const textos = {
            'pendiente': 'Pendiente',
            'aprobado': 'Aprobado',
            'completado': 'Completado',
            'cancelado': 'Cancelado'
        };
        return textos[estado] || 'Desconocido';
    }

    // Ver detalles de pedido
    verDetallesPedido(pedidoId) {
        this.mostrarLoading('Cargando detalles...');
        
        $.ajax({
            url: 'api/detalles_pedido.php',
            method: 'GET',
            data: { id: pedidoId },
            success: (response) => {
                this.ocultarLoading();
                if (response.success) {
                    this.mostrarDetallesPedido(response.pedido);
                    $('#modalDetallesPedido').modal('show');
                } else {
                    this.mostrarError(response.message || 'Error al cargar detalles');
                }
            },
            error: () => {
                this.ocultarLoading();
                this.mostrarError('Error de conexión al cargar detalles');
            }
        });
    }

    // Mostrar detalles de pedido
    mostrarDetallesPedido(pedido) {
        const container = $('#detalles-pedido');
        
        let html = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Folio:</strong> ${pedido.folio}<br>
                    <strong>Fecha:</strong> ${pedido.fecha_creacion}<br>
                    <strong>Estado:</strong> <span class="badge ${this.getEstadoClass(pedido.estado)}">${this.getEstadoText(pedido.estado)}</span>
                </div>
                <div class="col-md-6">
                    <strong>Solicitante:</strong> ${pedido.solicitante}<br>
                    <strong>Prioridad:</strong> ${pedido.prioridad}<br>
                    <strong>Total:</strong> $${parseFloat(pedido.total_estimado).toFixed(2)}
                </div>
            </div>
        `;
        
        if (pedido.observaciones) {
            html += `<div class="mb-3"><strong>Observaciones:</strong><br>${pedido.observaciones}</div>`;
        }
        
        html += '<h6>Productos:</h6><div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
        
        pedido.productos.forEach(producto => {
            const subtotal = producto.precio * producto.cantidad;
            const esEncargo = producto.es_encargo ? ' <span class="badge bg-info">Encargo</span>' : '';
            const infoAdicional = producto.es_encargo ? 
                `<br><small class="text-muted">Paciente: ${producto.cliente || 'N/A'}</small>` : '';
            
            html += `
                <tr>
                    <td>
                        ${producto.nombre}${esEncargo}${infoAdicional}
                    </td>
                    <td>${producto.cantidad}</td>
                    <td>$${producto.precio.toFixed(2)}</td>
                    <td>$${subtotal.toFixed(2)}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        
        container.html(html);
        
        // Mostrar/ocultar botones según estado
        if (pedido.estado === 'pendiente') {
            $('#btn-aprobar-pedido, #btn-cancelar-pedido').show();
            $('#btn-aprobar-pedido').data('pedido-id', pedido.id);
            $('#btn-cancelar-pedido').data('pedido-id', pedido.id);
        } else {
            $('#btn-aprobar-pedido, #btn-cancelar-pedido').hide();
        }
    }

    // Aprobar pedido
    aprobarPedido(pedidoId) {
        const pedidoIdToApprove = pedidoId || $('#btn-aprobar-pedido').data('pedido-id');
        
        Swal.fire({
            title: '¿Confirmar aprobación?',
            text: '¿Estás seguro de que deseas aprobar este pedido?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.cambiarEstadoPedido(pedidoIdToApprove, 'aprobado');
            }
        });
    }

    // Cancelar pedido
    cancelarPedido(pedidoId) {
        const pedidoIdToCancel = pedidoId || $('#btn-cancelar-pedido').data('pedido-id');
        
        Swal.fire({
            title: '¿Confirmar cancelación?',
            text: '¿Estás seguro de que deseas cancelar este pedido? Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.cambiarEstadoPedido(pedidoIdToCancel, 'cancelado');
            }
        });
    }

    // Cambiar estado de pedido
    cambiarEstadoPedido(pedidoId, nuevoEstado) {
        this.mostrarLoading('Actualizando estado...');
        
        $.ajax({
            url: 'api/cambiar_estado_pedido.php',
            method: 'POST',
            data: {
                id: pedidoId,
                estado: nuevoEstado
            },
            success: (response) => {
                this.ocultarLoading();
                if (response.success) {
                    this.mostrarExito(`Pedido ${nuevoEstado === 'aprobado' ? 'aprobado' : 'cancelado'} exitosamente`);
                    $('#modalDetallesPedido').modal('hide');
                    this.cargarPedidos();
                    this.cargarEstadisticas();
                } else {
                    this.mostrarError(response.message || 'Error al cambiar estado');
                }
            },
            error: () => {
                this.ocultarLoading();
                this.mostrarError('Error de conexión al cambiar estado');
            }
        });
    }

    // Aplicar filtros
    aplicarFiltros() {
        const estado = $('#filtro-estado').val();
        const fechaInicio = $('#filtro-fecha-inicio').val();
        const fechaFin = $('#filtro-fecha-fin').val();
        const busqueda = $('#busqueda-pedidos').val();
        
        this.mostrarLoading('Aplicando filtros...');
        
        $.ajax({
            url: 'api/pedidos_administrativos.php',
            method: 'GET',
            data: {
                estado: estado,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
                busqueda: busqueda
            },
            success: (response) => {
                this.ocultarLoading();
                if (response.success) {
                    this.pedidos = response.pedidos || [];
                    this.mostrarPedidos();
                } else {
                    this.mostrarError(response.message || 'Error al aplicar filtros');
                }
            },
            error: () => {
                this.ocultarLoading();
                this.mostrarError('Error de conexión al aplicar filtros');
            }
        });
    }

    // Refrescar pedidos
    refrescarPedidos() {
        this.cargarPedidos();
        this.cargarEstadisticas();
    }

    // Exportar pedidos
    exportarPedidos() {
        const filtros = this.obtenerFiltrosActuales();
        const url = `api/pedidos_administrativos.php?exportar=1&${new URLSearchParams(filtros)}`;
        
        // Crear un enlace temporal para descargar
        const link = document.createElement('a');
        link.href = url;
        link.download = `pedidos_administrativos_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.mostrarExito('Exportación iniciada');
    }

    // Obtener filtros actuales
    obtenerFiltrosActuales() {
        return {
            estado: $('#filtro-estado').val(),
            fecha_inicio: $('#filtro-fecha-inicio').val(),
            fecha_fin: $('#filtro-fecha-fin').val(),
            busqueda: $('#busqueda-pedidos').val()
        };
    }

    // Mostrar loading
    mostrarLoading(mensaje = 'Cargando...') {
        $('#loading-overlay').show();
        $('#loading-overlay .loading-spinner p').text(mensaje);
    }

    // Ocultar loading
    ocultarLoading() {
        $('#loading-overlay').hide();
    }

    // Mostrar éxito
    mostrarExito(mensaje) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: mensaje,
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Mostrar error
    mostrarError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje
        });
    }

    // Obtener clase CSS para estado de stock
    getStockClass(stockStatus) {
        const clases = {
            'agotado': 'text-danger',
            'bajo': 'text-warning',
            'normal': 'text-success'
        };
        return clases[stockStatus] || 'text-muted';
    }

    // Obtener icono para estado de stock
    getStockIcon(stockStatus) {
        const iconos = {
            'agotado': '<i class="fas fa-times-circle"></i>',
            'bajo': '<i class="fas fa-exclamation-triangle"></i>',
            'normal': '<i class="fas fa-check-circle"></i>'
        };
        return iconos[stockStatus] || '<i class="fas fa-question-circle"></i>';
    }
}

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    // Inicializar el módulo de productos si no existe
    if (typeof productosModule === 'undefined') {
        window.productosModule = new ProductosModule();
    }
    
    // Inicializar el sistema de pedidos administrativos
    window.pedidosAdmin = new PedidosAdministrativos();
    
    // Ocultar spinner
    setTimeout(() => {
        $('#spinner').removeClass('show');
    }, 1000);
}); 