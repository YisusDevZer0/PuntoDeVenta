<?php
// Versión optimizada del control de bitácoras de limpieza
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include_once "Controladores/ControladorUsuario.php";
    
    // Verificar sesión administrativa
    if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
        header("Location: Expiro.php");
        exit();
    }

    // Verificar que $conn esté disponible
    if (!isset($conn) || !$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Obtener datos básicos directamente para evitar problemas de carga
    $bitacoras = [];
    $sucursales = [];
    $areas = [];
    
    // Consulta simple para bitácoras
    $sql_bitacoras = "SELECT 
                        bl.id_bitacora,
                        bl.area,
                        bl.semana,
                        bl.fecha_inicio,
                        bl.fecha_fin,
                        bl.responsable,
                        bl.supervisor,
                        bl.aux_res,
                        'N/A' as sucursal_id,
                        'Sin Sucursal' as Nombre_Sucursal,
                        NOW() as created_at,
                        NOW() as updated_at,
                        0 as total_elementos,
                        0 as tareas_completadas,
                        0 as total_tareas_posibles,
                        0 as porcentaje_cumplimiento
                      FROM Bitacora_Limpieza bl 
                      ORDER BY bl.fecha_inicio DESC 
                      LIMIT 100";
    
    $result = mysqli_query($conn, $sql_bitacoras);
    if ($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $bitacoras[] = $row;
        }
    }
    
    // Consulta simple para sucursales
    $sql_sucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 'Si' ORDER BY Nombre_Sucursal LIMIT 20";
    $result_sucursales = mysqli_query($conn, $sql_sucursales);
    if ($result_sucursales) {
        while($row = mysqli_fetch_assoc($result_sucursales)) {
            $sucursales[] = $row;
        }
    }
    
    // Consulta simple para áreas
    $sql_areas = "SELECT DISTINCT area FROM Bitacora_Limpieza ORDER BY area LIMIT 20";
    $result_areas = mysqli_query($conn, $sql_areas);
    if ($result_areas) {
        while($row = mysqli_fetch_assoc($result_areas)) {
            $areas[] = $row['area'];
        }
    }
    
    // Estadísticas básicas
    $estadisticas = [
        'total_bitacoras' => count($bitacoras),
        'total_sucursales' => count($sucursales),
        'total_areas' => count($areas),
        'promedio_cumplimiento' => 0
    ];
    
    $bitacorasPorSucursal = [];
    $currentPage = 'bitacora_limpieza';

} catch (Exception $e) {
    // Mostrar error en pantalla para debugging
    die("Error fatal: " . $e->getMessage() . " en línea " . $e->getLine());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Control de Bitácoras de Limpieza - <?php echo $row['Licencia'] ?? 'Sistema'; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php";?>
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

        <!-- Container Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="row g-4">
                <!-- Título y controles -->
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-primary">
                                <i class="fa-solid fa-broom me-2"></i>
                                Control de Bitácoras de Limpieza
                            </h4>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" id="btnNuevaBitacora">
                                    <i class="fa fa-plus me-2"></i>Nueva Bitácora
                                </button>
                                <button type="button" class="btn btn-success" id="btnExportarCSV">
                                    <i class="fa fa-download me-2"></i>Exportar CSV
                                </button>
                                <button type="button" class="btn btn-info" id="btnActualizar">
                                    <i class="fa fa-refresh me-2"></i>Actualizar
                                </button>
                            </div>
                        </div>

                        <!-- Filtros -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="filtroSucursal" class="form-label">Sucursal:</label>
                                <select class="form-select" id="filtroSucursal">
                                    <option value="">Todas las sucursales</option>
                                    <?php foreach($sucursales as $sucursal): ?>
                                        <option value="<?php echo $sucursal['ID_Sucursal']; ?>">
                                            <?php echo htmlspecialchars($sucursal['Nombre_Sucursal']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtroArea" class="form-label">Área:</label>
                                <select class="form-select" id="filtroArea">
                                    <option value="">Todas las áreas</option>
                                    <?php foreach($areas as $area): ?>
                                        <option value="<?php echo htmlspecialchars($area); ?>">
                                            <?php echo htmlspecialchars($area); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtroFechaInicio" class="form-label">Fecha Inicio:</label>
                                <input type="date" class="form-control" id="filtroFechaInicio">
                            </div>
                            <div class="col-md-3">
                                <label for="filtroFechaFin" class="form-label">Fecha Fin:</label>
                                <input type="date" class="form-control" id="filtroFechaFin">
                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-clipboard-list fa-2x text-primary mb-2"></i>
                                        <h5 class="card-title"><?php echo $estadisticas['total_bitacoras']; ?></h5>
                                        <p class="card-text text-muted">Total Bitácoras</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-building fa-2x text-success mb-2"></i>
                                        <h5 class="card-title"><?php echo $estadisticas['total_sucursales']; ?></h5>
                                        <p class="card-text text-muted">Sucursales</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-map-marker-alt fa-2x text-info mb-2"></i>
                                        <h5 class="card-title"><?php echo $estadisticas['total_areas']; ?></h5>
                                        <p class="card-text text-muted">Áreas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-chart-line fa-2x text-warning mb-2"></i>
                                        <h5 class="card-title"><?php echo $estadisticas['promedio_cumplimiento']; ?>%</h5>
                                        <p class="card-text text-muted">Cumplimiento</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de bitácoras -->
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">
                            <i class="fa fa-table me-2"></i>
                            Bitácoras de Limpieza
                        </h5>
                        <div class="table-responsive">
                            <table id="tablaBitacoras" class="table table-bordered table-striped" style="width:100%">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Sucursal</th>
                                        <th>Área</th>
                                        <th>Semana</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Responsable</th>
                                        <th>Supervisor</th>
                                        <th>Elementos</th>
                                        <th>Cumplimiento</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($bitacoras as $bitacora): ?>
                                    <tr>
                                        <td><?php echo $bitacora['id_bitacora']; ?></td>
                                        <td><?php echo $bitacora['Nombre_Sucursal']; ?></td>
                                        <td><?php echo $bitacora['area']; ?></td>
                                        <td><?php echo $bitacora['semana']; ?></td>
                                        <td><?php echo $bitacora['fecha_inicio']; ?></td>
                                        <td><?php echo $bitacora['fecha_fin']; ?></td>
                                        <td><?php echo $bitacora['responsable']; ?></td>
                                        <td><?php echo $bitacora['supervisor']; ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $bitacora['total_elementos']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?php echo $bitacora['porcentaje_cumplimiento']; ?>%"
                                                     aria-valuenow="<?php echo $bitacora['porcentaje_cumplimiento']; ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <?php echo $bitacora['porcentaje_cumplimiento']; ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary btn-ver-detalles" data-id="<?php echo $bitacora['id_bitacora']; ?>" title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success btn-ver-elementos" data-id="<?php echo $bitacora['id_bitacora']; ?>" title="Ver Elementos">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-eliminar" data-id="<?php echo $bitacora['id_bitacora']; ?>" title="Eliminar">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container End -->

        <!-- Modales -->
        <?php 
        include "Modales/NuevaBitacoraAdmin.php";
        include "Modales/VerDetallesBitacora.php";
        include "Modales/VerElementosLimpieza.php";
        include "Footer.php";
        ?>
    </div>
    <!-- Content End -->

    <script>
    $(document).ready(function() {
        // Ocultar spinner
        $('#spinner').hide();
        
        // Inicializar DataTable
        $('#tablaBitacoras').DataTable({
            "paging": true,
            "searching": true,
            "info": true,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[0, "desc"]],
            "pageLength": 25
        });
        
        // Abrir modal de nueva bitácora
        $('#btnNuevaBitacora').click(function() {
            $('#ModalNuevaBitacoraAdmin').modal('show');
        });

        // Aplicar filtros
        function aplicarFiltros() {
            const sucursal = $('#filtroSucursal').val();
            const area = $('#filtroArea').val();
            const fechaInicio = $('#filtroFechaInicio').val();
            const fechaFin = $('#filtroFechaFin').val();
            
            let url = 'BitacoraLimpieza.php?';
            const params = [];
            
            if (sucursal) params.push('sucursal=' + sucursal);
            if (area) params.push('area=' + area);
            if (fechaInicio) params.push('fecha_inicio=' + fechaInicio);
            if (fechaFin) params.push('fecha_fin=' + fechaFin);
            
            if (params.length > 0) {
                url += params.join('&');
            }
            
            window.location.href = url;
        }

        // Event listeners para filtros
        $('#filtroSucursal, #filtroArea, #filtroFechaInicio, #filtroFechaFin').change(function() {
            aplicarFiltros();
        });

        // Botón actualizar
        $('#btnActualizar').click(function() {
            location.reload();
        });

        // Exportar CSV
        $('#btnExportarCSV').click(function() {
            alert('Función de exportación en desarrollo');
        });

        // Ver detalles de bitácora
        $(document).on('click', '.btn-ver-detalles', function() {
            const idBitacora = $(this).data('id');
            alert('Ver detalles de bitácora ID: ' + idBitacora);
        });

        // Ver elementos de bitácora
        $(document).on('click', '.btn-ver-elementos', function() {
            const idBitacora = $(this).data('id');
            alert('Ver elementos de bitácora ID: ' + idBitacora);
        });

        // Eliminar bitácora
        $(document).on('click', '.btn-eliminar', function() {
            const idBitacora = $(this).data('id');
            
            if (confirm('¿Está seguro de eliminar esta bitácora?')) {
                alert('Eliminar bitácora ID: ' + idBitacora);
            }
        });
    });
    </script>

</body>
</html>