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
    <link href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" rel="stylesheet">
    
    <style>
        /* Estilos homologados con el resto de la aplicación */
        body {
            background-color: #f8f9fa;
        }
        
        .pedidos-container {
            background: #f8f9fa;
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
        
        .stats-total {
            background: #6f42c1; /* Morado para total */
        }
        
        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        
        /* Hover effects por prioridad */
        .pedido-item.prioridad-baja:hover {
            border-left-color: #20c997;
        }
        
        .pedido-item.prioridad-normal:hover {
            border-left-color: #007bff;
        }
        
        .pedido-item.prioridad-alta:hover {
            border-left-color: #e0a800;
        }
        
        .pedido-item.prioridad-urgente:hover {
            border-left-color: #c82333;
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
        
        .producto-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease-in-out;
            border-left: 3px solid var(--primary);
        }
        
        .producto-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .producto-card.border-success {
            border-left-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
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
            background: var(--primary);
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--primary);
        }
        
        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid var(--primary);
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
            border-radius: 8px;
            margin: 0 2px;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-group .btn:hover {
            transform: scale(1.05);
        }
        
        /* Estilos para el resumen del pedido */
        .card {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: var(--primary);
            color: white;
            border-radius: 8px 8px 0 0;
            border: none;
        }
        
        /* Mejoras para las tablas */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table thead th {
            background: var(--primary);
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
            background: var(--primary);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #007bff;
        }
        
        /* Estilos específicos para el sistema de pedidos */
        .page-header {
            background: var(--primary);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .page-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #007bff;
            border-color: #007bff;
        }

        /* Efectos hover mejorados */
        .pedido-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .producto-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        /* Mejoras en botones */
        .btn {
            transition: all 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        /* Indicador de carga mejorado */
        #loading-overlay {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
        }
        
        /* Mejoras en modales */
        .modal-content {
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            border-bottom: 1px solid #e9ecef;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        /* Mejoras en las notificaciones */
        .swal2-popup {
            border-radius: 12px;
        }
        
        /* Responsive mejorado */
        @media (max-width: 768px) {
            .action-buttons .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
        }
        
        /* Estilos específicos para modo solo consulta */
        .readonly-mode {
            opacity: 0.8;
        }
        
        .readonly-mode .btn-primary {
            background-color: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
        }
        
        .readonly-mode .btn-primary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            transform: none;
        }
        
        .disabled-feature {
            opacity: 0.5;
            pointer-events: none;
        }
        
        .info-badge {
            background: #17a2b8;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
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
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="page-title">
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
                <div class="stats-grid">
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
                <div class="action-buttons mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" id="btnNuevoPedido">
                                <i class="fas fa-plus me-2"></i>Nuevo Pedido
                            </button>
                            <button class="btn btn-info" id="btnStockBajo">
                                <i class="fas fa-exclamation-triangle me-2"></i>Stock Bajo
                            </button>
                            <button class="btn btn-secondary" id="btnListadoPedidos">
                                <i class="fas fa-list me-2"></i>Listado Completo
                            </button>
                            <button class="btn btn-secondary" id="btnRefresh">
                                <i class="fas fa-sync-alt me-2"></i>Actualizar
                            </button>
                        </div>
                        
                        <!-- Información de sucursal -->
                        <div class="d-flex align-items-center">
                            <div class="info-badge">
                                <i class="fas fa-building me-1"></i>
                                <?php echo $row['Nombre_Sucursal']?>
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
                                <div class="info-badge">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Hacer clic para ver detalles - Solo lectura después de creado
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
                    
                    <div class="row mt-3">
            <div class="col-12">
                            <h6>Historial de Cambios</h6>
                            <div class="timeline" id="detalle-historial">
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

    <!-- Modal de Stock Bajo -->
    <div class="modal fade" id="modalStockBajo" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        Productos con Stock Bajo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="stock-bajo-content">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Listado de Pedidos -->
    <div class="modal fade" id="modalListadoPedidos" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-list me-2"></i>
                        Listado Completo de Pedidos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="lista-pedidos-modal">
                        <!-- Los pedidos se cargarán aquí dinámicamente -->
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
                                            <p>Busca productos para agregar al pedido</p>
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
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando pedidos...</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Script para gestión de pedidos
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Sistema de gestión de pedidos activado');
            
            // Variables globales
            let productosPedido = [];
            let totalProductos = 0;
            let totalCantidad = 0;
            let totalPrecio = 0;
            
            // Event listeners
            document.getElementById('btnRefresh').addEventListener('click', function() {
                location.reload();
            });
            
            // Botón nuevo pedido
            document.getElementById('btnNuevoPedido').addEventListener('click', function() {
                $('#modalNuevoPedido').modal('show');
            });
            
            // Botón crear primer pedido
            document.getElementById('btnCrearPrimerPedido').addEventListener('click', function() {
                $('#modalNuevoPedido').modal('show');
            });
            
            // Botón listado de pedidos
            document.getElementById('btnListadoPedidos').addEventListener('click', function() {
                $('#modalListadoPedidos').modal('show');
            });
            
            // Botón stock bajo
            document.getElementById('btnStockBajo').addEventListener('click', function() {
                $('#modalStockBajo').modal('show');
            });
            
            // Botón buscar producto
            document.getElementById('btnBuscarProductoNuevo').addEventListener('click', function() {
                buscarProductos();
            });
            
            // Enter en búsqueda de productos
            document.getElementById('busqueda-producto-nuevo').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    buscarProductos();
                }
            });
            
            // Botón limpiar pedido
            document.getElementById('btnLimpiarPedidoNuevo').addEventListener('click', function() {
                limpiarPedido();
            });
            
            // Botón guardar pedido
            document.getElementById('btnGuardarPedidoNuevo').addEventListener('click', function() {
                guardarPedido();
            });
            
            // Filtros
            document.getElementById('btnAplicarFiltros').addEventListener('click', function() {
                cargarPedidos();
            });
            
            document.getElementById('btnLimpiarFiltros').addEventListener('click', function() {
                document.getElementById('filtro-estado').value = '';
                document.getElementById('filtro-fecha-inicio').value = '';
                document.getElementById('filtro-fecha-fin').value = '';
                document.getElementById('busqueda').value = '';
                cargarPedidos();
            });
            
            // Búsqueda en tiempo real
            document.getElementById('busqueda').addEventListener('input', function() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    cargarPedidos();
                }, 500);
            });
            
            // Funciones
            function buscarProductos() {
                const busqueda = document.getElementById('busqueda-producto-nuevo').value;
                if (busqueda.trim() === '') {
                    alert('Por favor ingrese un término de búsqueda');
                    return;
                }
                
                // Mostrar loading
                const resultados = document.getElementById('resultados-busqueda-nuevo');
                resultados.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p class="mt-2">Buscando productos...</p></div>';
                
                // Realizar búsqueda real
                const formData = new FormData();
                formData.append('query', busqueda);
                
                fetch('api/buscar_productos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarResultadosBusqueda(data.productos);
                    } else {
                        resultados.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultados.innerHTML = '<div class="alert alert-danger">Error al buscar productos</div>';
                });
            }
            
            function mostrarResultadosBusqueda(productos) {
                const resultados = document.getElementById('resultados-busqueda-nuevo');
                
                if (productos.length === 0) {
                    resultados.innerHTML = '<div class="alert alert-info">No se encontraron productos</div>';
                    return;
                }
                
                let html = '';
                productos.forEach(producto => {
                    const stockClass = producto.stock_status === 'bajo' ? 'text-warning' : 
                                     producto.stock_status === 'agotado' ? 'text-danger' : 'text-success';
                    
                    html += `
                        <div class="producto-card" data-producto='${JSON.stringify(producto)}'>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">${producto.nombre}</h6>
                                    <small class="text-muted">Precio: $${producto.precio.toFixed(2)} | Stock: <span class="${stockClass}">${producto.existencias}</span></small>
                                    ${producto.codigo_barras ? `<br><small class="text-muted">Código: ${producto.codigo_barras}</small>` : ''}
                                </div>
                                <button class="btn btn-sm btn-primary" onclick="agregarProducto(this)">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                resultados.innerHTML = html;
            }
            
            function agregarProducto(boton) {
                const productoCard = boton.closest('.producto-card');
                const productoData = JSON.parse(productoCard.dataset.producto);
                
                // Verificar si ya existe
                const existe = productosPedido.find(p => p.id === productoData.id);
                if (existe) {
                    existe.cantidad += 1;
                } else {
                    productosPedido.push({
                        ...productoData,
                        cantidad: 1
                    });
                }
                
                actualizarResumen();
                actualizarListaProductos();
            }
            
            function actualizarListaProductos() {
                const container = document.getElementById('productos-pedido-nuevo');
                
                if (productosPedido.length === 0) {
                    container.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <p>Busca productos para agregar al pedido</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                productosPedido.forEach((producto, index) => {
                    html += `
                        <div class="producto-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">${producto.nombre}</h6>
                                    <small class="text-muted">Precio: $${producto.precio.toFixed(2)} | Cantidad: ${producto.cantidad}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="cambiarCantidad(${index}, -1)">-</button>
                                    <span class="mx-2">${producto.cantidad}</span>
                                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="cambiarCantidad(${index}, 1)">+</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarProducto(${index})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
            }
            
            function cambiarCantidad(index, cambio) {
                productosPedido[index].cantidad += cambio;
                if (productosPedido[index].cantidad <= 0) {
                    productosPedido.splice(index, 1);
                }
                actualizarResumen();
                actualizarListaProductos();
            }
            
            function eliminarProducto(index) {
                productosPedido.splice(index, 1);
                actualizarResumen();
                actualizarListaProductos();
            }
            
            function actualizarResumen() {
                totalProductos = productosPedido.length;
                totalCantidad = productosPedido.reduce((sum, p) => sum + p.cantidad, 0);
                totalPrecio = productosPedido.reduce((sum, p) => sum + (p.precio * p.cantidad), 0);
                
                document.getElementById('total-productos-nuevo').textContent = totalProductos;
                document.getElementById('total-cantidad-nuevo').textContent = totalCantidad;
                document.getElementById('total-precio-nuevo').textContent = `$${totalPrecio.toFixed(2)}`;
            }
            
            function limpiarPedido() {
                productosPedido = [];
                document.getElementById('observaciones-pedido-nuevo').value = '';
                document.getElementById('prioridad-pedido-nuevo').value = 'normal';
                actualizarResumen();
                actualizarListaProductos();
            }
            
            function guardarPedido() {
                if (productosPedido.length === 0) {
                    alert('Debe agregar al menos un producto al pedido');
                    return;
                }
                
                const observaciones = document.getElementById('observaciones-pedido-nuevo').value;
                const prioridad = document.getElementById('prioridad-pedido-nuevo').value;
                
                // Mostrar loading
                const btnGuardar = document.getElementById('btnGuardarPedidoNuevo');
                const textoOriginal = btnGuardar.innerHTML;
                btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
                btnGuardar.disabled = true;
                
                // Preparar datos para envío
                const formData = new FormData();
                formData.append('productos', JSON.stringify(productosPedido));
                formData.append('observaciones', observaciones);
                formData.append('prioridad', prioridad);
                
                // Realizar guardado
                console.log('Enviando pedido:', {
                    productos: productosPedido,
                    observaciones: observaciones,
                    prioridad: prioridad
                });
                
                fetch('api/guardar_pedido.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Respuesta de guardar pedido:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Datos de guardar pedido:', data);
                    if (data.success) {
                        alert(`Pedido guardado exitosamente\nFolio: ${data.folio}`);
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
                    btnGuardar.innerHTML = textoOriginal;
                    btnGuardar.disabled = false;
                });
            }
            
            // Funciones para cargar datos
            function cargarEstadisticas() {
                console.log('Cargando estadísticas...');
                fetch('api/estadisticas_pedidos.php')
                .then(response => {
                    console.log('Respuesta de estadísticas:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Datos de estadísticas:', data);
                    if (data.success) {
                        document.getElementById('stats-pendientes').textContent = data.estadisticas.pendientes;
                        document.getElementById('stats-aprobados').textContent = data.estadisticas.aprobados;
                        document.getElementById('stats-proceso').textContent = data.estadisticas.en_proceso;
                    } else {
                        console.error('Error en estadísticas:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al cargar estadísticas:', error);
                });
            }
            
            function cargarPedidos() {
                console.log('Cargando pedidos...');
                const formData = new FormData();
                formData.append('estado', document.getElementById('filtro-estado').value);
                formData.append('fecha_inicio', document.getElementById('filtro-fecha-inicio').value);
                formData.append('fecha_fin', document.getElementById('filtro-fecha-fin').value);
                formData.append('busqueda', document.getElementById('busqueda').value);
                
                fetch('api/listar_pedidos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Respuesta de pedidos:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Datos de pedidos:', data);
                    if (data.success) {
                        // Guardar pedidos en variable global para uso en verDetallePedido
                        window.pedidosActuales = data.pedidos;
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
                const container = document.getElementById('lista-pedidos');
                
                if (pedidos.length === 0) {
                    document.getElementById('empty-state').style.display = 'block';
                    container.innerHTML = '';
                    return;
                }
                
                document.getElementById('empty-state').style.display = 'none';
                
                let html = '';
                pedidos.forEach(pedido => {
                    const estadoClass = `estado-${pedido.estado}`;
                    const prioridadClass = `prioridad-${pedido.prioridad}`;
                    
                    // Formatear fecha
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
                
                container.innerHTML = html;
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
            
            function verDetallePedido(pedidoId) {
                console.log('Ver detalle del pedido:', pedidoId);
                
                // Buscar el pedido en la lista actual
                const pedidos = window.pedidosActuales || [];
                const pedido = pedidos.find(p => p.id == pedidoId);
                
                if (pedido) {
                    // Llenar el modal con los datos del pedido
                    document.getElementById('detalle-pedido-folio').textContent = pedido.folio;
                    document.getElementById('detalle-pedido-estado').innerHTML = `<span class="estado-badge estado-${pedido.estado}">${pedido.estado}</span>`;
                    document.getElementById('detalle-pedido-fecha').textContent = new Date(pedido.fecha_creacion).toLocaleString('es-ES');
                    document.getElementById('detalle-pedido-usuario').textContent = pedido.usuario_nombre;
                    document.getElementById('detalle-pedido-sucursal').textContent = pedido.sucursal_nombre || 'Farmacia';
                    document.getElementById('detalle-pedido-total').textContent = `$${pedido.total_estimado.toFixed(2)}`;
                    document.getElementById('detalle-pedido-observaciones').textContent = pedido.observaciones || 'Sin observaciones';
                    
                    // Limpiar tabla de productos
                    document.getElementById('detalle-productos-tbody').innerHTML = '<tr><td colspan="4" class="text-center text-muted">Cargando productos...</td></tr>';
                    
                    // Mostrar el modal
                    $('#modalDetallePedido').modal('show');
                    
                    // Aquí podrías hacer una llamada AJAX para obtener los detalles completos del pedido
                    // Por ahora solo mostramos la información básica
                    document.getElementById('detalle-productos-tbody').innerHTML = '<tr><td colspan="4" class="text-center text-muted">Detalles de productos no disponibles en modo consulta</td></tr>';
                } else {
                    alert('No se pudo encontrar el pedido seleccionado');
                }
            }
            
            // Hacer funciones globales para los botones inline
            window.agregarProducto = agregarProducto;
            window.cambiarCantidad = cambiarCantidad;
            window.eliminarProducto = eliminarProducto;
            
            // Instalar tablas si no existen y cargar datos iniciales
            function inicializarSistema() {
                // Primero verificar sesión
                fetch('api/test_apis.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Verificación de sesión:', data);
                    if (data.success) {
                        // Luego instalar tablas si es necesario
                        return fetch('api/instalar_tablas_pedidos.php');
                    } else {
                        throw new Error('Sesión no válida');
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Instalación de tablas:', data);
                    // Luego cargar datos
                    cargarEstadisticas();
                    cargarPedidos();
                    document.getElementById('loading-spinner').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error al inicializar sistema:', error);
                    // Intentar cargar datos de todas formas
                    cargarEstadisticas();
                    cargarPedidos();
                    document.getElementById('loading-spinner').style.display = 'none';
                });
            }
            
            // Inicializar sistema
            setTimeout(inicializarSistema, 1000);
        });
    </script>
</body>
</html>
