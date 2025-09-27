<?php
include_once "Controladores/ControladorUsuario.php";

// Obtener datos del usuario
$usuario_id = isset($row['Id_PvUser']) ? $row['Id_PvUser'] : 1;
$sucursal_id = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 1;
$tipo_usuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';

// Verificar permisos
$isAdmin = ($tipo_usuario == 'Administrador' || $tipo_usuario == 'MKT');

// Definir variable para identificar la página actual
$currentPage = 'devoluciones';
$showDashboard = false;
$disabledAttr = $isAdmin ? '' : 'disabled';

// Función para obtener tipos de devolución
function getTiposDevolucion() {
    return [
        'no_facturado' => 'Producto no facturado',
        'danado_recibir' => 'Producto dañado al recibir',
        'proximo_caducar' => 'Próximo a caducar',
        'caducado' => 'Producto caducado',
        'danado_roto' => 'Producto dañado/roto',
        'solicitado_admin' => 'Solicitado por administración',
        'error_etiquetado' => 'Error en etiquetado',
        'defectuoso' => 'Producto defectuoso',
        'sobrante' => 'Sobrante de inventario',
        'otro' => 'Otro motivo'
    ];
}

// Función para buscar producto
function buscarProducto($codigo_barras, $sucursal_id) {
    global $conn;
    
    $sql = "SELECT s.*, p.Precio_Venta, p.Precio_C, p.Fecha_Caducidad, p.Lote
            FROM Stock_POS s 
            LEFT JOIN productos_lotes_caducidad p ON s.ID_Prod_POS = p.ID_Prod_POS AND s.Fk_sucursal = p.Fk_sucursal
            WHERE s.Cod_Barra = ? AND s.Fk_sucursal = ? AND s.Existencias_R > 0
            ORDER BY p.Fecha_Caducidad ASC
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $codigo_barras, $sucursal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    switch ($_POST['action']) {
        case 'agregar_producto':
            $codigo_barras = trim($_POST['codigo_barras']);
            $cantidad = intval($_POST['cantidad']);
            $tipo_devolucion = $_POST['tipo_devolucion'];
            $observaciones = trim($_POST['observaciones']);
            
            if (empty($codigo_barras) || $cantidad <= 0) {
                $response['message'] = 'Código de barras y cantidad son requeridos';
                echo json_encode($response);
                exit;
            }
            
            $producto = buscarProducto($codigo_barras, $sucursal_id);
            if (!$producto) {
                $response['message'] = 'Producto no encontrado o sin existencias';
                echo json_encode($response);
                exit;
            }
            
            // Agregar a la sesión temporal
            if (!isset($_SESSION['devolucion_temp'])) {
                $_SESSION['devolucion_temp'] = [];
            }
            
            $item_key = $codigo_barras . '_' . $producto['Lote'];
            if (isset($_SESSION['devolucion_temp'][$item_key])) {
                $_SESSION['devolucion_temp'][$item_key]['cantidad'] += $cantidad;
            } else {
                $_SESSION['devolucion_temp'][$item_key] = [
                    'codigo_barras' => $codigo_barras,
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'tipo_devolucion' => $tipo_devolucion,
                    'observaciones' => $observaciones
                ];
            }
            
            $response['success'] = true;
            $response['message'] = 'Producto agregado correctamente';
            break;
            
        case 'eliminar_producto':
            $item_key = $_POST['item_key'];
            if (isset($_SESSION['devolucion_temp'][$item_key])) {
                unset($_SESSION['devolucion_temp'][$item_key]);
                $response['success'] = true;
                $response['message'] = 'Producto eliminado correctamente';
            } else {
                $response['message'] = 'Producto no encontrado';
            }
            break;
            
        case 'procesar_devolucion':
            $productos_json = $_POST['productos'] ?? '';
            if (empty($productos_json)) {
                $response['message'] = 'No hay productos para procesar';
                echo json_encode($response);
                exit;
            }
            
            $productos = json_decode($productos_json, true);
            if (!$productos || count($productos) === 0) {
                $response['message'] = 'No hay productos válidos para procesar';
                echo json_encode($response);
                exit;
            }
            
            // Generar folio único
            $folio_devolucion = 'DEV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Iniciar transacción
            $conn->begin_transaction();
            
            try {
                // Crear registro de devolución
                $sql = "INSERT INTO Devoluciones (folio, sucursal_id, usuario_id, fecha, estatus, observaciones_generales) 
                        VALUES (?, ?, ?, NOW(), 'procesada', ?)";
                $stmt = $conn->prepare($sql);
                $observaciones_generales = $_POST['observaciones_generales'] ?? '';
                $stmt->bind_param("siis", $folio_devolucion, $sucursal_id, $usuario_id, $observaciones_generales);
                $stmt->execute();
                $devolucion_id = $conn->insert_id;
                
                // Procesar cada producto
                foreach ($productos as $item) {
                    $producto = $item['producto'];
                    
                    // Insertar detalle de devolución
                    $sql = "INSERT INTO Devoluciones_Detalle 
                            (devolucion_id, producto_id, codigo_barras, nombre_producto, cantidad, 
                             tipo_devolucion, observaciones, lote, fecha_caducidad, precio_venta) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iississssd", 
                        $devolucion_id, 
                        $producto['id'], 
                        $item['codigo_barras'], 
                        $producto['nombre'], 
                        $item['cantidad'], 
                        $item['tipo_devolucion'], 
                        $item['observaciones'], 
                        $producto['lote'], 
                        $producto['fecha_caducidad'], 
                        $producto['precio_venta']
                    );
                    $stmt->execute();
                    
                    // Actualizar stock (reducir existencias)
                    $sql = "UPDATE Stock_POS 
                            SET Existencias_R = Existencias_R - ? 
                            WHERE ID_Prod_POS = ? AND Fk_sucursal = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iii", $item['cantidad'], $producto['id'], $sucursal_id);
                    $stmt->execute();
                    
                    // Registrar en log de stock
                    $sql = "INSERT INTO Stock_POS_Log (Cod_Barra, Fk_sucursal, Cantidad, TipoDeMov, Mensaje) 
                            VALUES (?, ?, ?, 'Devolucion', ?)";
                    $stmt = $conn->prepare($sql);
                    $mensaje = "Devolución: {$item['tipo_devolucion']} - {$item['observaciones']}";
                    $stmt->bind_param("siis", $item['codigo_barras'], $sucursal_id, $item['cantidad'], $mensaje);
                    $stmt->execute();
                }
                
                $conn->commit();
                $response['success'] = true;
                $response['message'] = 'Devolución procesada correctamente. Folio: ' . $folio_devolucion;
                $response['folio'] = $folio_devolucion;
                
            } catch (Exception $e) {
                $conn->rollback();
                $response['message'] = 'Error al procesar devolución: ' . $e->getMessage();
            }
            break;
            
        case 'obtener_devoluciones_procesar':
            $devoluciones = getDevoluciones($sucursal_id, $fecha_inicio, $fecha_fin, $estatus);
            $response['success'] = true;
            $response['devoluciones'] = $devoluciones;
            break;
            
        case 'obtener_detalles_devolucion':
            $devolucion_id = intval($_POST['devolucion_id']);
            $devolucion = getDevolucionById($devolucion_id);
            $detalles = getDevolucionDetalle($devolucion_id);
            
            if ($devolucion) {
                $response['success'] = true;
                $response['devolucion'] = $devolucion;
                $response['detalles'] = $detalles;
            } else {
                $response['message'] = 'Devolución no encontrada';
            }
            break;
            
        case 'aprobar_devolucion':
            $devolucion_id = intval($_POST['devolucion_id']);
            $sql = "UPDATE Devoluciones SET estatus = 'aprobada' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $devolucion_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Devolución aprobada correctamente';
            } else {
                $response['message'] = 'Error al aprobar devolución';
            }
            break;
            
        case 'revertir_devolucion':
            $devolucion_id = intval($_POST['devolucion_id']);
            
            $conn->begin_transaction();
            try {
                // Obtener detalles de la devolución
                $detalles = getDevolucionDetalle($devolucion_id);
                
                // Restaurar stock
                foreach ($detalles as $detalle) {
                    $sql = "UPDATE Stock_POS 
                            SET Existencias_R = Existencias_R + ? 
                            WHERE ID_Prod_POS = ? AND Fk_sucursal = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iii", $detalle['cantidad'], $detalle['producto_id'], $sucursal_id);
                    $stmt->execute();
                }
                
                // Cambiar estatus
                $sql = "UPDATE Devoluciones SET estatus = 'revertida' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $devolucion_id);
                $stmt->execute();
                
                $conn->commit();
                $response['success'] = true;
                $response['message'] = 'Devolución revertida correctamente';
                
            } catch (Exception $e) {
                $conn->rollback();
                $response['message'] = 'Error al revertir devolución: ' . $e->getMessage();
            }
            break;
            
            case 'cancelar_devolucion':
                $devolucion_id = intval($_POST['devolucion_id']);
                $sql = "UPDATE Devoluciones SET estatus = 'cancelada' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $devolucion_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Devolución cancelada correctamente';
                } else {
                    $response['message'] = 'Error al cancelar devolución';
                }
                break;
                
            case 'generar_reporte':
                $fecha_inicio = $_POST['fecha_inicio'];
                $fecha_fin = $_POST['fecha_fin'];
                $tipo_reporte = $_POST['tipo_reporte'];
                
                $reporte = generarReporteCompleto($fecha_inicio, $fecha_fin, $tipo_reporte);
                $response['success'] = true;
                $response['estadisticas'] = $reporte['estadisticas'];
                $response['datos'] = $reporte['datos'];
                $response['graficos'] = $reporte['graficos'];
                break;
                
            case 'exportar_excel':
                $fecha_inicio = $_GET['fecha_inicio'];
                $fecha_fin = $_GET['fecha_fin'];
                $tipo_reporte = $_GET['tipo_reporte'];
                exportarExcel($fecha_inicio, $fecha_fin, $tipo_reporte);
                exit;
                
            case 'exportar_pdf':
                $fecha_inicio = $_GET['fecha_inicio'];
                $fecha_fin = $_GET['fecha_fin'];
                $tipo_reporte = $_GET['tipo_reporte'];
                exportarPDF($fecha_inicio, $fecha_fin, $tipo_reporte);
                exit;
    }
    
    echo json_encode($response);
    exit;
}

