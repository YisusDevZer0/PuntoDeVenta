<?php
include_once "Controladores/ControladorUsuario.php";
include "Controladores/db_connect.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Gestión de Pedidos - <?php echo $row['Licencia']?> - Sucursal <?php echo $row['Nombre_Sucursal']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
    
    <!-- CSS adicional para el sistema de pedidos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        .pedidos-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
        }
        
        .pedido-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 4px solid #667eea;
        }
        
        .pedido-item:hover {
            transform: translateX(5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            border-left-color: #764ba2;
        }
        
        .estado-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        
        .filtros-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        
        .btn-modern {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .search-box {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .search-box:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .producto-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 3px solid #667eea;
        }
        
        .producto-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .producto-card.border-success {
            border-left-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
        }
        
        .drag-handle {
            cursor: move;
            color: #6c757d;
            margin-right: 10px;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
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
        
        /* Estilos adicionales para mejorar la experiencia */
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 5px;
            width: 12px;
            height: 12px;
            background: #667eea;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #667eea;
        }
        
        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 3px solid #667eea;
        }
        
        /* Animaciones suaves */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Mejoras para los botones de acción */
        .btn-group .btn {
            border-radius: 20px;
            margin: 0 2px;
            transition: all 0.3s ease;
        }
        
        .btn-group .btn:hover {
            transform: scale(1.1);
        }
        
        /* Estilos para el resumen del pedido */
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        /* Mejoras para las tablas */
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .pedidos-container {
                padding: 10px;
            }
            
            .stats-card {
                margin-bottom: 15px;
                padding: 15px;
            }
            
            .btn-modern {
                padding: 8px 20px;
                font-size: 14px;
            }
        }
        
        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
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
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="text-white mb-0">
                                <i class="fas fa-shopping-cart me-3"></i>
                                Sistema de Gestión de Pedidos
                            </h2>
                            <button class="btn btn-light btn-modern" id="btnNuevoPedido">
                                <i class="fas fa-plus me-2"></i>Nuevo Pedido
                            </button>
                        </div>
                    </div>
                    
                    <!-- Tarjetas de estadísticas -->
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h4 id="stats-pendientes">0</h4>
                            <p class="mb-0">Pendientes</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h4 id="stats-aprobados">0</h4>
                            <p class="mb-0">Aprobados</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-truck fa-2x mb-2"></i>
                            <h4 id="stats-proceso">0</h4>
                            <p class="mb-0">En Proceso</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                            <h4 id="stats-total">$0</h4>
                            <p class="mb-0">Total Estimado</p>
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
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary btn-modern" id="btnFiltrar">
                                            <i class="fas fa-filter me-2"></i>Filtrar
                                        </button>
                                        <button class="btn btn-secondary btn-modern" id="btnLimpiar">
                                            <i class="fas fa-times me-2"></i>Limpiar
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
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm" id="btnRefresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" id="btnStockBajo">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Stock Bajo
                                    </button>
                                </div>
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
                                <button class="btn btn-primary btn-modern" id="btnCrearPrimerPedido">
                                    <i class="fas fa-plus me-2"></i>Crear Primer Pedido
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para nuevo pedido -->
    <div class="modal fade" id="modalNuevoPedido" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Nuevo Pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Buscar Productos</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="busqueda-producto" placeholder="Escribe el nombre, código o clave del producto...">
                                    <button class="btn btn-outline-secondary" type="button" id="btnBuscarProducto">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="resultados-busqueda" class="mb-3">
                                <!-- Resultados de búsqueda se mostrarán aquí -->
                            </div>
                            
                            <div class="mb-3">
                                <h6>Productos del Pedido</h6>
                                <div id="productos-pedido" class="border rounded p-3" style="min-height: 200px;">
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                        <p>Arrastra productos aquí o busca productos para agregar</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones-pedido" rows="4" placeholder="Observaciones adicionales..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Prioridad</label>
                                <select class="form-control" id="prioridad-pedido">
                                    <option value="baja">Baja</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Resumen del Pedido</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Productos:</span>
                                        <span id="total-productos">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Cantidad:</span>
                                        <span id="total-cantidad">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Estimado:</span>
                                        <span id="total-precio">$0.00</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total:</strong>
                                        <strong id="total-final">$0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarPedido">
                        <i class="fas fa-save me-2"></i>Guardar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para ver detalle de pedido -->
    <div class="modal fade" id="modalDetallePedido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>Detalle del Pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalle-pedido-content">
                    <!-- Contenido del detalle se cargará aquí -->
                </div>
                <div class="modal-footer" id="detalle-pedido-actions">
                    <!-- Botones de acción se cargarán aquí -->
                </div>
            </div>
        </div>
    </div>
    
    <?php include "Footer.php"; ?>
    
    <!-- Scripts adicionales -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="js/pedidos-modern.js"></script>
</body>
</html> 