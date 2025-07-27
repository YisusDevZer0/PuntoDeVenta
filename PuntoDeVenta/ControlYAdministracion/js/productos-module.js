// Módulo simplificado para manejo de productos
class ProductosModule {
    constructor() {
        this.productosSeleccionados = [];
        this.paginaActual = 1;
        this.productosPorPagina = 10;
    }

    // Búsqueda de productos
    async buscarProductos(query, modalId) {
        console.log('Buscando productos:', query, modalId);
        
        if (query.length < 3) {
            this.mostrarError('Ingresa al menos 3 caracteres para buscar');
            return;
        }

        try {
            console.log('Enviando petición a:', 'Controladores/PedidosController.php');
            console.log('Datos enviados:', { accion: 'buscar_producto', q: query });
            
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'buscar_producto',
                q: query
            });

            console.log('Respuesta búsqueda:', response);
            console.log('Tipo de respuesta:', typeof response);

            // Si la respuesta es string, intentar parsear como JSON
            if (typeof response === 'string') {
                try {
                    const parsedResponse = JSON.parse(response);
                    console.log('Respuesta parseada:', parsedResponse);
                    
                    if (parsedResponse.status === 'ok') {
                        this.mostrarResultadosBusqueda(parsedResponse.data, modalId);
                    } else {
                        this.mostrarError('Error al buscar productos: ' + (parsedResponse.msg || 'Error desconocido'));
                    }
                } catch (parseError) {
                    console.error('Error al parsear respuesta:', parseError);
                    this.mostrarError('Error en la respuesta del servidor');
                }
            } else if (response.status === 'ok') {
                this.mostrarResultadosBusqueda(response.data, modalId);
            } else {
                this.mostrarError('Error al buscar productos: ' + (response.msg || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error en buscarProductos:', error);
            this.mostrarError('Error de conexión: ' + error.message);
        }
    }

    // Mostrar resultados de búsqueda
    mostrarResultadosBusqueda(productos, modalId) {
        console.log('Mostrando resultados:', productos, modalId);
        const container = $(`#resultados-busqueda-${modalId}`);
        
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
                                    `<button class="btn btn-primary btn-sm agregar-producto-${modalId}">Agregar</button>`
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
        $(`.agregar-producto-${modalId}`).on('click', (e) => {
            const card = $(e.currentTarget).closest('.producto-card');
            const producto = JSON.parse(card.data('producto'));
            this.agregarProducto(producto, modalId);
        });
    }

    // Agregar producto al pedido
    agregarProducto(producto, modalId) {
        console.log('Agregando producto:', producto, modalId);
        
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

        console.log('Productos seleccionados después de agregar:', this.productosSeleccionados);

        // Actualizar la vista
        this.actualizarVistaProductos(modalId);
        this.actualizarResumen(modalId);
        this.guardarDatos();
        
        // Mostrar notificación
        this.mostrarExito('Producto agregado al pedido');

        // Disparar evento para que el sistema principal sepa que se agregó un producto
        $(document).trigger('productoAgregado', { producto, modalId });
    }

    // Actualizar vista de productos
    actualizarVistaProductos(modalId) {
        const container = $(`#productos-pedido-${modalId}`);
        
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
                                   onchange="productosModule.actualizarCantidad(${index}, this.value, '${modalId}')">
                            <span class="me-2">$${producto.precio.toFixed(2)}</span>
                            <button class="btn btn-danger btn-sm" onclick="productosModule.eliminarProducto(${index}, '${modalId}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
    }

    // Actualizar cantidad
    actualizarCantidad(index, cantidad, modalId) {
        if (this.productosSeleccionados[index]) {
            this.productosSeleccionados[index].cantidad = parseInt(cantidad) || 1;
            this.actualizarResumen(modalId);
            this.guardarDatos();
        }
    }

    // Eliminar producto
    eliminarProducto(index, modalId) {
        this.productosSeleccionados.splice(index, 1);
        this.actualizarVistaProductos(modalId);
        this.actualizarResumen(modalId);
        this.guardarDatos();
    }

    // Actualizar resumen
    actualizarResumen(modalId) {
        const totalProductos = this.productosSeleccionados.length;
        const totalCantidad = this.productosSeleccionados.reduce((sum, p) => sum + p.cantidad, 0);
        const totalPrecio = this.productosSeleccionados.reduce((sum, p) => sum + (p.precio * p.cantidad), 0);

        $(`#total-productos-${modalId}`).text(totalProductos);
        $(`#total-cantidad-${modalId}`).text(totalCantidad);
        $(`#total-precio-${modalId}`).text(`$${totalPrecio.toFixed(2)}`);
    }

    // Cargar productos con stock bajo con paginación
    async cargarProductosStockBajo() {
        try {
            const response = await $.post('Controladores/PedidosController.php', {
                accion: 'productos_stock_bajo'
            });

            if (response.status === 'ok') {
                this.mostrarModalStockBajo(response.data || []);
            } else {
                this.mostrarError('Error al cargar productos con stock bajo');
            }
        } catch (error) {
            this.mostrarError('Error de conexión');
        }
    }

    // Mostrar modal de stock bajo con paginación
    mostrarModalStockBajo(productos) {
        if (!productos || productos.length === 0) {
            this.mostrarError('No hay productos con stock bajo');
            return;
        }

        const totalPaginas = Math.ceil(productos.length / this.productosPorPagina);
        const inicio = (this.paginaActual - 1) * this.productosPorPagina;
        const fin = inicio + this.productosPorPagina;
        const productosPagina = productos.slice(inicio, fin);

        let html = '<div class="row">';
        productosPagina.forEach(producto => {
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

        // Agregar controles de paginación
        if (totalPaginas > 1) {
            html += `
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Página ${this.paginaActual} de ${totalPaginas} 
                            (${productos.length} productos)
                        </small>
                    </div>
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-secondary btn-sm" id="btnAnterior" 
                                ${this.paginaActual === 1 ? 'disabled' : ''}>
                            <i class="fas fa-chevron-left"></i> Anterior
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" id="btnSiguiente" 
                                ${this.paginaActual === totalPaginas ? 'disabled' : ''}>
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        $('#stock-bajo-content').html(html);

        // Event listeners para agregar productos
        $('.agregar-stock-bajo').on('click', (e) => {
            const card = $(e.currentTarget).closest('.producto-card');
            const producto = JSON.parse(card.data('producto'));
            this.agregarProductoStockBajo(producto);
        });

        // Event listeners para paginación
        $('#btnAnterior').on('click', () => {
            if (this.paginaActual > 1) {
                this.paginaActual--;
                this.mostrarModalStockBajo(productos);
            }
        });

        $('#btnSiguiente').on('click', () => {
            if (this.paginaActual < totalPaginas) {
                this.paginaActual++;
                this.mostrarModalStockBajo(productos);
            }
        });

        $('#modalStockBajo').modal('show');
    }

    // Agregar producto desde stock bajo
    agregarProductoStockBajo(producto) {
        console.log('Agregando producto desde stock bajo:', producto);
        
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

        console.log('Productos seleccionados después de agregar desde stock bajo:', this.productosSeleccionados);

        // Si el modal nuevo no está abierto, abrirlo
        if (!$('#modalNuevoPedido').hasClass('show')) {
            $('#modalNuevoPedido').modal('show');
        }

        // Actualizar la vista del modal nuevo
        this.actualizarVistaProductos('nuevo');
        this.actualizarResumen('nuevo');
        this.guardarDatos();
        
        // Mostrar notificación
        this.mostrarExito('Producto agregado al pedido');
        
        // Cerrar el modal de stock bajo
        $('#modalStockBajo').modal('hide');
    }

    // Guardar datos en localStorage
    guardarDatos() {
        localStorage.setItem('productosSeleccionados', JSON.stringify(this.productosSeleccionados));
    }

    // Cargar datos desde localStorage
    cargarDatosGuardados() {
        const datos = localStorage.getItem('productosSeleccionados');
        if (datos) {
            this.productosSeleccionados = JSON.parse(datos);
        }
    }

    // Limpiar datos guardados
    limpiarDatosGuardados() {
        localStorage.removeItem('productosSeleccionados');
        this.productosSeleccionados = [];
    }

    // Limpiar pedido
    limpiarPedido(modalId) {
        $(`#busqueda-producto-${modalId}`).val('');
        $(`#resultados-busqueda-${modalId}`).html(`
            <p class="text-muted text-center">
                <i class="fas fa-search fa-2x mb-2"></i><br>
                Busca productos para agregar al pedido
            </p>
        `);
        $(`#observaciones-pedido-${modalId}`).val('');
        $(`#prioridad-pedido-${modalId}`).val('normal');
        this.productosSeleccionados = [];
        this.actualizarVistaProductos(modalId);
        this.actualizarResumen(modalId);
        this.limpiarDatosGuardados();
        this.mostrarExito('Pedido limpiado correctamente');
    }

    // Obtener productos seleccionados
    getProductosSeleccionados() {
        return this.productosSeleccionados;
    }

    // Establecer productos seleccionados
    setProductosSeleccionados(productos) {
        this.productosSeleccionados = productos;
    }

    // Mostrar mensajes
    mostrarExito(mensaje) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(mensaje);
        }
    }

    mostrarError(mensaje) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje
            });
        } else {
            alert('Error: ' + mensaje);
        }
    }
}

// Crear instancia global
const productosModule = new ProductosModule(); 