// Obtener devoluciones para listado
function getDevoluciones($sucursal_id, $fecha_inicio = null, $fecha_fin = null, $estatus = null) {
    global $conn;
    
    $sql = "SELECT d.*, u.Nombre_Apellidos as usuario_nombre, s.Nombre_Sucursal
            FROM Devoluciones d
            LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser
            LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
            WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if ($sucursal_id) {
        $sql .= " AND d.sucursal_id = ?";
        $params[] = $sucursal_id;
        $types .= 'i';
    }
    
    if ($fecha_inicio) {
        $sql .= " AND DATE(d.fecha) >= ?";
        $params[] = $fecha_inicio;
        $types .= 's';
    }
    
    if ($fecha_fin) {
        $sql .= " AND DATE(d.fecha) <= ?";
        $params[] = $fecha_fin;
        $types .= 's';
    }
    
    if ($estatus) {
        $sql .= " AND d.estatus = ?";
        $params[] = $estatus;
        $types .= 's';
    }
    
    $sql .= " ORDER BY d.fecha DESC LIMIT 100";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener detalles de una devolución
function getDevolucionDetalle($devolucion_id) {
    global $conn;
    
    $sql = "SELECT * FROM Devoluciones_Detalle WHERE devolucion_id = ? ORDER BY id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $devolucion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener devolución por ID
function getDevolucionById($devolucion_id) {
    global $conn;
    
    $sql = "SELECT d.*, u.Nombre_Apellidos as usuario_nombre, s.Nombre_Sucursal
            FROM Devoluciones d
            LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser
            LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
            WHERE d.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $devolucion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// ==================== FUNCIONES DE REPORTES ====================

function generarReporteCompleto($fecha_inicio, $fecha_fin, $tipo_reporte) {
    global $conn;
    
    $estadisticas = obtenerEstadisticasGenerales($fecha_inicio, $fecha_fin);
    $datos = obtenerDatosReporte($fecha_inicio, $fecha_fin, $tipo_reporte);
    $graficos = generarDatosGraficos($fecha_inicio, $fecha_fin);
    
    return [
        'estadisticas' => $estadisticas,
        'datos' => $datos,
        'graficos' => $graficos
    ];
}

function obtenerEstadisticasGenerales($fecha_inicio, $fecha_fin) {
    global $conn;
    
    $sql = "SELECT 
                COUNT(*) as total_devoluciones,
                SUM(total_productos) as total_productos,
                SUM(valor_total) as valor_total,
                AVG(total_productos) as promedio_diario
            FROM Devoluciones 
            WHERE fecha BETWEEN ? AND ? AND estatus != 'cancelada'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

function obtenerDatosReporte($fecha_inicio, $fecha_fin, $tipo_reporte) {
    global $conn;
    
    switch ($tipo_reporte) {
        case 'general':
            return obtenerReporteGeneral($fecha_inicio, $fecha_fin);
        case 'por_tipo':
            return obtenerReportePorTipo($fecha_inicio, $fecha_fin);
        case 'por_usuario':
            return obtenerReportePorUsuario($fecha_inicio, $fecha_fin);
        case 'por_producto':
            return obtenerReportePorProducto($fecha_inicio, $fecha_fin);
        case 'valor_monetario':
            return obtenerReporteValorMonetario($fecha_inicio, $fecha_fin);
        default:
            return [];
    }
}

function obtenerReporteGeneral($fecha_inicio, $fecha_fin) {
    global $conn;
    
    $sql = "SELECT d.folio, d.fecha, u.Nombre_Apellidos as usuario, 
                   d.total_productos as productos, d.valor_total as valor, d.estatus
            FROM Devoluciones d
            LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser
            WHERE d.fecha BETWEEN ? AND ? AND d.estatus != 'cancelada'
            ORDER BY d.fecha DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
    
    return $datos;
}

function obtenerReportePorTipo($fecha_inicio, $fecha_fin) {
    global $conn;
    
    $sql = "SELECT dd.tipo_devolucion as tipo, 
                   COUNT(*) as cantidad,
                   SUM(dd.cantidad) as productos,
                   SUM(dd.cantidad * dd.precio_venta) as valor_total,
                   (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM Devoluciones_Detalle dd2 
                    JOIN Devoluciones d2 ON dd2.devolucion_id = d2.id 
                    WHERE d2.fecha BETWEEN ? AND ? AND d2.estatus != 'cancelada')) as porcentaje
            FROM Devoluciones_Detalle dd
            JOIN Devoluciones d ON dd.devolucion_id = d.id
            WHERE d.fecha BETWEEN ? AND ? AND d.estatus != 'cancelada'
            GROUP BY dd.tipo_devolucion
            ORDER BY cantidad DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
    
    return $datos;
}

function obtenerReportePorUsuario($fecha_inicio, $fecha_fin) {
    global $conn;
    
    $sql = "SELECT u.Nombre_Apellidos as usuario,
                   COUNT(d.id) as devoluciones,
                   SUM(d.total_productos) as productos,
                   SUM(d.valor_total) as valor_total,
                   AVG(d.valor_total) as promedio
            FROM Devoluciones d
            LEFT JOIN Usuarios_PV u ON d.usuario_id = u.Id_PvUser
            WHERE d.fecha BETWEEN ? AND ? AND d.estatus != 'cancelada'
            GROUP BY d.usuario_id, u.Nombre_Apellidos
            ORDER BY devoluciones DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
    
    return $datos;
}

function obtenerReportePorProducto($fecha_inicio, $fecha_fin) {
    global $conn;
    
    $sql = "SELECT dd.nombre_producto as producto, dd.codigo_barras as codigo,
                   SUM(dd.cantidad) as cantidad,
                   SUM(dd.cantidad * dd.precio_venta) as valor,
                   dd.tipo_devolucion as motivo_principal
            FROM Devoluciones_Detalle dd
            JOIN Devoluciones d ON dd.devolucion_id = d.id
            WHERE d.fecha BETWEEN ? AND ? AND d.estatus != 'cancelada'
            GROUP BY dd.producto_id, dd.nombre_producto, dd.codigo_barras
            ORDER BY cantidad DESC
            LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
    
    return $datos;
}

function obtenerReporteValorMonetario($fecha_inicio, $fecha_fin) {
    global $conn;
    
    $sql = "SELECT DATE(d.fecha) as fecha,
                   COUNT(d.id) as devoluciones,
                   SUM(d.valor_total) as valor_total,
                   AVG(d.valor_total) as valor_promedio,
                   (SELECT dd.tipo_devolucion 
                    FROM Devoluciones_Detalle dd 
                    WHERE dd.devolucion_id = d.id 
                    GROUP BY dd.tipo_devolucion 
                    ORDER BY COUNT(*) DESC 
                    LIMIT 1) as tipo_principal
            FROM Devoluciones d
            WHERE d.fecha BETWEEN ? AND ? AND d.estatus != 'cancelada'
            GROUP BY DATE(d.fecha)
            ORDER BY d.fecha DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
    
    return $datos;
}

function generarDatosGraficos($fecha_inicio, $fecha_fin) {
    global $conn;
    
    // Gráfico por tipo
    $sql = "SELECT dd.tipo_devolucion, COUNT(*) as cantidad
            FROM Devoluciones_Detalle dd
            JOIN Devoluciones d ON dd.devolucion_id = d.id
            WHERE d.fecha BETWEEN ? AND ? AND d.estatus != 'cancelada'
            GROUP BY dd.tipo_devolucion";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $por_tipo = ['labels' => [], 'valores' => []];
    while ($row = $result->fetch_assoc()) {
        $por_tipo['labels'][] = getTipoDevolucionTexto($row['tipo_devolucion']);
        $por_tipo['valores'][] = $row['cantidad'];
    }
    
    // Gráfico de tendencia
    $sql = "SELECT DATE(fecha) as fecha, COUNT(*) as cantidad
            FROM Devoluciones 
            WHERE fecha BETWEEN ? AND ? AND estatus != 'cancelada'
            GROUP BY DATE(fecha)
            ORDER BY fecha";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tendencia = ['fechas' => [], 'valores' => []];
    while ($row = $result->fetch_assoc()) {
        $tendencia['fechas'][] = date('d/m', strtotime($row['fecha']));
        $tendencia['valores'][] = $row['cantidad'];
    }
    
    return [
        'por_tipo' => $por_tipo,
        'tendencia' => $tendencia
    ];
}

function exportarExcel($fecha_inicio, $fecha_fin, $tipo_reporte) {
    // Implementación básica de exportación Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="reporte_devoluciones_' . date('Y-m-d') . '.xls"');
    
    echo "Reporte de Devoluciones\n";
    echo "Fecha: " . $fecha_inicio . " a " . $fecha_fin . "\n";
    echo "Tipo: " . $tipo_reporte . "\n\n";
    
    // Aquí se implementaría la lógica de exportación
    echo "Datos del reporte...";
}

function exportarPDF($fecha_inicio, $fecha_fin, $tipo_reporte) {
    // Implementación básica de exportación PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="reporte_devoluciones_' . date('Y-m-d') . '.pdf"');
    
    // Aquí se implementaría la lógica de exportación PDF
    echo "PDF del reporte...";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Sistema de Devoluciones - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <?php include "header.php";?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
</head>

<body>
    <!-- Spinner End -->
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
                    <h6 class="mb-4" style="color:#0172b6;">Sistema de Devoluciones - <?php echo $row['Licencia']?></h6>
                    
                    <!-- Header con botones -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fa-solid fa-undo me-2"></i>Sistema de Devoluciones</h2>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="mostrarNuevaDevolucion()">
                                <i class="fa-solid fa-plus me-1"></i>Nueva Devolución
                            </button>
                            <button class="btn btn-secondary" onclick="mostrarListaDevoluciones()">
                                <i class="fa-solid fa-list me-1"></i>Lista
                            </button>
                            <button class="btn btn-info" onclick="mostrarProcesarDevoluciones()">
                                <i class="fa-solid fa-cogs me-1"></i>Procesar
                            </button>
                            <button class="btn btn-success" onclick="mostrarReportes()">
                                <i class="fa-solid fa-chart-bar me-1"></i>Reportes
                            </button>
                        </div>
                    </div>
                    
                    <!-- Búsqueda de Productos -->
                    <div id="busqueda-section" class="card" style="display: none;">
                        <div class="card-body">
                            <h5><i class="fa-solid fa-search me-2"></i>Buscar Productos</h5>
                            <p class="mb-3">Escanea o ingresa el código de barras del producto a devolver</p>
                            
                            <!-- Información del Usuario y Sucursal -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Usuario:</label>
                                    <input type="text" class="form-control" value="<?php echo $row['Nombre_Apellidos']; ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sucursal:</label>
                                    <input type="text" class="form-control" value="<?php echo $row['Nombre_Sucursal']; ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha:</label>
                                    <input type="text" class="form-control" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="codigoEscaneado" class="form-label">
                                            <i class="fas fa-barcode me-1"></i>
                                            <span>Producto</span>
                                        </label>
                                        <input type="text" class="form-control" name="codigoEscaneado" id="codigoEscaneado" 
                                               placeholder="Código de barras o nombre del producto..." autofocus>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipo-devolucion-default" class="form-label">
                                            <i class="fa-solid fa-tag me-1"></i>
                                            <span>Tipo de Devolución por Defecto</span>
                                        </label>
                                        <select id="tipo-devolucion-default" class="form-select">
                                            <option value="no_facturado">Producto no facturado</option>
                                            <option value="danado_recibir">Dañado al recibir</option>
                                            <option value="proximo_caducar">Próximo a caducar</option>
                                            <option value="caducado">Caducado</option>
                                            <option value="danado_roto">Dañado/Roto</option>
                                            <option value="solicitado_admin">Solicitado por administración</option>
                                            <option value="error_etiquetado">Error en etiquetado</option>
                                            <option value="defectuoso">Defectuoso</option>
                                            <option value="sobrante">Sobrante</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de Productos en Devolución -->
                    <div id="productos-lista" class="row" style="display: none;">
                        <div class="col-12">
                            <h5><i class="fa-solid fa-list me-2"></i>Productos a Devolver</h5>
                            <div id="productos-container">
                                <!-- Los productos se cargarán aquí dinámicamente -->
                            </div>
                            
                            <!-- Resumen y Acciones -->
                            <div class="total-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Observaciones Generales:</h6>
                                        <textarea id="observaciones-generales" class="form-control" rows="3" 
                                                  placeholder="Observaciones adicionales sobre la devolución..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-end">
                                            <h6>Total de Productos: <span id="total-productos">0</span></h6>
                                            <h6>Total de Unidades: <span id="total-unidades">0</span></h6>
                                            <div class="mt-3">
                                                <button class="btn btn-success me-2" onclick="procesarDevolucion()">
                                                    <i class="fa-solid fa-check me-1"></i>Procesar Devolución
                                                </button>
                                                <button class="btn btn-secondary" onclick="cancelarDevolucion()">
                                                    <i class="fa-solid fa-times me-1"></i>Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Procesar Devoluciones -->
                    <div id="procesar-devoluciones" class="card" style="display: none;">
                        <div class="card-header">
                            <h5><i class="fa-solid fa-cogs me-2"></i>Procesar Devoluciones Pendientes</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Filtrar por Estatus:</label>
                                    <select id="filtro-procesar" class="form-select">
                                        <option value="procesada">Procesadas</option>
                                        <option value="pendiente">Pendientes</option>
                                        <option value="cancelada">Canceladas</option>
                                        <option value="">Todas</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Inicio:</label>
                                    <input type="date" id="fecha-procesar-inicio" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Fin:</label>
                                    <input type="date" id="fecha-procesar-fin" class="form-control">
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Folio</th>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Productos</th>
                                            <th>Estatus</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-procesar-devoluciones">
                                        <!-- Los datos se cargarán aquí -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reportes de Devoluciones -->
                    <div id="reportes-section" class="card" style="display: none;">
                        <div class="card-header">
                            <h5><i class="fa-solid fa-chart-bar me-2"></i>Reportes de Devoluciones</h5>
                        </div>
                        <div class="card-body">
                            <!-- Filtros de Reportes -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Inicio:</label>
                                    <input type="date" id="reporte-fecha-inicio" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Fin:</label>
                                    <input type="date" id="reporte-fecha-fin" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipo de Reporte:</label>
                                    <select id="tipo-reporte" class="form-select">
                                        <option value="general">Reporte General</option>
                                        <option value="por_tipo">Por Tipo de Devolución</option>
                                        <option value="por_usuario">Por Usuario</option>
                                        <option value="por_producto">Por Producto</option>
                                        <option value="valor_monetario">Valor Monetario</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button class="btn btn-primary d-block w-100" onclick="generarReporte()">
                                        <i class="fa-solid fa-chart-line me-1"></i>Generar Reporte
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Estadísticas Generales -->
                            <div class="row mb-4" id="estadisticas-generales">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3 id="total-devoluciones">0</h3>
                                            <p class="mb-0">Total Devoluciones</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3 id="total-productos-devueltos">0</h3>
                                            <p class="mb-0">Productos Devueltos</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h3 id="valor-total-devoluciones">$0.00</h3>
                                            <p class="mb-0">Valor Total</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3 id="promedio-diario">0</h3>
                                            <p class="mb-0">Promedio Diario</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Gráficos -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Devoluciones por Tipo</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="grafico-por-tipo" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Tendencia por Fecha</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="grafico-tendencia" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tabla de Reporte Detallado -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h6>Reporte Detallado</h6>
                                    <div>
                                        <button class="btn btn-sm btn-success" onclick="exportarExcel()">
                                            <i class="fa-solid fa-file-excel me-1"></i>Excel
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="exportarPDF()">
                                            <i class="fa-solid fa-file-pdf me-1"></i>PDF
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="tabla-reporte">
                                            <thead class="table-dark" id="tabla-reporte-header">
                                                <!-- Headers dinámicos según tipo de reporte -->
                                            </thead>
                                            <tbody id="tabla-reporte-body">
                                                <!-- Datos dinámicos -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de Devoluciones -->
                    <div id="lista-devoluciones">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fa-solid fa-history me-2"></i>Historial de Devoluciones</h5>
                            </div>
                            <div class="card-body">
                                <!-- Filtros -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Fecha Inicio:</label>
                                        <input type="date" id="fecha-inicio" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Fecha Fin:</label>
                                        <input type="date" id="fecha-fin" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Estatus:</label>
                                        <select id="filtro-estatus" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="pendiente">Pendiente</option>
                                            <option value="procesada">Procesada</option>
                                            <option value="cancelada">Cancelada</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button class="btn btn-primary d-block w-100" onclick="filtrarDevoluciones()">
                                            <i class="fa-solid fa-filter me-1"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Tabla de Devoluciones -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Folio</th>
                                                <th>Fecha</th>
                                                <th>Usuario</th>
                                                <th>Sucursal</th>
                                                <th>Productos</th>
                                                <th>Estatus</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-devoluciones">
                                            <!-- Los datos se cargarán aquí -->
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
    
    <!-- Modal para Detalles de Devolución -->
    <div class="modal fade" id="modalDetalles" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-eye me-2"></i>Detalles de Devolución</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detalles-contenido">
                        <!-- Los detalles se cargarán aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="imprimirDevolucion()">
                        <i class="fa-solid fa-print me-1"></i>Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para Agregar Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i>Agregar Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-producto">
                        <div class="mb-3">
                            <label class="form-label">Código de Barras:</label>
                            <input type="text" id="modal-codigo-barras" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad:</label>
                            <input type="number" id="modal-cantidad" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Devolución:</label>
                            <select id="modal-tipo-devolucion" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach (getTiposDevolucion() as $key => $value): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones:</label>
                            <textarea id="modal-observaciones" class="form-control" rows="3" 
                                      placeholder="Motivo específico de la devolución..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="agregarProductoModal()">
                        <i class="fa-solid fa-plus me-1"></i>Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Variables globales
        let productosDevolucion = [];
        let scannerActivo = false;
        
        // Inicializar página
        var isScannerInput = false;

        $(document).ready(function() {
            cargarDevoluciones();
            
            // Auto-refresh cada 30 segundos
            setInterval(function() {
                if ($('#lista-devoluciones').is(':visible')) {
                    cargarDevoluciones();
                }
                if ($('#procesar-devoluciones').is(':visible')) {
                    cargarDevolucionesProcesar();
                }
            }, 30000);
            
            // Enter en búsqueda de productos (igual que RealizarVentas.php)
            $('#codigoEscaneado').keyup(function (event) {
                if (event.which === 13) { // Verifica si la tecla presionada es "Enter"
                    if (!isScannerInput) { // Verifica si el evento no viene del escáner
                        var codigoEscaneado = $('#codigoEscaneado').val();
                        buscarArticulo(codigoEscaneado);
                        event.preventDefault(); // Evita que el formulario se envíe al presionar "Enter"
                    }
                    isScannerInput = false; // Restablece la bandera del escáner
                }
            });
            
            // Autocompletado (igual que RealizarVentas.php)
            $('#codigoEscaneado').autocomplete({
                source: function (request, response) {
                    // Realiza una solicitud AJAX para obtener los resultados de autocompletado
                    $.ajax({
                        url: 'Controladores/autocompletadoAdministrativo.php',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                minLength: 3, // Especifica la cantidad mínima de caracteres para activar el autocompletado
                select: function (event, ui) {
                    // Cuando se selecciona un resultado del autocompletado, llamar a la función buscarArticulo() con el código seleccionado
                    var codigoEscaneado = ui.item.value;
                    isScannerInput = true; // Establece la bandera del escáner
                    $('#codigoEscaneado').val(codigoEscaneado);
                    buscarArticulo(codigoEscaneado);
                }
            });
        });
        
        // Mostrar nueva devolución
        function mostrarNuevaDevolucion() {
            $('#busqueda-section').show();
            $('#productos-lista').show();
            $('#lista-devoluciones').hide();
            $('#procesar-devoluciones').hide();
            $('#reportes-section').hide();
            $('#codigoEscaneado').focus();
            
            // Limpiar productos anteriores
            window.productosDevolucion = [];
            mostrarProductos([]);
            actualizarTotales();
        }
        
        // Mostrar procesar devoluciones
        function mostrarProcesarDevoluciones() {
            $('#busqueda-section').hide();
            $('#productos-lista').hide();
            $('#lista-devoluciones').hide();
            $('#procesar-devoluciones').show();
            $('#reportes-section').hide();
            cargarDevolucionesProcesar();
        }
        
        // Buscar artículo (igual que en RealizarVentas.php y RealizarTraspasos.php)
        function buscarArticulo(codigoEscaneado) {
            var formData = new FormData();
            formData.append('codigoEscaneado', codigoEscaneado);

            $.ajax({
                url: "Controladores/escaner_articulo_administrativo.php",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if ($.isEmptyObject(data)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Producto no encontrado',
                            text: 'El producto no existe en el inventario',
                            timer: 2000
                        });
                    } else if (data.codigo || data.descripcion) {
                        // Agregar directamente a la lista de devoluciones (sin modal)
                        agregarProductoDevolucion(data);
                    }
                    limpiarCampo();
                },
                error: function (data) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la búsqueda',
                        text: 'No se pudo buscar el producto'
                    });
                }
            });
        }
        
        // Agregar producto a devoluciones (sin modal, directo como en RealizarVentas.php)
        function agregarProductoDevolucion(producto) {
            if (!producto || (!producto.id && !producto.descripcion)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Producto inválido',
                    text: 'El producto no es válido'
                });
                return;
            }
            
            // Verificar si ya existe en la lista
            const existingIndex = window.productosDevolucion ? 
                window.productosDevolucion.findIndex(p => p.codigo_barras === producto.codigo) : -1;
            
            if (existingIndex >= 0) {
                // Si ya existe, incrementar cantidad
                window.productosDevolucion[existingIndex].cantidad += 1;
                mostrarProductos(window.productosDevolucion);
                actualizarTotales();
                return;
            }
            
            // Obtener el tipo de devolución seleccionado
            const tipoDevolucionSeleccionado = $('#tipo-devolucion-default').val() || 'no_facturado';
            
            // Agregar nuevo producto
            const nuevoProducto = {
                codigo_barras: producto.codigo,
                producto: {
                    id: producto.id,
                    codigo: producto.codigo,
                    nombre: producto.descripcion,
                    precio_venta: producto.precio,
                    precio_compra: producto.precio_compra || producto.precio,
                    stock: producto.stock || 0,
                    lote: producto.lote || null,
                    fecha_caducidad: producto.fecha_caducidad || null
                },
                cantidad: 1,
                tipo_devolucion: tipoDevolucionSeleccionado,
                observaciones: ''
            };
            
            if (!window.productosDevolucion) {
                window.productosDevolucion = [];
            }
            
            window.productosDevolucion.push(nuevoProducto);
            mostrarProductos(window.productosDevolucion);
            actualizarTotales();
        }
        
        // Limpiar campo de búsqueda
        function limpiarCampo() {
            $('#codigoEscaneado').val('');
            $('#codigoEscaneado').focus();
        }
        
        // Mostrar modal de producto
        function mostrarModalProducto(producto) {
            $('#modal-codigo-barras').val(producto.codigo);
            $('#modal-cantidad').val(1);
            $('#modal-tipo-devolucion').val('');
            $('#modal-observaciones').val('');
            
            // Limpiar información anterior
            $('#modalProducto .modal-body .alert').remove();
            
            // Mostrar información del producto
            const info = `
                <div class="alert alert-info">
                    <h6>${producto.nombre}</h6>
                    <p class="mb-1"><strong>Código:</strong> ${producto.codigo}</p>
                    <p class="mb-1"><strong>Stock:</strong> ${producto.stock}</p>
                    <p class="mb-1"><strong>Precio:</strong> $${producto.precio_venta}</p>
                    ${producto.lote ? `<p class="mb-1"><strong>Lote:</strong> ${producto.lote}</p>` : ''}
                    ${producto.fecha_caducidad ? `<p class="mb-0"><strong>Caducidad:</strong> ${producto.fecha_caducidad}</p>` : ''}
                </div>
            `;
            $('#modalProducto .modal-body').prepend(info);
            
            $('#modalProducto').modal('show');
        }
        
        // Agregar producto desde modal
        function agregarProductoModal() {
            const codigoBarras = $('#modal-codigo-barras').val();
            const cantidad = parseInt($('#modal-cantidad').val());
            const tipoDevolucion = $('#modal-tipo-devolucion').val();
            const observaciones = $('#modal-observaciones').val();
            
            if (!codigoBarras || !cantidad || !tipoDevolucion) {
                alert('Complete todos los campos requeridos');
                return;
            }
            
            // Obtener datos del producto desde la sesión temporal
            const productoData = window.productoSeleccionado;
            if (!productoData) {
                alert('Error: No se encontraron los datos del producto');
                return;
            }
            
            // Agregar a la lista local
            const itemKey = codigoBarras + '_' + (productoData.lote || 'default');
            const item = {
                codigo_barras: codigoBarras,
                producto: productoData,
                cantidad: cantidad,
                tipo_devolucion: tipoDevolucion,
                observaciones: observaciones
            };
            
            if (!window.productosDevolucion) {
                window.productosDevolucion = [];
            }
            
            // Verificar si ya existe
            const existingIndex = window.productosDevolucion.findIndex(p => p.codigo_barras === codigoBarras && p.producto.lote === productoData.lote);
            if (existingIndex >= 0) {
                window.productosDevolucion[existingIndex].cantidad += cantidad;
            } else {
                window.productosDevolucion.push(item);
            }
            
            $('#modalProducto').modal('hide');
            $('#codigoEscaneado').val('');
            mostrarProductos(window.productosDevolucion);
            actualizarTotales();
        }
        
        // Cargar productos en devolución
        function cargarProductosDevolucion() {
            $.ajax({
                url: 'Devoluciones.php',
                method: 'POST',
                data: {
                    action: 'obtener_productos_temp'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarProductos(response.productos);
                    }
                }
            });
        }
        
        // Mostrar productos
        function mostrarProductos(productos) {
            const container = $('#productos-container');
            container.empty();
            
            if (!productos || productos.length === 0) {
                container.html('<div class="alert alert-info text-center">No hay productos agregados</div>');
                return;
            }
            
            productos.forEach(function(item, index) {
                const producto = item.producto;
                const tipoDevolucion = getTipoDevolucionTexto(item.tipo_devolucion);
                const itemKey = item.codigo_barras + '_' + (producto.lote || 'default');
                
                const html = `
                    <div class="product-item" data-key="${itemKey}">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-1">${producto.nombre}</h6>
                                <small class="text-muted">Código: ${item.codigo_barras} | Lote: ${producto.lote || 'N/A'}</small>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" onchange="actualizarTipoDevolucionLocal('${itemKey}', this.value)">
                                    <option value="no_facturado" ${item.tipo_devolucion === 'no_facturado' ? 'selected' : ''}>No Facturado</option>
                                    <option value="danado_recibir" ${item.tipo_devolucion === 'danado_recibir' ? 'selected' : ''}>Dañado al Recibir</option>
                                    <option value="proximo_caducar" ${item.tipo_devolucion === 'proximo_caducar' ? 'selected' : ''}>Próximo a Caducar</option>
                                    <option value="caducado" ${item.tipo_devolucion === 'caducado' ? 'selected' : ''}>Caducado</option>
                                    <option value="danado_roto" ${item.tipo_devolucion === 'danado_roto' ? 'selected' : ''}>Dañado/Roto</option>
                                    <option value="solicitado_admin" ${item.tipo_devolucion === 'solicitado_admin' ? 'selected' : ''}>Solicitado por Admin</option>
                                    <option value="error_etiquetado" ${item.tipo_devolucion === 'error_etiquetado' ? 'selected' : ''}>Error en Etiquetado</option>
                                    <option value="defectuoso" ${item.tipo_devolucion === 'defectuoso' ? 'selected' : ''}>Defectuoso</option>
                                    <option value="sobrante" ${item.tipo_devolucion === 'sobrante' ? 'selected' : ''}>Sobrante</option>
                                    <option value="otro" ${item.tipo_devolucion === 'otro' ? 'selected' : ''}>Otro</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="number" class="form-control cantidad-input" 
                                           value="${item.cantidad}" min="1" max="${producto.stock}"
                                           onchange="actualizarCantidadLocal('${itemKey}', this.value)">
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="eliminarProductoLocal('${itemKey}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        ${item.observaciones ? `<div class="row mt-2"><div class="col-12"><small class="text-muted"><strong>Observaciones:</strong> ${item.observaciones}</small></div></div>` : ''}
                    </div>
                `;
                container.append(html);
            });
        }
        
        // Obtener texto del tipo de devolución
        function getTipoDevolucionTexto(tipo) {
            const tipos = {
                'no_facturado': 'No Facturado',
                'danado_recibir': 'Dañado al Recibir',
                'proximo_caducar': 'Próximo a Caducar',
                'caducado': 'Caducado',
                'danado_roto': 'Dañado/Roto',
                'solicitado_admin': 'Solicitado por Admin',
                'error_etiquetado': 'Error en Etiquetado',
                'defectuoso': 'Defectuoso',
                'sobrante': 'Sobrante',
                'otro': 'Otro'
            };
            return tipos[tipo] || tipo;
        }
        
        // Obtener color del tipo de devolución
        function getTipoColor(tipo) {
            const colores = {
                'no_facturado': 'primary',
                'danado_recibir': 'danger',
                'proximo_caducar': 'warning',
                'caducado': 'dark',
                'danado_roto': 'danger',
                'solicitado_admin': 'info',
                'error_etiquetado': 'secondary',
                'defectuoso': 'danger',
                'sobrante': 'success',
                'otro': 'light'
            };
            return colores[tipo] || 'secondary';
        }
        
        // Actualizar cantidad localmente
        function actualizarCantidadLocal(itemKey, nuevaCantidad) {
            if (!window.productosDevolucion) return;
            
            const item = window.productosDevolucion.find(p => {
                const key = p.codigo_barras + '_' + (p.producto.lote || 'default');
                return key === itemKey;
            });
            
            if (item) {
                item.cantidad = parseInt(nuevaCantidad);
                actualizarTotales();
            }
        }
        
        // Actualizar tipo de devolución localmente
        function actualizarTipoDevolucionLocal(itemKey, nuevoTipo) {
            if (!window.productosDevolucion) return;
            
            const item = window.productosDevolucion.find(p => {
                const key = p.codigo_barras + '_' + (p.producto.lote || 'default');
                return key === itemKey;
            });
            
            if (item) {
                item.tipo_devolucion = nuevoTipo;
            }
        }
        
        // Eliminar producto localmente
        function eliminarProductoLocal(itemKey) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: 'Se eliminará el producto de la devolución',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (!window.productosDevolucion) return;
                    
                    window.productosDevolucion = window.productosDevolucion.filter(p => {
                        const key = p.codigo_barras + '_' + (p.producto.lote || 'default');
                        return key !== itemKey;
                    });
                    
                    mostrarProductos(window.productosDevolucion);
                    actualizarTotales();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Producto eliminado',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Actualizar totales
        function actualizarTotales() {
            if (!window.productosDevolucion) {
                $('#total-productos').text('0');
                $('#total-unidades').text('0');
                return;
            }
            
            const totalProductos = window.productosDevolucion.length;
            const totalUnidades = window.productosDevolucion.reduce((sum, item) => sum + item.cantidad, 0);
            
            $('#total-productos').text(totalProductos);
            $('#total-unidades').text(totalUnidades);
        }
        
        // Procesar devolución
        function procesarDevolucion() {
            if (!window.productosDevolucion || window.productosDevolucion.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin productos',
                    text: 'No hay productos para procesar'
                });
                return;
            }
            
            const observaciones = $('#observaciones-generales').val();
            
            Swal.fire({
                title: '¿Procesar devolución?',
                text: 'Se procesará la devolución con ' + window.productosDevolucion.length + ' productos',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, procesar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'Devoluciones.php',
                        method: 'POST',
                        data: {
                            action: 'procesar_devolucion',
                            observaciones_generales: observaciones,
                            productos: JSON.stringify(window.productosDevolucion)
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Devolución procesada!',
                                    text: 'Folio: ' + response.folio,
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(() => {
                                    cancelarDevolucion();
                                    cargarDevoluciones();
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
                                text: 'Error al procesar devolución'
                            });
                        }
                    });
                }
            });
        }
        
        // Cancelar devolución
        function cancelarDevolucion() {
            Swal.fire({
                title: '¿Cancelar devolución?',
                text: 'Se perderán todos los productos agregados',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.productosDevolucion = [];
                    $('#busqueda-section').hide();
                    $('#productos-lista').hide();
                    $('#lista-devoluciones').show();
                    $('#codigoEscaneado').val('');
                    $('#observaciones-generales').val('');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Devolución cancelada',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Cargar devoluciones
        function cargarDevoluciones() {
            const fechaInicio = $('#fecha-inicio').val();
            const fechaFin = $('#fecha-fin').val();
            const estatus = $('#filtro-estatus').val();
            
            $.ajax({
                url: 'Devoluciones.php',
                method: 'POST',
                data: {
                    action: 'obtener_devoluciones',
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    estatus: estatus
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarDevoluciones(response.devoluciones);
                    }
                }
            });
        }
        
        // Cargar devoluciones para procesar
        function cargarDevolucionesProcesar() {
            const fechaInicio = $('#fecha-procesar-inicio').val();
            const fechaFin = $('#fecha-procesar-fin').val();
            const estatus = $('#filtro-procesar').val();
            
            $.ajax({
                url: 'Devoluciones.php',
                method: 'POST',
                data: {
                    action: 'obtener_devoluciones_procesar',
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    estatus: estatus
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarDevolucionesProcesar(response.devoluciones);
                    }
                }
            });
        }
        
        // Mostrar devoluciones para procesar
        function mostrarDevolucionesProcesar(devoluciones) {
            const tbody = $('#tabla-procesar-devoluciones');
            tbody.empty();
            
            if (devoluciones.length === 0) {
                tbody.html('<tr><td colspan="6" class="text-center">No hay devoluciones para procesar</td></tr>');
                return;
            }
            
            devoluciones.forEach(function(devolucion) {
                const estatusClass = `status-${devolucion.estatus}`;
                const html = `
                    <tr>
                        <td><strong>${devolucion.folio}</strong></td>
                        <td>${formatearFecha(devolucion.fecha)}</td>
                        <td>${devolucion.usuario_nombre || 'N/A'}</td>
                        <td>${devolucion.total_productos || 0}</td>
                        <td><span class="badge status-badge ${estatusClass}">${devolucion.estatus}</span></td>
                        <td>
                            <button class="btn btn-sm btn-info me-1" onclick="verDetallesProcesar(${devolucion.id})">
                                <i class="fa-solid fa-eye"></i> Ver
                            </button>
                            ${devolucion.estatus === 'procesada' ? 
                                `<button class="btn btn-sm btn-warning me-1" onclick="revertirDevolucion(${devolucion.id})">
                                    <i class="fa-solid fa-undo"></i> Revertir
                                </button>` : 
                                `<button class="btn btn-sm btn-success me-1" onclick="aprobarDevolucion(${devolucion.id})">
                                    <i class="fa-solid fa-check"></i> Aprobar
                                </button>`
                            }
                            <button class="btn btn-sm btn-danger" onclick="cancelarDevolucionProcesar(${devolucion.id})">
                                <i class="fa-solid fa-times"></i> Cancelar
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(html);
            });
        }
        
        // Mostrar devoluciones en tabla
        function mostrarDevoluciones(devoluciones) {
            const tbody = $('#tabla-devoluciones');
            tbody.empty();
            
            if (devoluciones.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center">No hay devoluciones registradas</td></tr>');
                return;
            }
            
            devoluciones.forEach(function(devolucion) {
                const estatusClass = `status-${devolucion.estatus}`;
                const html = `
                    <tr>
                        <td><strong>${devolucion.folio}</strong></td>
                        <td>${formatearFecha(devolucion.fecha)}</td>
                        <td>${devolucion.usuario_nombre || 'N/A'}</td>
                        <td>${devolucion.Nombre_Sucursal || 'N/A'}</td>
                        <td>${devolucion.total_productos || 0}</td>
                        <td><span class="badge status-badge ${estatusClass}">${devolucion.estatus}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="verDetalles(${devolucion.id})">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(html);
            });
        }
        
        // Ver detalles de devolución
        function verDetalles(devolucionId) {
            $.ajax({
                url: 'Devoluciones.php',
                method: 'POST',
                data: {
                    action: 'obtener_detalles',
                    devolucion_id: devolucionId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarDetalles(response.devolucion, response.detalles);
                    }
                }
            });
        }
        
        // Mostrar detalles en modal
        function mostrarDetalles(devolucion, detalles) {
            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><strong>Folio:</strong> ${devolucion.folio}</h6>
                        <h6><strong>Fecha:</strong> ${formatearFecha(devolucion.fecha)}</h6>
                        <h6><strong>Usuario:</strong> ${devolucion.usuario_nombre || 'N/A'}</h6>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Sucursal:</strong> ${devolucion.Nombre_Sucursal || 'N/A'}</h6>
                        <h6><strong>Estatus:</strong> <span class="badge status-${devolucion.estatus}">${devolucion.estatus}</span></h6>
                    </div>
                </div>
            `;
            
            if (devolucion.observaciones_generales) {
                html += `<div class="alert alert-info"><strong>Observaciones:</strong> ${devolucion.observaciones_generales}</div>`;
            }
            
            html += `
                <h6>Productos Devoluidos:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Lote</th>
                                <th>Cantidad</th>
                                <th>Tipo</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            detalles.forEach(function(detalle) {
                html += `
                    <tr>
                        <td>${detalle.nombre_producto}</td>
                        <td>${detalle.codigo_barras}</td>
                        <td>${detalle.lote}</td>
                        <td>${detalle.cantidad}</td>
                        <td><span class="badge bg-${getTipoColor(detalle.tipo_devolucion)}">${getTipoDevolucionTexto(detalle.tipo_devolucion)}</span></td>
                        <td>${detalle.observaciones || '-'}</td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            $('#detalles-contenido').html(html);
            $('#modalDetalles').modal('show');
        }
        
        // Filtrar devoluciones
        function filtrarDevoluciones() {
            cargarDevoluciones();
        }
        
        // Mostrar reportes
        function mostrarReportes() {
            // Implementar funcionalidad de reportes
            alert('Funcionalidad de reportes en desarrollo');
        }
        
        // Activar cámara (placeholder)
        function activarCamara() {
            alert('Funcionalidad de cámara en desarrollo');
        }
        
        // Imprimir devolución
        function imprimirDevolucion() {
            window.print();
        }
        
        // Formatear fecha
        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-ES');
        }
        
        // Ver detalles para procesar
        function verDetallesProcesar(devolucionId) {
            $.ajax({
                url: 'Devoluciones.php',
                method: 'POST',
                data: {
                    action: 'obtener_detalles_devolucion',
                    devolucion_id: devolucionId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarModalDetallesProcesar(response.devolucion, response.detalles);
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
        
        // Aprobar devolución
        function aprobarDevolucion(devolucionId) {
            Swal.fire({
                title: '¿Aprobar devolución?',
                text: 'La devolución será aprobada y procesada',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'Devoluciones.php',
                        method: 'POST',
                        data: {
                            action: 'aprobar_devolucion',
                            devolucion_id: devolucionId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Devolución aprobada',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    cargarDevolucionesProcesar();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            });
        }
        
        // Revertir devolución
        function revertirDevolucion(devolucionId) {
            Swal.fire({
                title: '¿Revertir devolución?',
                text: 'Esto restaurará el stock y revertirá la devolución',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, revertir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'Devoluciones.php',
                        method: 'POST',
                        data: {
                            action: 'revertir_devolucion',
                            devolucion_id: devolucionId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Devolución revertida',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    cargarDevolucionesProcesar();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            });
        }
        
        // Cancelar devolución
        function cancelarDevolucionProcesar(devolucionId) {
            Swal.fire({
                title: '¿Cancelar devolución?',
                text: 'La devolución será cancelada',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'Devoluciones.php',
                        method: 'POST',
                        data: {
                            action: 'cancelar_devolucion',
                            devolucion_id: devolucionId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Devolución cancelada',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    cargarDevolucionesProcesar();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            });
        }
        
        // ==================== FUNCIONES DE REPORTES ====================
        
        // Mostrar lista de devoluciones
        function mostrarListaDevoluciones() {
            $('#busqueda-section').hide();
            $('#productos-lista').hide();
            $('#lista-devoluciones').show();
            $('#procesar-devoluciones').hide();
            $('#reportes-section').hide();
            cargarDevoluciones();
        }
        
        // Mostrar reportes
        function mostrarReportes() {
            $('#busqueda-section').hide();
            $('#productos-lista').hide();
            $('#lista-devoluciones').hide();
            $('#procesar-devoluciones').hide();
            $('#reportes-section').show();
            
            // Establecer fechas por defecto (último mes)
            const hoy = new Date();
            const hace30dias = new Date();
            hace30dias.setDate(hoy.getDate() - 30);
            
            $('#reporte-fecha-inicio').val(hace30dias.toISOString().split('T')[0]);
            $('#reporte-fecha-fin').val(hoy.toISOString().split('T')[0]);
            
            // Cargar reporte inicial
            generarReporte();
        }
        
        // Generar reporte
        function generarReporte() {
            const fechaInicio = $('#reporte-fecha-inicio').val();
            const fechaFin = $('#reporte-fecha-fin').val();
            const tipoReporte = $('#tipo-reporte').val();
            
            if (!fechaInicio || !fechaFin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas requeridas',
                    text: 'Seleccione las fechas de inicio y fin'
                });
                return;
            }
            
            $.ajax({
                url: 'Devoluciones.php',
                method: 'POST',
                data: {
                    action: 'generar_reporte',
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    tipo_reporte: tipoReporte
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarEstadisticas(response.estadisticas);
                        mostrarTablaReporte(response.datos, tipoReporte);
                        generarGraficos(response.graficos);
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
                        text: 'Error al generar reporte'
                    });
                }
            });
        }
        
        // Mostrar estadísticas generales
        function mostrarEstadisticas(estadisticas) {
            $('#total-devoluciones').text(estadisticas.total_devoluciones || 0);
            $('#total-productos-devueltos').text(estadisticas.total_productos || 0);
            $('#valor-total-devoluciones').text('$' + (estadisticas.valor_total || 0).toFixed(2));
            $('#promedio-diario').text((estadisticas.promedio_diario || 0).toFixed(1));
        }
        
        // Mostrar tabla de reporte
        function mostrarTablaReporte(datos, tipoReporte) {
            const header = $('#tabla-reporte-header');
            const body = $('#tabla-reporte-body');
            
            header.empty();
            body.empty();
            
            if (!datos || datos.length === 0) {
                body.html('<tr><td colspan="100%" class="text-center">No hay datos para mostrar</td></tr>');
                return;
            }
            
            // Headers dinámicos según tipo de reporte
            let headerHtml = '<tr>';
            switch (tipoReporte) {
                case 'general':
                    headerHtml += '<th>Folio</th><th>Fecha</th><th>Usuario</th><th>Productos</th><th>Valor</th><th>Estatus</th>';
                    break;
                case 'por_tipo':
                    headerHtml += '<th>Tipo de Devolución</th><th>Cantidad</th><th>Productos</th><th>Valor Total</th><th>Porcentaje</th>';
                    break;
                case 'por_usuario':
                    headerHtml += '<th>Usuario</th><th>Devoluciones</th><th>Productos</th><th>Valor Total</th><th>Promedio</th>';
                    break;
                case 'por_producto':
                    headerHtml += '<th>Producto</th><th>Código</th><th>Cantidad Devuelta</th><th>Valor</th><th>Motivo Principal</th>';
                    break;
                case 'valor_monetario':
                    headerHtml += '<th>Fecha</th><th>Devoluciones</th><th>Valor Total</th><th>Valor Promedio</th><th>Tipo Principal</th>';
                    break;
            }
            headerHtml += '</tr>';
            header.html(headerHtml);
            
            // Datos dinámicos
            datos.forEach(function(item) {
                let rowHtml = '<tr>';
                switch (tipoReporte) {
                    case 'general':
                        rowHtml += `<td>${item.folio}</td>`;
                        rowHtml += `<td>${formatearFecha(item.fecha)}</td>`;
                        rowHtml += `<td>${item.usuario}</td>`;
                        rowHtml += `<td>${item.productos}</td>`;
                        rowHtml += `<td>$${parseFloat(item.valor).toFixed(2)}</td>`;
                        rowHtml += `<td><span class="badge status-${item.estatus}">${item.estatus}</span></td>`;
                        break;
                    case 'por_tipo':
                        rowHtml += `<td>${getTipoDevolucionTexto(item.tipo)}</td>`;
                        rowHtml += `<td>${item.cantidad}</td>`;
                        rowHtml += `<td>${item.productos}</td>`;
                        rowHtml += `<td>$${parseFloat(item.valor_total).toFixed(2)}</td>`;
                        rowHtml += `<td>${parseFloat(item.porcentaje).toFixed(1)}%</td>`;
                        break;
                    case 'por_usuario':
                        rowHtml += `<td>${item.usuario}</td>`;
                        rowHtml += `<td>${item.devoluciones}</td>`;
                        rowHtml += `<td>${item.productos}</td>`;
                        rowHtml += `<td>$${parseFloat(item.valor_total).toFixed(2)}</td>`;
                        rowHtml += `<td>$${parseFloat(item.promedio).toFixed(2)}</td>`;
                        break;
                    case 'por_producto':
                        rowHtml += `<td>${item.producto}</td>`;
                        rowHtml += `<td>${item.codigo}</td>`;
                        rowHtml += `<td>${item.cantidad}</td>`;
                        rowHtml += `<td>$${parseFloat(item.valor).toFixed(2)}</td>`;
                        rowHtml += `<td>${getTipoDevolucionTexto(item.motivo_principal)}</td>`;
                        break;
                    case 'valor_monetario':
                        rowHtml += `<td>${formatearFecha(item.fecha)}</td>`;
                        rowHtml += `<td>${item.devoluciones}</td>`;
                        rowHtml += `<td>$${parseFloat(item.valor_total).toFixed(2)}</td>`;
                        rowHtml += `<td>$${parseFloat(item.valor_promedio).toFixed(2)}</td>`;
                        rowHtml += `<td>${getTipoDevolucionTexto(item.tipo_principal)}</td>`;
                        break;
                }
                rowHtml += '</tr>';
                body.append(rowHtml);
            });
        }
        
        // Generar gráficos (usando Chart.js básico)
        function generarGraficos(datosGraficos) {
            // Gráfico por tipo
            const ctxTipo = document.getElementById('grafico-por-tipo').getContext('2d');
            if (window.chartTipo) window.chartTipo.destroy();
            
            window.chartTipo = new Chart(ctxTipo, {
                type: 'doughnut',
                data: {
                    labels: datosGraficos.por_tipo.labels,
                    datasets: [{
                        data: datosGraficos.por_tipo.valores,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Gráfico de tendencia
            const ctxTendencia = document.getElementById('grafico-tendencia').getContext('2d');
            if (window.chartTendencia) window.chartTendencia.destroy();
            
            window.chartTendencia = new Chart(ctxTendencia, {
                type: 'line',
                data: {
                    labels: datosGraficos.tendencia.fechas,
                    datasets: [{
                        label: 'Devoluciones',
                        data: datosGraficos.tendencia.valores,
                        borderColor: '#36A2EB',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Exportar a Excel
        function exportarExcel() {
            const fechaInicio = $('#reporte-fecha-inicio').val();
            const fechaFin = $('#reporte-fecha-fin').val();
            const tipoReporte = $('#tipo-reporte').val();
            
            window.open(`Devoluciones.php?action=exportar_excel&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&tipo_reporte=${tipoReporte}`, '_blank');
        }
        
        // Exportar a PDF
        function exportarPDF() {
            const fechaInicio = $('#reporte-fecha-inicio').val();
            const fechaFin = $('#reporte-fecha-fin').val();
            const tipoReporte = $('#tipo-reporte').val();
            
            window.open(`Devoluciones.php?action=exportar_pdf&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&tipo_reporte=${tipoReporte}`, '_blank');
        }
    </script>
    
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .product-item {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
        }
        .tipo-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }
        .cantidad-input {
            width: 80px;
        }
        .total-section {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
        }
        .status-pendiente { background-color: #ffc107; color: #000; }
        .status-procesada { background-color: #28a745; color: #fff; }
        .status-cancelada { background-color: #dc3545; color: #fff; }
    </style>

    <!-- Footer Start -->
    <?php include "footer.php";?>

</body>
</html>