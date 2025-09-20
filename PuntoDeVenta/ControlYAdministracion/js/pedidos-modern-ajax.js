// Sistema de Gestión de Pedidos - JavaScript con AJAX para descarga Excel
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

    // Función para descargar Excel general con AJAX
    descargarExcel() {
        const btnDescargar = $('#btnDescargarExcel');
        
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
        
        // Verificar si hay pedidos para exportar
        if (this.pedidos && this.pedidos.length === 0) {
            this.mostrarError('No hay pedidos para exportar. Ajusta los filtros e intenta nuevamente.');
            btnDescargar.removeClass('downloading');
            btnDescargar.html('<i class="fas fa-file-excel me-2"></i>Descargar Excel');
            btnDescargar.prop('disabled', false);
            return;
        }
        
        // Usar AJAX para descargar
        $.ajax({
            url: 'api/exportar_pedidos_excel.php',
            method: 'POST',
            data: filtros,
            xhrFields: {
                responseType: 'blob'
            },
            success: (data, status, xhr) => {
                // Crear blob y descargar
                const blob = new Blob([data], { 
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
                });
                
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `pedidos_${new Date().toISOString().slice(0, 10)}.xlsx`;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                // Mostrar mensaje de éxito
                this.mostrarExito(`Archivo Excel generado correctamente (${this.pedidos ? this.pedidos.length : 0} pedidos)`);
            },
            error: (xhr, status, error) => {
                console.error('Error al descargar Excel:', error);
                this.mostrarError('Error al generar el archivo Excel: ' + error);
            },
            complete: () => {
                // Restaurar botón
                btnDescargar.removeClass('downloading');
                btnDescargar.html('<i class="fas fa-file-excel me-2"></i>Descargar Excel');
                btnDescargar.prop('disabled', false);
            }
        });
    }

    // Función para descargar Excel de un pedido específico con AJAX
    descargarExcelPedido(pedidoId) {
        const btnDescargar = $(`.descargar-excel-pedido[data-pedido-id="${pedidoId}"]`);
        
        // Mostrar estado de descarga
        btnDescargar.html('<i class="fas fa-spinner fa-spin"></i>');
        btnDescargar.prop('disabled', true);
        
        // Usar AJAX para descargar
        $.ajax({
            url: 'api/exportar_pedido_excel.php',
            method: 'POST',
            data: { pedido_id: pedidoId },
            xhrFields: {
                responseType: 'blob'
            },
            success: (data, status, xhr) => {
                // Crear blob y descargar
                const blob = new Blob([data], { 
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
                });
                
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `pedido_${pedidoId}_${new Date().toISOString().slice(0, 10)}.xlsx`;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                // Mostrar mensaje de éxito
                this.mostrarExito('Archivo Excel del pedido generado correctamente');
            },
            error: (xhr, status, error) => {
                console.error('Error al descargar Excel del pedido:', error);
                this.mostrarError('Error al generar el archivo Excel del pedido: ' + error);
            },
            complete: () => {
                // Restaurar botón
                btnDescargar.html('<i class="fas fa-file-excel"></i>');
                btnDescargar.prop('disabled', false);
            }
        });
    }

    // Funciones auxiliares (simplificadas para el ejemplo)
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

    // Funciones placeholder para compatibilidad
    async verDetallePedido(pedidoId) {
        console.log('Ver detalle pedido:', pedidoId);
    }

    async confirmarCambiarEstado(pedidoId, estado) {
        console.log('Cambiar estado pedido:', pedidoId, estado);
    }

    async confirmarEliminarPedido(pedidoId) {
        console.log('Eliminar pedido:', pedidoId);
    }

    async abrirModalNuevoPedido() {
        console.log('Abrir modal nuevo pedido');
    }

    async abrirModalListadoPedidos() {
        console.log('Abrir modal listado pedidos');
    }

    async mostrarProductosStockBajo() {
        console.log('Mostrar productos stock bajo');
    }

    async confirmarGuardarPedidoNuevo() {
        console.log('Confirmar guardar pedido nuevo');
    }

    async confirmarLimpiarPedidoNuevo() {
        console.log('Confirmar limpiar pedido nuevo');
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
        console.log('Cargar estadísticas');
    }
}

// Inicializar cuando el DOM esté listo
$(document).ready(() => {
    window.sistemaPedidos = new SistemaPedidos();
});
