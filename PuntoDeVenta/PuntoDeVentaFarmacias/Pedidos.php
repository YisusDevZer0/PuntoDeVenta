<?php
include_once "Controladores/ControladorUsuario.php";
include "Controladores/db_connect.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de Pedidos - <?php echo $row['Licencia']?> - Sucursal <?php echo $row['Nombre_Sucursal']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
    
    <!-- CSS adicional para el sistema de pedidos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Estilos homologados con el resto de la aplicación */
        .pedidos-container {
            background: #FFFFFF;
            min-height: 100vh;
            padding: 20px;
        }
        
        .dashboard-card {
            background: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease-in-out;
        }
        
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .stats-card {
            color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            transition: transform 0.2s ease-in-out;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* Colores específicos para cada estadística */
        .stats-pendientes {
            background: #ffc107; /* Amarillo para pendientes */
        }
        
        .stats-aprobados {
            background: #28a745; /* Verde para aprobados */
        }
        
        .stats-proceso {
            background: #17a2b8; /* Azul para en proceso */
        }
        
        .pedido-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            border-left: 4px solid var(--primary);
        }
        
        .pedido-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            border-left-color: #007bff;
        }
        
        .estado-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .estado-pendiente { background: #ffc107; color: #000; }
        .estado-aprobado { background: #28a745; color: white; }
        .estado-rechazado { background: #dc3545; color: white; }
        .estado-en_proceso { background: #17a2b8; color: white; }
        .estado-completado { background: #6f42c1; color: white; }
        .estado-cancelado { background: #6c757d; color: white; }
        
        .prioridad-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .prioridad-baja { background: #28a745; color: white; }
        .prioridad-normal { background: #17a2b8; color: white; }
        .prioridad-alta { background: #ffc107; color: #000; }
        .prioridad-urgente { background: #dc3545; color: white; }
        
        /* Colores por prioridad en los pedidos */
        .pedido-item.prioridad-baja {
            border-left-color: #28a745;
        }
        
        .pedido-item.prioridad-normal {
            border-left-color: var(--primary);
        }
        
        .pedido-item.prioridad-alta {
            border-left-color: #ffc107;
        }
        
        .pedido-item.prioridad-urgente {
            border-left-color: #dc3545;
        }
        
        .filtros-container {
            background: #FFFFFF;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }
        
        .btn-modern {
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
            border: none;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .search-box {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 20px;
            transition: all 0.2s ease-in-out;
        }
        
        .search-box:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 156, 255, 0.25);
        }
        
        .modal-content {
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .modal-header {
            background: var(--primary);
            color: white;
            border-radius: 8px 8px 0 0;
            border: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    
    <div class="content">
        <?php include "navbar.php"; ?>
        
        <div class="pedidos-container">
            <div class="container-fluid">
                <!-- Header con estadísticas -->
                <div class="page-header" style="background: var(--primary); color: white; padding: 20px; margin-bottom: 20px; border-radius: 8px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="page-title" style="margin: 0; font-size: 1.5rem; font-weight: 600;">
                            <i class="fas fa-shopping-cart me-3"></i>
                            Gestión de Pedidos
                        </h2>
                        <div class="d-flex align-items-center">
                            <span class="text-light me-3">
                                <i class="fas fa-building me-2"></i>
                                Sucursal: <?php echo $row['Nombre_Sucursal']?>
                            </span>
                            <button class="btn btn-light btn-modern" id="btnNuevoPedido">
                                <i class="fas fa-plus me-2"></i>Nuevo Pedido
                            </button>
                            <button class="btn btn-light btn-modern ms-2" id="btnRefresh">
                                <i class="fas fa-sync-alt me-2"></i>Actualizar
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Tarjetas de estadísticas -->
                <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <div class="stats-card stats-pendientes">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h4 id="stats-pendientes">0</h4>
                        <p class="mb-0">Pendientes</p>
                    </div>
                    <div class="stats-card stats-aprobados">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h4 id="stats-aprobados">0</h4>
                        <p class="mb-0">Aprobados</p>
                    </div>
                    <div class="stats-card stats-proceso">
                        <i class="fas fa-truck fa-2x mb-2"></i>
                        <h4 id="stats-proceso">0</h4>
                        <p class="mb-0">En Proceso</p>
                    </div>
                </div>
                
                <!-- Barra de acciones -->
                <div class="action-buttons mb-4" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" id="btnNuevoPedido">
                                <i class="fas fa-plus me-2"></i>Nuevo Pedido
                            </button>
                            <button class="btn btn-info" id="btnListadoPedidos">
                                <i class="fas fa-list me-2"></i>Listado Completo
                            </button>
                            <button class="btn btn-secondary" id="btnRefresh">
                                <i class="fas fa-sync-alt me-2"></i>Actualizar
                            </button>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="alert alert-info mb-0" style="padding: 8px 15px; margin: 0;">
                                <i class="fas fa-info-circle me-2"></i>
                                Hacer clic para ver detalles - Solo lectura después de creado
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="filtros-container">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Buscar</label>
                                    <input type="text" class="form-control search-box" id="busqueda" placeholder="Folio, producto, usuario...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Estado</label>
                                    <select class="form-control" id="filtro-estado">
                                        <option value="">Todos</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="aprobado">Aprobado</option>
                                        <option value="rechazado">Rechazado</option>
                                        <option value="en_proceso">En Proceso</option>
                                        <option value="completado">Completado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="filtro-fecha-inicio">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control" id="filtro-fecha-fin">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Acciones</label>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-modern" id="btnAplicarFiltros">
                                            <i class="fas fa-filter me-2"></i>Aplicar Filtros
                                        </button>
                                        <button class="btn btn-secondary btn-modern" id="btnLimpiarFiltros">
                                            <i class="fas fa-times me-2"></i>Limpiar Filtros
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de pedidos -->
                <div class="row">
                    <div class="col-12">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Lista de Pedidos
                                </h5>
                            </div>
                            
                            <div id="loading-spinner" class="loading-spinner">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando pedidos...</p>
                            </div>
                            
                            <div id="lista-pedidos">
                                <!-- Los pedidos se cargarán aquí dinámicamente -->
                            </div>
                            
                            <div id="empty-state" class="empty-state" style="display: none;">
                                <i class="fas fa-inbox"></i>
                                <h4>No hay pedidos</h4>
                                <p>No se encontraron pedidos con los filtros aplicados</p>
                                <button class="btn btn-primary btn-modern mt-3" id="btnCrearPrimerPedido">
                                    <i class="fas fa-plus me-2"></i>Crear Primer Pedido
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Detalle del Pedido -->
    <div class="modal fade" id="modalDetallePedido" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>
                        Detalle del Pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información del Pedido</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Folio:</strong></td><td id="detalle-pedido-folio"></td></tr>
                                <tr><td><strong>Estado:</strong></td><td id="detalle-pedido-estado"></td></tr>
                                <tr><td><strong>Fecha:</strong></td><td id="detalle-pedido-fecha"></td></tr>
                                <tr><td><strong>Usuario:</strong></td><td id="detalle-pedido-usuario"></td></tr>
                                <tr><td><strong>Sucursal:</strong></td><td id="detalle-pedido-sucursal"></td></tr>
                                <tr><td><strong>Total:</strong></td><td id="detalle-pedido-total"></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Observaciones</h6>
                            <p class="text-muted" id="detalle-pedido-observaciones"></p>
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
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle-productos-tbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nuevo Pedido -->
    <div class="modal fade" id="modalNuevoPedido" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Crear Nuevo Pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna izquierda: Búsqueda -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-search me-2"></i>
                                        Buscar Productos
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="busqueda-producto-nuevo" 
                                               placeholder="Buscar productos...">
                                        <button class="btn btn-primary" type="button" id="btnBuscarProductoNuevo">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    
                                    <div id="resultados-busqueda-nuevo">
                                        <p class="text-muted text-center">
                                            <i class="fas fa-search fa-2x mb-2"></i><br>
                                            Busca productos para agregar al pedido
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna derecha: Productos del pedido -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Productos del Pedido
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="productos-pedido-nuevo" class="productos-container">
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                            <p>Busca productos para agregar</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Resumen del pedido -->
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <h6>Resumen del Pedido</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Productos:</small><br>
                                                <strong id="total-productos-nuevo">0</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Cantidad total:</small><br>
                                                <strong id="total-cantidad-nuevo">0</strong>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12">
                                                <small class="text-muted">Total estimado:</small><br>
                                                <strong id="total-precio-nuevo">$0.00</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Observaciones y prioridad -->
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <label for="observaciones-pedido-nuevo" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones-pedido-nuevo" rows="3" 
                                      placeholder="Observaciones del pedido..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="prioridad-pedido-nuevo" class="form-label">Prioridad</label>
                            <select class="form-select" id="prioridad-pedido-nuevo">
                                <option value="baja">Baja</option>
                                <option value="normal" selected>Normal</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnLimpiarPedidoNuevo">
                        <i class="fas fa-trash me-2"></i>Limpiar
                    </button>
                    <button type="button" class="btn btn-success" id="btnGuardarPedidoNuevo">
                        <i class="fas fa-save me-2"></i>Guardar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "Footer.php"; ?>
    
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 9999;">
        <div class="loading-spinner" style="background: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando pedidos...</p>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let productosPedido = [];
            
            // Event listeners
            $('#btnNuevoPedido').click(function() {
                $('#modalNuevoPedido').modal('show');
            });
            
            $('#btnRefresh').click(function() {
                cargarPedidos();
            });
            
            $('#btnAplicarFiltros').click(function() {
                cargarPedidos();
            });
            
            $('#btnLimpiarFiltros').click(function() {
                $('#busqueda').val('');
                $('#filtro-estado').val('');
                $('#filtro-fecha-inicio').val('');
                $('#filtro-fecha-fin').val('');
                cargarPedidos();
            });
            
            $('#btnCrearPrimerPedido').click(function() {
                $('#modalNuevoPedido').modal('show');
            });
            
            $('#btnBuscarProductoNuevo').click(function() {
                buscarProductos();
            });
            
            $('#busqueda-producto-nuevo').keypress(function(e) {
                if (e.which === 13) {
                    buscarProductos();
                }
            });
            
            $('#btnGuardarPedidoNuevo').click(function() {
                guardarPedido();
            });
            
            $('#btnLimpiarPedidoNuevo').click(function() {
                limpiarPedido();
            });
            
            // Búsqueda en tiempo real
            $('#busqueda').on('input', function() {
                clearTimeout(window.busquedaTimeout);
                window.busquedaTimeout = setTimeout(function() {
                    cargarPedidos();
                }, 500);
            });
            
            // Cargar datos iniciales
            setTimeout(function() {
                document.getElementById('loading-spinner').style.display = 'none';
                cargarEstadisticas();
                cargarPedidos();
            }, 1000);
            
            // Funciones
            function buscarProductos() {
                const query = $('#busqueda-producto-nuevo').val().trim();
                if (query.length < 2) {
                    alert('Ingresa al menos 2 caracteres para buscar');
                    return;
                }
                
                fetch('api/buscar_productos.php', {
                    method: 'POST',
                    body: new FormData().append('query', query)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarResultadosBusqueda(data.productos);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al buscar productos');
                });
            }
            
            function mostrarResultadosBusqueda(productos) {
                let html = '';
                productos.forEach(producto => {
                    html += `
                        <div class="card mb-2">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${producto.Nombre_Prod}</h6>
                                        <small class="text-muted">Código: ${producto.Cod_Barra}</small>
                                        <br>
                                        <small class="text-success">$${producto.Precio_Venta}</small>
                                        <small class="text-muted"> | Stock: ${producto.Existencias_R}</small>
                                    </div>
                                    <button class="btn btn-primary btn-sm" onclick="agregarProducto(${producto.ID_Prod_POS}, '${producto.Nombre_Prod}', ${producto.Precio_Venta})">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#resultados-busqueda-nuevo').html(html);
            }
            
            window.agregarProducto = function(id, nombre, precio) {
                const productoExistente = productosPedido.find(p => p.id === id);
                if (productoExistente) {
                    productoExistente.cantidad += 1;
                } else {
                    productosPedido.push({
                        id: id,
                        nombre: nombre,
                        precio: parseFloat(precio),
                        cantidad: 1
                    });
                }
                actualizarListaProductos();
            };
            
            window.cambiarCantidad = function(id, cambio) {
                const producto = productosPedido.find(p => p.id === id);
                if (producto) {
                    producto.cantidad += cambio;
                    if (producto.cantidad <= 0) {
                        productosPedido = productosPedido.filter(p => p.id !== id);
                    }
                    actualizarListaProductos();
                }
            };
            
            window.eliminarProducto = function(id) {
                productosPedido = productosPedido.filter(p => p.id !== id);
                actualizarListaProductos();
            };
            
            function actualizarListaProductos() {
                let html = '';
                if (productosPedido.length === 0) {
                    html = '<div class="text-center text-muted py-4"><i class="fas fa-shopping-cart fa-2x mb-2"></i><p>Busca productos para agregar</p></div>';
                } else {
                    productosPedido.forEach(producto => {
                        html += `
                            <div class="card mb-2">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">${producto.nombre}</h6>
                                            <small class="text-success">$${producto.precio.toFixed(2)}</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${producto.id}, -1)">-</button>
                                            <span class="mx-2">${producto.cantidad}</span>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${producto.id}, 1)">+</button>
                                            <button class="btn btn-sm btn-outline-danger ms-2" onclick="eliminarProducto(${producto.id})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                $('#productos-pedido-nuevo').html(html);
                actualizarResumen();
            }
            
            function actualizarResumen() {
                const totalProductos = productosPedido.length;
                const totalCantidad = productosPedido.reduce((sum, p) => sum + p.cantidad, 0);
                const totalPrecio = productosPedido.reduce((sum, p) => sum + (p.precio * p.cantidad), 0);
                
                $('#total-productos-nuevo').text(totalProductos);
                $('#total-cantidad-nuevo').text(totalCantidad);
                $('#total-precio-nuevo').text('$' + totalPrecio.toFixed(2));
            }
            
            function guardarPedido() {
                if (productosPedido.length === 0) {
                    alert('Debe agregar al menos un producto al pedido');
                    return;
                }
                
                const observaciones = $('#observaciones-pedido-nuevo').val();
                const prioridad = $('#prioridad-pedido-nuevo').val();
                
                const btnGuardar = $('#btnGuardarPedidoNuevo');
                const textoOriginal = btnGuardar.html();
                btnGuardar.html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
                btnGuardar.prop('disabled', true);
                
                const formData = new FormData();
                formData.append('productos', JSON.stringify(productosPedido));
                formData.append('observaciones', observaciones);
                formData.append('prioridad', prioridad);
                
                fetch('api/guardar_pedido.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pedido guardado exitosamente\nFolio: ' + data.folio);
                        $('#modalNuevoPedido').modal('hide');
                        limpiarPedido();
                        cargarEstadisticas();
                        cargarPedidos();
                    } else {
                        alert('Error al guardar pedido: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al guardar pedido');
                })
                .finally(() => {
                    btnGuardar.html(textoOriginal);
                    btnGuardar.prop('disabled', false);
                });
            }
            
            function limpiarPedido() {
                productosPedido = [];
                $('#observaciones-pedido-nuevo').val('');
                $('#prioridad-pedido-nuevo').val('normal');
                $('#busqueda-producto-nuevo').val('');
                $('#resultados-busqueda-nuevo').html('<p class="text-muted text-center"><i class="fas fa-search fa-2x mb-2"></i><br>Busca productos para agregar al pedido</p>');
                actualizarListaProductos();
            }
            
            function cargarEstadisticas() {
                fetch('api/estadisticas_pedidos.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#stats-pendientes').text(data.estadisticas.pendientes);
                        $('#stats-aprobados').text(data.estadisticas.aprobados);
                        $('#stats-proceso').text(data.estadisticas.en_proceso);
                    }
                })
                .catch(error => {
                    console.error('Error al cargar estadísticas:', error);
                });
            }
            
            function cargarPedidos() {
                const formData = new FormData();
                formData.append('estado', $('#filtro-estado').val());
                formData.append('fecha_inicio', $('#filtro-fecha-inicio').val());
                formData.append('fecha_fin', $('#filtro-fecha-fin').val());
                formData.append('busqueda', $('#busqueda').val());
                
                fetch('api/listar_pedidos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarPedidos(data.pedidos);
                    } else {
                        console.error('Error al cargar pedidos:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al cargar pedidos:', error);
                });
            }
            
            function mostrarPedidos(pedidos) {
                const container = $('#lista-pedidos');
                
                if (pedidos.length === 0) {
                    $('#empty-state').show();
                    container.hide();
                    return;
                }
                
                $('#empty-state').hide();
                container.show();
                
                let html = '';
                pedidos.forEach(pedido => {
                    const estadoClass = `estado-${pedido.estado}`;
                    const prioridadClass = `prioridad-${pedido.prioridad}`;
                    
                    const fecha = new Date(pedido.fecha_creacion);
                    const fechaFormateada = fecha.toLocaleDateString('es-ES');
                    const tiempoTranscurrido = calcularTiempoTranscurrido(fecha);
                    
                    html += `
                        <div class="pedido-item ${prioridadClass}" onclick="verDetallePedido(${pedido.id})">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h6 class="mb-1 fw-bold">${pedido.folio}</h6>
                                    <small class="text-muted">${fechaFormateada} | ${tiempoTranscurrido}</small>
                                </div>
                                <div class="col-md-2">
                                    <span class="estado-badge ${estadoClass}">${pedido.estado}</span>
                                    <br>
                                    <span class="prioridad-badge ${prioridadClass}">${pedido.prioridad}</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">${pedido.usuario_nombre}</small>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Productos: <strong>0</strong></small><br>
                                    <small class="text-muted">Unidades: <strong>0</strong></small>
                                </div>
                                <div class="col-md-2 text-end">
                                    <h6 class="mb-0 text-success">$${pedido.total_estimado.toFixed(2)}</h6>
                                    <small class="text-muted">${pedido.sucursal_nombre || 'Farmacia'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.html(html);
            }
            
            function calcularTiempoTranscurrido(fecha) {
                const ahora = new Date();
                const diffMs = ahora - fecha;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMins / 60);
                const diffDays = Math.floor(diffHours / 24);
                
                if (diffDays > 0) {
                    return `${diffDays} día(s)`;
                } else if (diffHours > 0) {
                    return `${diffHours} hora(s)`;
                } else if (diffMins > 0) {
                    return `${diffMins} minuto(s)`;
                } else {
                    return 'Recién creado';
                }
            }
            
            window.verDetallePedido = function(pedidoId) {
                // Buscar el pedido en la lista actual
                const pedidos = window.pedidosActuales || [];
                const pedido = pedidos.find(p => p.id == pedidoId);
                
                if (pedido) {
                    // Llenar el modal con los datos del pedido
                    $('#detalle-pedido-folio').text(pedido.folio);
                    $('#detalle-pedido-estado').html(`<span class="estado-badge estado-${pedido.estado}">${pedido.estado}</span>`);
                    $('#detalle-pedido-fecha').text(new Date(pedido.fecha_creacion).toLocaleString('es-ES'));
                    $('#detalle-pedido-usuario').text(pedido.usuario_nombre);
                    $('#detalle-pedido-sucursal').text(pedido.sucursal_nombre || 'Farmacia');
                    $('#detalle-pedido-total').text('$' + pedido.total_estimado.toFixed(2));
                    $('#detalle-pedido-observaciones').text(pedido.observaciones || 'Sin observaciones');
                    
                    // Limpiar tabla de productos
                    $('#detalle-productos-tbody').html('<tr><td colspan="4" class="text-center text-muted">Detalles de productos no disponibles en modo consulta</td></tr>');
                    
                    // Mostrar el modal
                    $('#modalDetallePedido').modal('show');
                } else {
                    alert('No se pudo encontrar el pedido seleccionado');
                }
            };
        });
    </script>
</body>
</html>