<?php
// Incluir controlador de usuario
include_once "Controladores/ControladorUsuario.php";

// Verificar permisos de administrador
if (!isset($row) || !isset($row['TipoUsuario'])) {
    if (!function_exists('getUserData')) {
        include_once "Controladores/ControladorUsuario.php";
    }
}

$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

if (!$isAdmin) {
    header('Location: index.php');
    exit();
}

// Variables para el menú
$currentPage = 'caducados';
$showDashboard = false;
$disabledAttr = '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Caducados - Doctor Pez</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        .card-stat {
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }
        .card-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-stat.warning { border-left-color: #ffc107; }
        .card-stat.danger { border-left-color: #dc3545; }
        .card-stat.success { border-left-color: #28a745; }
        .card-stat.info { border-left-color: #17a2b8; }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .badge-caducidad {
            font-size: 0.8em;
            padding: 0.4em 0.6em;
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .sidebar {
            background: #f8f9fa;
        }
        
        .main-content {
            background: #ffffff;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <?php include 'Menu.php'; ?>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="container-fluid py-4">
                    <!-- Header -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1"><i class="fa-solid fa-calendar-times text-primary me-2"></i>Control de Caducados</h2>
                                    <p class="text-muted mb-0">Gestión de lotes y fechas de caducidad</p>
                                </div>
                                <div>
                                    <button class="btn btn-primary me-2" onclick="abrirModalRegistrarLote()">
                                        <i class="fa-solid fa-plus me-1"></i>Registrar Lote
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="abrirModalConfiguracion()">
                                        <i class="fa-solid fa-cog me-1"></i>Configuración
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cards de Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card card-stat warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-1">3 Meses</h6>
                                            <h3 class="mb-0" id="contador-3-meses">0</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-exclamation-triangle text-warning fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-stat danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-1">6 Meses</h6>
                                            <h3 class="mb-0" id="contador-6-meses">0</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-exclamation-circle text-danger fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-stat info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-1">9 Meses</h6>
                                            <h3 class="mb-0" id="contador-9-meses">0</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-info-circle text-info fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-stat success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-1">Total Lotes</h6>
                                            <h3 class="mb-0" id="contador-total">0</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-boxes-stacked text-success fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Sucursal</label>
                                            <select class="form-select" id="filtro-sucursal">
                                                <option value="">Todas las sucursales</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select" id="filtro-estado">
                                                <option value="">Todos los estados</option>
                                                <option value="activo">Activo</option>
                                                <option value="agotado">Agotado</option>
                                                <option value="vencido">Vencido</option>
                                                <option value="retirado">Retirado</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Tipo de Alerta</label>
                                            <select class="form-select" id="filtro-alerta">
                                                <option value="">Todas las alertas</option>
                                                <option value="3_meses">3 Meses</option>
                                                <option value="6_meses">6 Meses</option>
                                                <option value="9_meses">9 Meses</option>
                                                <option value="vencido">Vencido</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="d-grid">
                                                <button class="btn btn-primary" onclick="aplicarFiltros()">
                                                    <i class="fa-solid fa-filter me-1"></i>Filtrar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de Productos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa-solid fa-list me-2"></i>Productos por Caducidad</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="tabla-caducados">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Producto</th>
                                                    <th>Lote</th>
                                                    <th>Fecha Caducidad</th>
                                                    <th>Cantidad</th>
                                                    <th>Sucursal</th>
                                                    <th>Estado</th>
                                                    <th>Alerta</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-caducados">
                                                <!-- Los datos se cargarán dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modales -->
    <?php include 'Modales/RegistrarLote.php'; ?>
    <?php include 'Modales/ActualizarCaducidad.php'; ?>
    <?php include 'Modales/TransferirLote.php'; ?>
    <?php include 'Modales/DetallesLote.php'; ?>
    <?php include 'Modales/ConfiguracionCaducados.php'; ?>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/caducados.js"></script>
    
    <script>
        // Inicializar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarEstadisticas();
            cargarSucursales();
            cargarProductosCaducados();
        });
    </script>
</body>
</html>
