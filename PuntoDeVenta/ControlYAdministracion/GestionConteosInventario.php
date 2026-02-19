<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar si el usuario es administrador
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : '';
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Gestión de Conteos de Inventario - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php";?>
    
    <!-- Cargar script antes del contenido para que las funciones estén disponibles -->
    <script src="js/GestionConteosInventario.js"></script>
    
    <style>
        .card-stat {
            transition: transform 0.2s;
        }
        .card-stat:hover {
            transform: translateY(-5px);
        }
        .producto-completado {
            background-color: #d4edda;
        }
        .producto-con-diferencia {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .producto-sin-diferencia {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
        }
    </style>
</head>

<body>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    
    <?php include_once "Menu.php" ?>

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php";?>
        <!-- Navbar End -->

        <!-- Table Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="mb-0" style="color:#0172b6;">
                            <i class="fa-solid fa-clipboard-check me-2"></i>
                            Gestión de Conteos de Inventario - <?php echo $row['Licencia']?>
                        </h6>
                        <div class="d-flex gap-2" id="panelBotonesProductos">
                            <button class="btn btn-primary btn-sm" onclick="CargarProductosContados()">
                                <i class="fa-solid fa-refresh me-1"></i>Actualizar
                            </button>
                            <button class="btn btn-success btn-sm" onclick="ExportarConteosInventario()">
                                <i class="fa-solid fa-file-excel me-1"></i>Exportar Excel
                            </button>
                            <?php if ($isAdmin): ?>
                            <button class="btn btn-warning btn-sm" id="btn-liberar-productos" onclick="mostrarModalLiberarProductos()">
                                <i class="fa-solid fa-unlock me-1"></i>Liberar Productos Contados
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Pestañas: Configuración | Productos contados -->
                    <ul class="nav nav-tabs mb-3" id="tabGestionConteos" role="tablist">
                        <?php if ($isAdmin): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-config" data-bs-toggle="tab" data-bs-target="#panel-config" type="button" role="tab">Configuración</button>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo !$isAdmin ? 'active' : ''; ?>" id="tab-productos" data-bs-toggle="tab" data-bs-target="#panel-productos" type="button" role="tab">Productos contados</button>
                        </li>
                    </ul>
                    
                    <!-- Panel Configuración (solo admin) -->
                    <?php if ($isAdmin): ?>
                    <div class="tab-pane fade show active" id="panel-config" role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-secondary mb-2"><i class="fa-solid fa-calendar-days me-1"></i> Periodos (fechas en que se permite inventario por turnos)</h6>
                                <p class="small text-muted">Si no hay periodos activos, el inventario se permite cualquier día. Si hay al menos uno, la fecha actual debe estar dentro de un periodo para la sucursal.</p>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="abrirModalPeriodo()"><i class="fa-solid fa-plus me-1"></i>Nuevo periodo</button>
                                </div>
                                <div id="tablaPeriodosContainer"><table class="table table-sm table-bordered" id="tablaPeriodos"><thead><tr><th>Sucursal</th><th>Inicio</th><th>Fin</th><th>Nombre</th><th>Código externo</th><th>Activo</th><th></th></tr></thead><tbody></tbody></table></div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-secondary mb-2"><i class="fa-solid fa-store me-1"></i> Configuración por sucursal</h6>
                                <p class="small text-muted">Máx. turnos por día (0 = sin límite). Máx. productos por turno.</p>
                                <div class="mb-2">
                                    <label class="form-label small">Sucursal</label>
                                    <select class="form-select form-select-sm" id="configSucursalSelect"><option value="0">Global (por defecto)</option></select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Máx. turnos por día</label>
                                    <input type="number" class="form-control form-control-sm" id="configSucursalMaxTurnos" min="0" value="0" placeholder="0 = sin límite">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Máx. productos por turno</label>
                                    <input type="number" class="form-control form-control-sm" id="configSucursalMaxProductos" min="1" value="50">
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" onclick="guardarConfigSucursal()"><i class="fa-solid fa-save me-1"></i>Guardar</button>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary mb-2"><i class="fa-solid fa-user me-1"></i> Configuración por empleado</h6>
                                <p class="small text-muted">0 = usar límite de la sucursal.</p>
                                <div class="mb-2">
                                    <label class="form-label small">Empleado</label>
                                    <select class="form-select form-select-sm" id="configEmpleadoUsuario"><option value="">-- Seleccionar --</option></select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Sucursal (0 = todas)</label>
                                    <select class="form-select form-select-sm" id="configEmpleadoSucursal"><option value="0">Todas</option></select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Máx. turnos por día</label>
                                    <input type="number" class="form-control form-control-sm" id="configEmpleadoMaxTurnos" min="0" value="0">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Máx. productos por turno</label>
                                    <input type="number" class="form-control form-control-sm" id="configEmpleadoMaxProductos" min="0" value="0">
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" onclick="guardarConfigEmpleado()"><i class="fa-solid fa-save me-1"></i>Guardar</button>
                                <div id="listaConfigEmpleados" class="mt-3 small"></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Panel Productos contados -->
                    <div class="tab-pane fade <?php echo !$isAdmin ? 'show active' : ''; ?>" id="panel-productos" role="tabpanel">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Sucursal:</label>
                            <select class="form-select" id="filtroSucursal" onchange="CargarProductosContados()">
                                <option value="">Todas las sucursales</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Usuario:</label>
                            <select class="form-select" id="filtroUsuario" onchange="CargarProductosContados()">
                                <option value="">Todos los usuarios</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha desde:</label>
                            <input type="date" class="form-control" id="fechaDesde" onchange="CargarProductosContados()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha hasta:</label>
                            <input type="date" class="form-control" id="fechaHasta" onchange="CargarProductosContados()">
                        </div>
                    </div>
                    
                    <!-- Estadísticas Resumen -->
                    <div class="row mb-4" id="estadisticasResumen">
                        <div class="col-md-3">
                            <div class="card card-stat bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Productos Contados</h6>
                                            <h4 id="totalProductos">0</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-boxes-stacked fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stat bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Sin Diferencias</h6>
                                            <h4 id="sinDiferencias">0</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stat bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Con Diferencias</h6>
                                            <h4 id="conDiferencias">0</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stat bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Usuarios Activos</h6>
                                            <h4 id="usuariosActivos">0</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="tablaProductosContados"></div>
                    </div>
                    <!-- fin panel Productos contados -->
                </div>
            </div>
        </div>
            
        <!-- Footer Start -->
        <!-- Modal Periodo -->
        <div class="modal fade" id="modalPeriodo" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Periodo de inventario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="modalPeriodoId" value="">
                        <div class="mb-2">
                            <label class="form-label">Sucursal (0 = global)</label>
                            <select class="form-select form-select-sm" id="modalPeriodoSucursal"><option value="0">Global</option></select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Fecha inicio</label>
                            <input type="date" class="form-control form-control-sm" id="modalPeriodoInicio">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Fecha fin</label>
                            <input type="date" class="form-control form-control-sm" id="modalPeriodoFin">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nombre (opcional)</label>
                            <input type="text" class="form-control form-control-sm" id="modalPeriodoNombre" placeholder="Ej. Semana 1 Feb 2025">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Código externo (opcional)</label>
                            <input type="text" class="form-control form-control-sm" id="modalPeriodoCodigo" placeholder="Para integración">
                        </div>
                        <div class="mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="modalPeriodoActivo" checked>
                                <label class="form-check-label" for="modalPeriodoActivo">Activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="guardarPeriodo()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <?php 
        include "Modales/Modales_Errores.php";
        include "Modales/Modales_Referencias.php";
        include "Footer.php";?>

<script>
var esAdminGestionConteos = <?php echo $isAdmin ? 'true' : 'false'; ?>;
$(document).ready(function() {
    // Verificar que las funciones estén disponibles
    if (typeof CargarSucursales === 'undefined') {
        console.error('Error: CargarSucursales no está definida. Verifique que GestionConteosInventario.js se cargó correctamente.');
        return;
    }
    
    // Cargar datos iniciales
    CargarSucursales();
    CargarUsuarios();
    
    if (esAdminGestionConteos && typeof CargarConfigInventario === 'function') {
        setTimeout(function() {
            CargarConfigInventario();
            if (typeof CargarUsuariosParaConfig === 'function') CargarUsuariosParaConfig();
        }, 500);
    }
    
    // Cargar productos después de un pequeño delay para asegurar que los selects están cargados
    setTimeout(function() {
        CargarProductosContados();
    }, 300);
});

// Asegurar que ExportarConteosInventario esté disponible (respaldo si el JS no se carga)
if (typeof ExportarConteosInventario === 'undefined') {
    window.ExportarConteosInventario = function() {
        alert('El archivo JavaScript no se cargó correctamente. Por favor, recarga la página.');
        console.error('ExportarConteosInventario no está definida');
    };
}
</script>

</body>

</html>
