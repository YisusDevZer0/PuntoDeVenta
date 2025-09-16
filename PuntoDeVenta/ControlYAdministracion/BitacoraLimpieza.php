<?php
// Versión ultra-simplificada para evitar problemas de carga
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

    // Obtener datos básicos directamente
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
                        bl.aux_res
                      FROM Bitacora_Limpieza bl 
                      ORDER BY bl.fecha_inicio DESC 
                      LIMIT 50";
    
    $result = mysqli_query($conn, $sql_bitacoras);
    if ($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $bitacoras[] = $row;
        }
    }
    
    // Consulta simple para sucursales
    $sql_sucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 'Si' ORDER BY Nombre_Sucursal LIMIT 10";
    $result_sucursales = mysqli_query($conn, $sql_sucursales);
    if ($result_sucursales) {
        while($row = mysqli_fetch_assoc($result_sucursales)) {
            $sucursales[] = $row;
        }
    }
    
    // Consulta simple para áreas
    $sql_areas = "SELECT DISTINCT area FROM Bitacora_Limpieza ORDER BY area LIMIT 10";
    $result_areas = mysqli_query($conn, $sql_areas);
    if ($result_areas) {
        while($row = mysqli_fetch_assoc($result_areas)) {
            $areas[] = $row['area'];
        }
    }
    
    // Estadísticas básicas
    $total_bitacoras = count($bitacoras);
    $total_sucursales = count($sucursales);
    $total_areas = count($areas);

} catch (Exception $e) {
    // Mostrar error en pantalla para debugging
    die("Error fatal: " . $e->getMessage() . " en línea " . $e->getLine());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Control de Bitácoras de Limpieza</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php";?>
</head>
<body>
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
                                <button type="button" class="btn btn-primary" onclick="alert('Función en desarrollo')">
                                    <i class="fa fa-plus me-2"></i>Nueva Bitácora
                                </button>
                                <button type="button" class="btn btn-success" onclick="alert('Función en desarrollo')">
                                    <i class="fa fa-download me-2"></i>Exportar CSV
                                </button>
                                <button type="button" class="btn btn-info" onclick="location.reload()">
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
                                        <h5 class="card-title"><?php echo $total_bitacoras; ?></h5>
                                        <p class="card-text text-muted">Total Bitácoras</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-building fa-2x text-success mb-2"></i>
                                        <h5 class="card-title"><?php echo $total_sucursales; ?></h5>
                                        <p class="card-text text-muted">Sucursales</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-map-marker-alt fa-2x text-info mb-2"></i>
                                        <h5 class="card-title"><?php echo $total_areas; ?></h5>
                                        <p class="card-text text-muted">Áreas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-chart-line fa-2x text-warning mb-2"></i>
                                        <h5 class="card-title">0%</h5>
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
                            <table class="table table-bordered table-striped" style="width:100%">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Área</th>
                                        <th>Semana</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Responsable</th>
                                        <th>Supervisor</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($bitacoras as $bitacora): ?>
                                    <tr>
                                        <td><?php echo $bitacora['id_bitacora']; ?></td>
                                        <td><?php echo $bitacora['area']; ?></td>
                                        <td><?php echo $bitacora['semana']; ?></td>
                                        <td><?php echo $bitacora['fecha_inicio']; ?></td>
                                        <td><?php echo $bitacora['fecha_fin']; ?></td>
                                        <td><?php echo $bitacora['responsable']; ?></td>
                                        <td><?php echo $bitacora['supervisor']; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary" onclick="alert('Ver detalles ID: <?php echo $bitacora['id_bitacora']; ?>')" title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success" onclick="alert('Ver elementos ID: <?php echo $bitacora['id_bitacora']; ?>')" title="Ver Elementos">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="if(confirm('¿Eliminar bitácora?')) alert('Eliminar ID: <?php echo $bitacora['id_bitacora']; ?>')" title="Eliminar">
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

        <!-- Footer -->
        <?php include "Footer.php"; ?>
    </div>
    <!-- Content End -->

    <script>
    // Script mínimo para evitar problemas de carga
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Página cargada correctamente');
        
        // Inicializar DataTable si está disponible
        if (typeof $.fn.DataTable !== 'undefined') {
            $('table').DataTable({
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
        }
    });
    </script>

</body>
</html>