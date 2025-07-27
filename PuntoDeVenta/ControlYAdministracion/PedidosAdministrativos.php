<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    include_once "Controladores/ControladorUsuario.php";
}

// Variable para identificar la página actual
$currentPage = 'pedidos-administrativos';

// Obtener el tipo de usuario actual
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';

// Verificar si el usuario tiene permisos de administrador
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pedidos Administrativos - <?php echo $row['Licencia']?></title>
    <meta content="" name="keywords">
    <meta content="" name="description">
    
    <?php include "header.php";?>
    
    <!-- Estilos específicos para pedidos administrativos -->
    <style>
        .card-stats {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .producto-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            background: #fff;
            transition: all 0.3s ease;
        }
        .producto-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .producto-card.border-success {
            border-color: #28a745 !important;
        }
        .producto-card.border-warning {
            border-color: #ffc107 !important;
        }
        .drag-handle {
            cursor: move;
            color: #6c757d;
        }
        .carrito-indicator {
            position: relative;
        }
        .carrito-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .loading-spinner {
            text-align: center;
            color: white;
        }
        .encargo-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .encargo-item .badge {
            background: rgba(255,255,255,0.2);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Sidebar Start -->
    <?php include_once "Menu.php" ?>
    <!-- Sidebar End -->

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php";?>
        <!-- Navbar End -->

        <!-- Main Content Start -->
        <div class="container-fluid pt-4 px-4">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="mb-2 text-primary">
                                    <i class="fas fa-shopping-cart me-3" style="color: #ef7980!important;"></i>
                                    Pedidos Administrativos
                                </h1>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-building me-2"></i>
                                    Sucursal: <?php echo isset($row['Nombre_Sucursal']) ? $row['Nombre_Sucursal'] : 'N/A'; ?>
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary carrito-indicator" data-bs-toggle="modal" data-bs-target="#modalNuevoPedido">
                                    <i class="fas fa-plus me-2"></i>Nuevo Pedido
                                    <span class="carrito-badge" id="carrito-badge" style="display: none;">0</span>
                                </button>
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalStockBajo">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Bajo Stock
                                </button>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalEncargos">
                                    <i class="fas fa-file-invoice me-2"></i>Encargos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Informativas -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 card-stats">
                        <i class="fa fa-shopping-cart fa-3x text-primary"></i>
                        <div class="ms-3">
                            <p class="mb-2">Pedidos Generados</p>
                            <h6 class="mb-0" id="total-pedidos">0</h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 card-stats">
                        <i class="fa fa-clock fa-3x text-warning"></i>
                        <div class="ms-3">
                            <p class="mb-2">En Espera</p>
                            <h6 class="mb-0" id="pedidos-espera">0</h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 card-stats">
                        <i class="fa fa-check-circle fa-3x text-success"></i>
                        <div class="ms-3">
                            <p class="mb-2">Completados</p>
                            <h6 class="mb-0" id="pedidos-completados">0</h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 card-stats">
                        <i class="fa fa-dollar-sign fa-3x text-info"></i>
                        <div class="ms-3">
                            <p class="mb-2">Total Hoy</p>
                            <h6 class="mb-0" id="total-hoy">$0.00</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros y búsqueda -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filtro-estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro-estado">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="completado">Completado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro-fecha-inicio" class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" id="filtro-fecha-inicio">
                </div>
                <div class="col-md-3">
                    <label for="filtro-fecha-fin" class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" id="filtro-fecha-fin">
                </div>
                <div class="col-md-3">
                    <label for="busqueda-pedidos" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="busqueda-pedidos" placeholder="Buscar por folio, solicitante...">
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalNuevoPedido">
                        <i class="fas fa-plus me-2"></i>Nuevo Pedido
                    </button>
                    <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#modalStockBajo">
                        <i class="fas fa-exclamation-triangle me-2"></i>Bajo Stock
                    </button>
                    <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#modalEncargos">
                        <i class="fas fa-clipboard-list me-2"></i>Encargos
                    </button>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#FiltroEspecifico">
                        <i class="fas fa-exchange-alt me-2"></i>Cambiar Sucursal
                    </button>
                    <button type="button" class="btn btn-success" onclick="pedidosAdmin.exportarPedidos()">
                        <i class="fas fa-download me-2"></i>Exportar
                    </button>
                </div>
            </div>

            <!-- Listado de Pedidos -->
            <div class="row">
                <div class="col-12">
                    <div class="bg-light rounded h-100 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0">Listado de Pedidos Administrativos</h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="pedidosAdmin.exportarPedidos()">
                                    <i class="fas fa-download me-1"></i>Exportar
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="pedidosAdmin.refrescarPedidos()">
                                    <i class="fas fa-sync-alt me-1"></i>Refrescar
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover" id="tabla-pedidos">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Folio</th>
                                        <th>Fecha</th>
                                        <th>Productos</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Solicitante</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-pedidos">
                                    <!-- Los pedidos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginación -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <span class="text-muted">Mostrando <span id="mostrando-inicio">0</span> a <span id="mostrando-fin">0</span> de <span id="total-registros">0</span> pedidos</span>
                            </div>
                            <nav>
                                <ul class="pagination mb-0" id="paginacion">
                                    <!-- La paginación se generará dinámicamente -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Content End -->
    </div>
    <!-- Content End -->

    <!-- Modal: Nuevo Pedido -->
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
                                        <button class="btn btn-info" type="button" id="btnBuscarEncargosNuevo">
                                            <i class="fas fa-history"></i>
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
                        
                        <!-- Columna derecha: Productos seleccionados -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Productos del Pedido
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="productos-pedido-nuevo" style="max-height: 400px; overflow-y: auto;">
                                        <!-- Los productos seleccionados se mostrarán aquí -->
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Total: <span id="total-pedido-nuevo">$0.00</span></h6>
                                        <button class="btn btn-primary" onclick="pedidosAdmin.abrirResumenPedido()">
                                            <i class="fas fa-eye me-2"></i>Ver Resumen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="pedidosAdmin.guardarPedido()">
                        <i class="fas fa-save me-2"></i>Guardar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Stock Bajo -->
    <div class="modal fade" id="modalStockBajo" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        Productos con Bajo Stock
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div id="productos-stock-bajo">
                                <!-- Los productos con bajo stock se mostrarán aquí -->
                            </div>
                            
                            <!-- Paginación para stock bajo -->
                            <div class="d-flex justify-content-center mt-3">
                                <nav>
                                    <ul class="pagination" id="paginacion-stock-bajo">
                                        <!-- La paginación se generará dinámicamente -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="pedidosAdmin.agregarTodosStockBajo()">
                        <i class="fas fa-plus me-2"></i>Agregar Todos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Encargos -->
    <div class="modal fade" id="modalEncargos" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-invoice me-2 text-info"></i>
                        Encargos Disponibles
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div id="encargos-disponibles">
                                <!-- Los encargos se mostrarán aquí -->
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

    <!-- Modal: Resumen de Pedido -->
    <div class="modal fade" id="modalResumenPedido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Resumen del Pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="resumen-productos">
                        <!-- El resumen se mostrará aquí -->
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="observaciones-pedido" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones-pedido" rows="3" 
                                      placeholder="Observaciones adicionales..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="prioridad-pedido" class="form-label">Prioridad</label>
                            <select class="form-select" id="prioridad-pedido">
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="pedidosAdmin.confirmarPedido()">
                        <i class="fas fa-check me-2"></i>Confirmar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Detalles de Pedido -->
    <div class="modal fade" id="modalDetallesPedido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Detalles del Pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detalles-pedido">
                        <!-- Los detalles se mostrarán aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btn-aprobar-pedido" style="display: none;">
                        <i class="fas fa-check me-2"></i>Aprobar
                    </button>
                    <button type="button" class="btn btn-danger" id="btn-cancelar-pedido" style="display: none;">
                        <i class="fas fa-times me-2"></i>Cancelar
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
            <p class="mt-2">Procesando...</p>
        </div>
    </div>

    <!-- Modal de Cambio de Sucursal -->
    <div class="modal fade bd-example-modal-xl" id="FiltroEspecifico" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-notify modal-success">
            <div class="modal-content">
                <div class="text-center">
                    <div class="modal-header" style="background-color: #ef7980 !important;">
                        <h5 class="modal-title" style="color:white;" id="exampleModalLabel">Cambiar de sucursal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="white-text">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <form action="javascript:void(0)" method="post" id="CambiaDeSucursal">
                            <div class="row">
                                <div class="col">
                                    <label for="exampleFormControlInput1">Sucursal Actual</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="Tarjeta2"><i class="far fa-hospital"></i></span>
                                        </div>
                                        <input type="text" class="form-control" disabled readonly value="<?php echo $row['Nombre_Sucursal']?>">
                                    </div>
                                </div>
                                
                                <div class="col">
                                    <label for="exampleFormControlInput1">Sucursal a elegir</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="Tarjeta2"><i class="far fa-hospital"></i></span>
                                        </div>
                                        <select id="sucursal" class="form-control" name="Sucursal" required>
                                            <option value="">Seleccione una Sucursal:</option>
                                            <?php 
                                                $query = $conn->query("SELECT ID_Sucursal,Nombre_Sucursal,Licencia FROM Sucursales WHERE Licencia='".$row['Licencia']."'");
                                                while ($valores = mysqli_fetch_array($query)) {
                                                    echo '<option value="'.$valores["ID_Sucursal"].'">'.$valores["Nombre_Sucursal"].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <input type="text" name="user" hidden value="<?php echo $row['Id_PvUser']?>">
                                </div>
                            </div>
                            <button type="submit" id="submit_registroarea" value="Guardar" class="btn btn-success">
                                Aplicar cambio de sucursal <i class="fas fa-exchange-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="js/productos-module.js"></script>
    <script src="js/pedidos-administrativos.js"></script>
    <script src="js/RealizaCambioDeSucursalPorFiltro.js"></script>
    
    <script>
        // Inicializar el sistema cuando el documento esté listo
        $(document).ready(function() {
            pedidosAdmin = new PedidosAdministrativos();
            pedidosAdmin.init();
        });
    </script>
</body>
</html> 