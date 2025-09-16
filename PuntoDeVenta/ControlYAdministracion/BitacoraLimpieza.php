<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
include_once "Controladores/ControladorUsuario.php";
    include_once "Controladores/BitacoraLimpiezaAdminControllerSimple.php";

    // Verificar sesión administrativa
    if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
        header("Location: Expiro.php");
        exit();
    }

    // Verificar que $conn esté disponible
    if (!isset($conn) || !$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    $controller = new BitacoraLimpiezaAdminControllerSimple($conn);

    // Obtener filtros
    $filtros = [];
    if (isset($_GET['sucursal']) && !empty($_GET['sucursal'])) {
        $filtros['sucursal'] = $_GET['sucursal'];
    }
    if (isset($_GET['area']) && !empty($_GET['area'])) {
        $filtros['area'] = $_GET['area'];
    }
    if (isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio'])) {
        $filtros['fecha_inicio'] = $_GET['fecha_inicio'];
    }
    if (isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin'])) {
        $filtros['fecha_fin'] = $_GET['fecha_fin'];
    }

    // Obtener datos con manejo de errores
    $bitacoras = [];
    $estadisticas = [];
    $sucursales = [];
    $areas = [];
    $bitacorasPorSucursal = [];

    try {
        $bitacoras = $controller->obtenerBitacorasConSucursal($filtros);
    } catch (Exception $e) {
        error_log("Error obteniendo bitácoras: " . $e->getMessage());
        $bitacoras = [];
    }

    try {
        $estadisticas = $controller->obtenerEstadisticasGenerales($filtros);
    } catch (Exception $e) {
        error_log("Error obteniendo estadísticas: " . $e->getMessage());
        $estadisticas = ['total_bitacoras' => 0, 'total_sucursales' => 1, 'total_areas' => 0, 'promedio_cumplimiento' => 0];
    }

    try {
        $sucursales = $controller->obtenerSucursales();
    } catch (Exception $e) {
        error_log("Error obteniendo sucursales: " . $e->getMessage());
        $sucursales = [];
    }

    try {
        $areas = $controller->obtenerAreas();
    } catch (Exception $e) {
        error_log("Error obteniendo áreas: " . $e->getMessage());
        $areas = [];
    }

    try {
        $bitacorasPorSucursal = $controller->obtenerBitacorasPorSucursal($filtros);
    } catch (Exception $e) {
        error_log("Error obteniendo bitácoras por sucursal: " . $e->getMessage());
        $bitacorasPorSucursal = [];
    }

    // Variable para identificar la página actual
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
    <title>Control de Bitácoras de Limpieza - <?php echo $row['Licencia']?></title>
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
                                        <option value="<?php echo $sucursal['Id_Sucursal']; ?>" 
                                                <?php echo (isset($filtros['sucursal']) && $filtros['sucursal'] == $sucursal['Id_Sucursal']) ? 'selected' : ''; ?>>
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
                                        <option value="<?php echo $area; ?>" 
                                                <?php echo (isset($filtros['area']) && $filtros['area'] == $area) ? 'selected' : ''; ?>>
                                            <?php echo $area; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filtroFechaInicio" class="form-label">Fecha Inicio:</label>
                                <input type="date" class="form-control" id="filtroFechaInicio" 
                                       value="<?php echo $filtros['fecha_inicio'] ?? ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="filtroFechaFin" class="form-label">Fecha Fin:</label>
                                <input type="date" class="form-control" id="filtroFechaFin" 
                                       value="<?php echo $filtros['fecha_fin'] ?? ''; ?>">
                            </div>
                        </div>

                        <!-- Estadísticas generales -->
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
                                        <h5 class="card-title"><?php echo $estadisticas['total_bitacoras']; ?></h5>
                                        <p class="card-text text-muted">Bitácoras Activas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-layer-group fa-2x text-info mb-2"></i>
                                        <h5 class="card-title"><?php echo $estadisticas['total_areas']; ?></h5>
                                        <p class="card-text text-muted">Áreas Diferentes</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-chart-line fa-2x text-warning mb-2"></i>
                                        <h5 class="card-title"><?php echo $estadisticas['promedio_cumplimiento']; ?>%</h5>
                                        <p class="card-text text-muted">Cumplimiento Promedio</p>
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
                                        <td><?php echo $bitacora['Nombre_Sucursal'] ?? 'N/A'; ?></td>
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
                                                <div class="progress-bar <?php 
                                                    echo $bitacora['porcentaje_cumplimiento'] >= 80 ? 'bg-success' : 
                                                        ($bitacora['porcentaje_cumplimiento'] >= 60 ? 'bg-warning' : 'bg-danger'); 
                                                ?>" 
                                                     style="width: <?php echo $bitacora['porcentaje_cumplimiento']; ?>%">
                                                    <?php echo $bitacora['porcentaje_cumplimiento']; ?>%
                                                </div>
                                            </div>
        </td>
        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary btn-ver-detalles" 
                                                        data-id="<?php echo $bitacora['id_bitacora']; ?>"
                                                        title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info btn-ver-elementos" 
                                                        data-id="<?php echo $bitacora['id_bitacora']; ?>"
                                                        title="Ver Elementos">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-eliminar" 
                                                        data-id="<?php echo $bitacora['id_bitacora']; ?>"
                                                        title="Eliminar">
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
            const sucursal = $('#filtroSucursal').val();
            const area = $('#filtroArea').val();
            const fechaInicio = $('#filtroFechaInicio').val();
            const fechaFin = $('#filtroFechaFin').val();
            
            let url = 'api/exportar_bitacoras_csv.php?';
            const params = [];
            
            if (sucursal) params.push('sucursal=' + sucursal);
            if (area) params.push('area=' + area);
            if (fechaInicio) params.push('fecha_inicio=' + fechaInicio);
            if (fechaFin) params.push('fecha_fin=' + fechaFin);
            
            if (params.length > 0) {
                url += params.join('&');
            }
            
            window.open(url, '_blank');
        });

        // Ver detalles de bitácora
        $(document).on('click', '.btn-ver-detalles', function() {
            const idBitacora = $(this).data('id');
            $('#ModalVerDetalles').modal('show');
            cargarDetallesBitacora(idBitacora);
        });

        // Ver elementos de limpieza
        $(document).on('click', '.btn-ver-elementos', function() {
            const idBitacora = $(this).data('id');
            $('#ModalVerElementos').modal('show');
            cargarElementosLimpieza(idBitacora);
        });

        // Eliminar bitácora
        $(document).on('click', '.btn-eliminar', function() {
            const idBitacora = $(this).data('id');
            
            Swal.fire({
                title: '¿Está seguro?',
                text: "Esta acción eliminará la bitácora y todos sus elementos. No se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarBitacora(idBitacora);
                }
            });
        });

        // Función para cargar detalles de bitácora
        function cargarDetallesBitacora(idBitacora) {
            $.ajax({
                url: 'api/obtener_detalles_bitacora.php',
                method: 'POST',
                data: { id_bitacora: idBitacora },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarDetallesBitacora(response.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión'
                    });
                }
            });
        }

        // Función para mostrar detalles de bitácora
        function mostrarDetallesBitacora(bitacora) {
            $('#detalleSucursal').text(bitacora.Nombre_Sucursal || 'N/A');
            $('#detalleArea').text(bitacora.area);
            $('#detalleSemana').text(bitacora.semana);
            $('#detalleFechaInicio').text(bitacora.fecha_inicio);
            $('#detalleFechaFin').text(bitacora.fecha_fin);
            $('#detalleResponsable').text(bitacora.responsable);
            $('#detalleSupervisor').text(bitacora.supervisor);
            $('#detalleAuxiliar').text(bitacora.aux_res);
        }

        // Función para cargar elementos de limpieza
        function cargarElementosLimpieza(idBitacora) {
            $.ajax({
                url: 'api/obtener_elementos_limpieza.php',
                method: 'POST',
                data: { id_bitacora: idBitacora },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarElementosLimpieza(response.data);
        } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión'
                    });
                }
            });
        }

        // Función para mostrar elementos de limpieza
        function mostrarElementosLimpieza(elementos) {
            const tbody = $('#tbodyElementos');
            tbody.empty();

            if (elementos.length === 0) {
                tbody.append('<tr><td colspan="16" class="text-center">No hay elementos registrados</td></tr>');
                return;
            }

            elementos.forEach(function(elemento) {
                const row = `
                    <tr>
                        <td>${elemento.elemento}</td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.lunes_mat ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.lunes_vesp ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.martes_mat ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.martes_vesp ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.miercoles_mat ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.miercoles_vesp ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.jueves_mat ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.jueves_vesp ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.viernes_mat ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.viernes_vesp ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.sabado_mat ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.sabado_vesp ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.domingo_mat ? 'checked' : ''} disabled></td>
                        <td><input type="checkbox" class="form-check-input" ${elemento.domingo_vesp ? 'checked' : ''} disabled></td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        // Función para eliminar bitácora
        function eliminarBitacora(idBitacora) {
            $.ajax({
                url: 'api/eliminar_bitacora_admin.php',
                method: 'POST',
                data: { id_bitacora: idBitacora },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión'
                    });
                }
            });
        }
    });
            </script>
        
</body>
</html>