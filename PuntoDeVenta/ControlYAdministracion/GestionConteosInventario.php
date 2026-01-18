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
    
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    
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
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm" onclick="CargarProductosContados()">
                                <i class="fa-solid fa-refresh me-1"></i>Actualizar
                            </button>
                            <?php if ($isAdmin): ?>
                            <button class="btn btn-warning btn-sm" id="btn-liberar-productos" onclick="mostrarModalLiberarProductos()">
                                <i class="fa-solid fa-unlock me-1"></i>Liberar Productos Contados
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
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
            </div>
        </div>
            
<script src="js/GestionConteosInventario.js"></script>

        <!-- Footer Start -->
        <?php 
        include "Modales/Modales_Errores.php";
        include "Modales/Modales_Referencias.php";
        include "Footer.php";?>

<script>
$(document).ready(function() {
    // Cargar datos iniciales
    CargarSucursales();
    CargarUsuarios();
    
    // NO establecer fechas por defecto - dejar vacías para mostrar todos los registros
    // Si el usuario quiere filtrar por fecha, puede seleccionarlas manualmente
    
    // Cargar productos después de un pequeño delay para asegurar que los selects están cargados
    setTimeout(function() {
        CargarProductosContados();
    }, 300);
});
</script>

</body>

</html>
