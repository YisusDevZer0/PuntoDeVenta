<?php
session_start();
require_once '../Consultas/db_connect.php';
require_once '../Consultas/ValidadorUsuario.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit();
}

// Obtener datos del usuario
$usuario_id = $_SESSION['usuario_id'];
$sucursal_id = $_SESSION['sucursal_id'] ?? 1;
$tipo_usuario = $_SESSION['tipo_usuario'] ?? 'Usuario';

// Verificar permisos
$isAdmin = ($tipo_usuario == 'Administrador' || $tipo_usuario == 'MKT');

// Incluir el menú
include 'Menu.php';

// Función para generar reportes
function generarReporteGeneral($fecha_inicio, $fecha_fin, $sucursal_id = null, $tipo_devolucion = null) {
    global $conn;
    
    $sql = "SELECT 
                d.folio,
                d.fecha,
                s.Nombre_Sucursal,
                u.Nombre_Apellidos as usuario,
                d.total_productos,
                d.total_unidades,
                d.valor_total,
                d.estatus,
                d.observaciones_generales
            FROM Devoluciones d
            LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
            LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser
            WHERE DATE(d.fecha) BETWEEN ? AND ?";
    
    $params = [$fecha_inicio, $fecha_fin];
    $types = 'ss';
    
    if ($sucursal_id) {
        $sql .= " AND d.sucursal_id = ?";
        $params[] = $sucursal_id;
        $types .= 'i';
    }
    
    $sql .= " ORDER BY d.fecha DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

function generarReporteDetallado($fecha_inicio, $fecha_fin, $sucursal_id = null) {
    global $conn;
    
    $sql = "SELECT 
                d.folio,
                d.fecha,
                s.Nombre_Sucursal,
                u.Nombre_Apellidos as usuario,
                dd.codigo_barras,
                dd.nombre_producto,
                dd.cantidad,
                dd.tipo_devolucion,
                td.nombre as tipo_nombre,
                dd.lote,
                dd.fecha_caducidad,
                dd.precio_venta,
                dd.valor_total,
                dd.observaciones,
                d.estatus
            FROM Devoluciones d
            LEFT JOIN Devoluciones_Detalle dd ON d.id = dd.devolucion_id
            LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
            LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser
            LEFT JOIN Tipos_Devolucion td ON dd.tipo_devolucion = td.codigo
            WHERE DATE(d.fecha) BETWEEN ? AND ?";
    
    $params = [$fecha_inicio, $fecha_fin];
    $types = 'ss';
    
    if ($sucursal_id) {
        $sql .= " AND d.sucursal_id = ?";
        $params[] = $sucursal_id;
        $types .= 'i';
    }
    
    $sql .= " ORDER BY d.fecha DESC, d.folio, dd.id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

function generarEstadisticas($fecha_inicio, $fecha_fin, $sucursal_id = null) {
    global $conn;
    
    $sql = "SELECT 
                COUNT(*) as total_devoluciones,
                SUM(total_unidades) as total_unidades,
                SUM(valor_total) as valor_total,
                COUNT(CASE WHEN estatus = 'pendiente' THEN 1 END) as pendientes,
                COUNT(CASE WHEN estatus = 'procesada' THEN 1 END) as procesadas,
                COUNT(CASE WHEN estatus = 'cancelada' THEN 1 END) as canceladas,
                AVG(total_unidades) as promedio_unidades,
                AVG(valor_total) as promedio_valor
            FROM Devoluciones 
            WHERE DATE(fecha) BETWEEN ? AND ?";
    
    $params = [$fecha_inicio, $fecha_fin];
    $types = 'ss';
    
    if ($sucursal_id) {
        $sql .= " AND sucursal_id = ?";
        $params[] = $sucursal_id;
        $types .= 'i';
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

function obtenerTiposDevolucionStats($fecha_inicio, $fecha_fin, $sucursal_id = null) {
    global $conn;
    
    $sql = "SELECT 
                dd.tipo_devolucion,
                td.nombre as tipo_nombre,
                td.color,
                COUNT(*) as cantidad,
                SUM(dd.cantidad) as total_unidades,
                SUM(dd.valor_total) as valor_total
            FROM Devoluciones_Detalle dd
            LEFT JOIN Devoluciones d ON dd.devolucion_id = d.id
            LEFT JOIN Tipos_Devolucion td ON dd.tipo_devolucion = td.codigo
            WHERE DATE(d.fecha) BETWEEN ? AND ?";
    
    $params = [$fecha_inicio, $fecha_fin];
    $types = 'ss';
    
    if ($sucursal_id) {
        $sql .= " AND d.sucursal_id = ?";
        $params[] = $sucursal_id;
        $types .= 'i';
    }
    
    $sql .= " GROUP BY dd.tipo_devolucion, td.nombre, td.color
              ORDER BY cantidad DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

function obtenerSucursales() {
    global $conn;
    
    $sql = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";
    $result = $conn->query($sql);
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Procesar solicitudes de reportes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    switch ($_POST['action']) {
        case 'generar_reporte':
            $tipo_reporte = $_POST['tipo_reporte'] ?? 'general';
            $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
            $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
            $sucursal_filtro = !empty($_POST['sucursal_id']) ? intval($_POST['sucursal_id']) : null;
            
            try {
                switch ($tipo_reporte) {
                    case 'general':
                        $datos = generarReporteGeneral($fecha_inicio, $fecha_fin, $sucursal_filtro);
                        break;
                    case 'detallado':
                        $datos = generarReporteDetallado($fecha_inicio, $fecha_fin, $sucursal_filtro);
                        break;
                    case 'estadisticas':
                        $datos = generarEstadisticas($fecha_inicio, $fecha_fin, $sucursal_filtro);
                        break;
                    case 'tipos':
                        $datos = obtenerTiposDevolucionStats($fecha_inicio, $fecha_fin, $sucursal_filtro);
                        break;
                    default:
                        throw new Exception('Tipo de reporte no válido');
                }
                
                $response = [
                    'success' => true,
                    'data' => $datos,
                    'tipo' => $tipo_reporte
                ];
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            break;
            
        case 'exportar_excel':
            // Funcionalidad para exportar a Excel
            $tipo_reporte = $_POST['tipo_reporte'] ?? 'general';
            $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
            $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
            $sucursal_filtro = !empty($_POST['sucursal_id']) ? intval($_POST['sucursal_id']) : null;
            
            // Aquí se implementaría la exportación a Excel
            $response = [
                'success' => true,
                'message' => 'Funcionalidad de exportación en desarrollo'
            ];
            break;
    }
    
    echo json_encode($response);
    exit;
}

$sucursales = obtenerSucursales();
$currentPage = 'reportes_devoluciones';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Devoluciones - Doctor Pez</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    <style>
        .report-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .chart-container {
            position: relative;
            height: 400px;
            background: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .filter-section {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .btn-export {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            font-weight: bold;
        }
        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
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
            <div class="col-md-9 col-lg-10">
                <div class="container-fluid py-4">
                    
                    <!-- Header -->
                    <div class="report-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2><i class="fa-solid fa-chart-line me-2"></i>Reportes de Devoluciones</h2>
                                <p class="mb-0">Análisis detallado de devoluciones y estadísticas</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-light" onclick="exportarDatos()">
                                    <i class="fa-solid fa-download me-2"></i>Exportar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtros -->
                    <div class="filter-section">
                        <h5><i class="fa-solid fa-filter me-2"></i>Filtros de Reporte</h5>
                        <form id="filtros-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Inicio:</label>
                                    <input type="date" id="fecha-inicio" class="form-control" value="<?php echo date('Y-m-01'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Fin:</label>
                                    <input type="date" id="fecha-fin" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sucursal:</label>
                                    <select id="sucursal-filtro" class="form-select">
                                        <option value="">Todas las sucursales</option>
                                        <?php foreach ($sucursales as $sucursal): ?>
                                            <option value="<?php echo $sucursal['ID_Sucursal']; ?>">
                                                <?php echo $sucursal['Nombre_Sucursal']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipo de Reporte:</label>
                                    <select id="tipo-reporte" class="form-select">
                                        <option value="general">Reporte General</option>
                                        <option value="detallado">Reporte Detallado</option>
                                        <option value="estadisticas">Estadísticas</option>
                                        <option value="tipos">Por Tipos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" onclick="generarReporte()">
                                        <i class="fa-solid fa-chart-bar me-1"></i>Generar Reporte
                                    </button>
                                    <button type="button" class="btn btn-export ms-2" onclick="exportarExcel()">
                                        <i class="fa-solid fa-file-excel me-1"></i>Exportar Excel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Loading -->
                    <div id="loading" class="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Generando reporte...</p>
                    </div>
                    
                    <!-- Estadísticas Generales -->
                    <div id="estadisticas-section" style="display: none;">
                        <h5><i class="fa-solid fa-chart-pie me-2"></i>Estadísticas Generales</h5>
                        <div class="row" id="stats-cards">
                            <!-- Las tarjetas se generarán dinámicamente -->
                        </div>
                    </div>
                    
                    <!-- Gráficos -->
                    <div id="graficos-section" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h6>Devoluciones por Tipo</h6>
                                    <canvas id="chart-tipos"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h6>Tendencia de Devoluciones</h6>
                                    <canvas id="chart-tendencia"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de Resultados -->
                    <div id="resultados-section" style="display: none;">
                        <h5><i class="fa-solid fa-table me-2"></i>Resultados del Reporte</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark" id="tabla-header">
                                    <!-- Los headers se generarán dinámicamente -->
                                </thead>
                                <tbody id="tabla-body">
                                    <!-- Los datos se generarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        let chartTipos = null;
        let chartTendencia = null;
        
        // Generar reporte
        function generarReporte() {
            const tipoReporte = $('#tipo-reporte').val();
            const fechaInicio = $('#fecha-inicio').val();
            const fechaFin = $('#fecha-fin').val();
            const sucursalId = $('#sucursal-filtro').val();
            
            if (!fechaInicio || !fechaFin) {
                alert('Por favor seleccione las fechas');
                return;
            }
            
            $('#loading').show();
            $('#estadisticas-section').hide();
            $('#graficos-section').hide();
            $('#resultados-section').hide();
            
            $.ajax({
                url: 'ReportesDevoluciones.php',
                method: 'POST',
                data: {
                    action: 'generar_reporte',
                    tipo_reporte: tipoReporte,
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    sucursal_id: sucursalId
                },
                dataType: 'json',
                success: function(response) {
                    $('#loading').hide();
                    
                    if (response.success) {
                        mostrarResultados(response.data, response.tipo);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    $('#loading').hide();
                    alert('Error al generar el reporte');
                }
            });
        }
        
        // Mostrar resultados
        function mostrarResultados(data, tipo) {
            switch (tipo) {
                case 'general':
                    mostrarReporteGeneral(data);
                    break;
                case 'detallado':
                    mostrarReporteDetallado(data);
                    break;
                case 'estadisticas':
                    mostrarEstadisticas(data);
                    break;
                case 'tipos':
                    mostrarReporteTipos(data);
                    break;
            }
        }
        
        // Mostrar reporte general
        function mostrarReporteGeneral(data) {
            const headers = `
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Sucursal</th>
                    <th>Usuario</th>
                    <th>Productos</th>
                    <th>Unidades</th>
                    <th>Valor Total</th>
                    <th>Estatus</th>
                </tr>
            `;
            
            let rows = '';
            data.forEach(function(row) {
                const estatusClass = getEstatusClass(row.estatus);
                rows += `
                    <tr>
                        <td><strong>${row.folio}</strong></td>
                        <td>${formatearFecha(row.fecha)}</td>
                        <td>${row.Nombre_Sucursal || 'N/A'}</td>
                        <td>${row.usuario || 'N/A'}</td>
                        <td>${row.total_productos}</td>
                        <td>${row.total_unidades}</td>
                        <td>$${parseFloat(row.valor_total).toFixed(2)}</td>
                        <td><span class="badge ${estatusClass}">${row.estatus}</span></td>
                    </tr>
                `;
            });
            
            $('#tabla-header').html(headers);
            $('#tabla-body').html(rows);
            $('#resultados-section').show();
        }
        
        // Mostrar reporte detallado
        function mostrarReporteDetallado(data) {
            const headers = `
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Código</th>
                    <th>Cantidad</th>
                    <th>Tipo</th>
                    <th>Lote</th>
                    <th>Caducidad</th>
                    <th>Valor</th>
                    <th>Estatus</th>
                </tr>
            `;
            
            let rows = '';
            data.forEach(function(row) {
                const estatusClass = getEstatusClass(row.estatus);
                rows += `
                    <tr>
                        <td><strong>${row.folio}</strong></td>
                        <td>${formatearFecha(row.fecha)}</td>
                        <td>${row.nombre_producto}</td>
                        <td>${row.codigo_barras}</td>
                        <td>${row.cantidad}</td>
                        <td><span class="badge bg-secondary">${row.tipo_nombre || row.tipo_devolucion}</span></td>
                        <td>${row.lote || 'N/A'}</td>
                        <td>${row.fecha_caducidad || 'N/A'}</td>
                        <td>$${parseFloat(row.valor_total).toFixed(2)}</td>
                        <td><span class="badge ${estatusClass}">${row.estatus}</span></td>
                    </tr>
                `;
            });
            
            $('#tabla-header').html(headers);
            $('#tabla-body').html(rows);
            $('#resultados-section').show();
        }
        
        // Mostrar estadísticas
        function mostrarEstadisticas(data) {
            const cards = `
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value">${data.total_devoluciones}</div>
                        <div class="text-muted">Total Devoluciones</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value">${data.total_unidades}</div>
                        <div class="text-muted">Unidades Devueltas</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value">$${parseFloat(data.valor_total).toFixed(2)}</div>
                        <div class="text-muted">Valor Total</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value">${parseFloat(data.promedio_valor).toFixed(2)}</div>
                        <div class="text-muted">Promedio por Devolución</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card border-warning">
                        <div class="stat-value text-warning">${data.pendientes}</div>
                        <div class="text-muted">Pendientes</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card border-success">
                        <div class="stat-value text-success">${data.procesadas}</div>
                        <div class="text-muted">Procesadas</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card border-danger">
                        <div class="stat-value text-danger">${data.canceladas}</div>
                        <div class="text-muted">Canceladas</div>
                    </div>
                </div>
            `;
            
            $('#stats-cards').html(cards);
            $('#estadisticas-section').show();
        }
        
        // Mostrar reporte por tipos
        function mostrarReporteTipos(data) {
            // Mostrar tabla
            const headers = `
                <tr>
                    <th>Tipo de Devolución</th>
                    <th>Cantidad</th>
                    <th>Unidades</th>
                    <th>Valor Total</th>
                    <th>Porcentaje</th>
                </tr>
            `;
            
            const totalCantidad = data.reduce((sum, item) => sum + parseInt(item.cantidad), 0);
            
            let rows = '';
            data.forEach(function(row) {
                const porcentaje = ((parseInt(row.cantidad) / totalCantidad) * 100).toFixed(1);
                rows += `
                    <tr>
                        <td>
                            <span class="badge" style="background-color: ${row.color}">
                                ${row.tipo_nombre}
                            </span>
                        </td>
                        <td>${row.cantidad}</td>
                        <td>${row.total_unidades}</td>
                        <td>$${parseFloat(row.valor_total).toFixed(2)}</td>
                        <td>${porcentaje}%</td>
                    </tr>
                `;
            });
            
            $('#tabla-header').html(headers);
            $('#tabla-body').html(rows);
            $('#resultados-section').show();
            
            // Mostrar gráfico
            mostrarGraficoTipos(data);
            $('#graficos-section').show();
        }
        
        // Mostrar gráfico de tipos
        function mostrarGraficoTipos(data) {
            const ctx = document.getElementById('chart-tipos').getContext('2d');
            
            if (chartTipos) {
                chartTipos.destroy();
            }
            
            const labels = data.map(item => item.tipo_nombre);
            const valores = data.map(item => item.cantidad);
            const colores = data.map(item => item.color);
            
            chartTipos = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: valores,
                        backgroundColor: colores,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Exportar a Excel
        function exportarExcel() {
            const tipoReporte = $('#tipo-reporte').val();
            const fechaInicio = $('#fecha-inicio').val();
            const fechaFin = $('#fecha-fin').val();
            const sucursalId = $('#sucursal-filtro').val();
            
            $.ajax({
                url: 'ReportesDevoluciones.php',
                method: 'POST',
                data: {
                    action: 'exportar_excel',
                    tipo_reporte: tipoReporte,
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    sucursal_id: sucursalId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
        
        // Funciones auxiliares
        function getEstatusClass(estatus) {
            switch (estatus) {
                case 'pendiente': return 'bg-warning text-dark';
                case 'procesada': return 'bg-success';
                case 'cancelada': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }
        
        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-ES');
        }
        
        function exportarDatos() {
            // Implementar exportación general
            exportarExcel();
        }
        
        // Inicializar
        $(document).ready(function() {
            // Cargar estadísticas iniciales
            generarReporte();
        });
    </script>
</body>
</html>
