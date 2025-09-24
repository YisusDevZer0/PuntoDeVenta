<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../Consultas/db_connect.php";

try {
    $sucursal = isset($_GET['sucursal']) ? (int)$_GET['sucursal'] : null;
    $fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : null;
    $fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : null;
    $tipo_reporte = isset($_GET['tipo_reporte']) ? $_GET['tipo_reporte'] : 'general';
    
    $sql = "SELECT 
                plc.id_lote,
                plc.cod_barra,
                plc.nombre_producto,
                plc.lote,
                plc.fecha_caducidad,
                plc.cantidad_actual,
                plc.estado,
                s.Nombre_Sucursal,
                plc.proveedor,
                plc.precio_compra,
                plc.precio_venta,
                plc.fecha_registro,
                DATEDIFF(plc.fecha_caducidad, CURDATE()) as dias_restantes,
                CASE 
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) < 0 THEN 'Vencido'
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 90 THEN '3 Meses'
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 180 THEN '6 Meses'
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 270 THEN '9 Meses'
                    ELSE 'Normal'
                END as tipo_alerta
            FROM productos_lotes_caducidad plc
            LEFT JOIN Sucursales s ON plc.sucursal_id = s.ID_Sucursal
            WHERE 1=1";
    
    $params = [];
    $types = '';
    
    // Filtro por sucursal
    if ($sucursal) {
        $sql .= " AND plc.sucursal_id = ?";
        $params[] = $sucursal;
        $types .= 'i';
    }
    
    // Filtro por fechas
    if ($fecha_desde) {
        $sql .= " AND plc.fecha_caducidad >= ?";
        $params[] = $fecha_desde;
        $types .= 's';
    }
    
    if ($fecha_hasta) {
        $sql .= " AND plc.fecha_caducidad <= ?";
        $params[] = $fecha_hasta;
        $types .= 's';
    }
    
    // Filtro por tipo de reporte
    switch ($tipo_reporte) {
        case 'vencidos':
            $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) < 0";
            break;
        case 'proximos_3_meses':
            $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) BETWEEN 0 AND 90";
            break;
        case 'proximos_6_meses':
            $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) BETWEEN 91 AND 180";
            break;
        case 'proximos_9_meses':
            $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) BETWEEN 181 AND 270";
            break;
    }
    
    $sql .= " ORDER BY plc.fecha_caducidad ASC, s.Nombre_Sucursal ASC, plc.nombre_producto ASC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $productos = [];
    $total_valor = 0;
    
    while ($row = $result->fetch_assoc()) {
        $valor_lote = $row['cantidad_actual'] * $row['precio_venta'];
        $total_valor += $valor_lote;
        
        $productos[] = [
            'id_lote' => $row['id_lote'],
            'cod_barra' => $row['cod_barra'],
            'nombre_producto' => $row['nombre_producto'],
            'lote' => $row['lote'],
            'fecha_caducidad' => $row['fecha_caducidad'],
            'cantidad_actual' => $row['cantidad_actual'],
            'estado' => $row['estado'],
            'sucursal' => $row['Nombre_Sucursal'],
            'proveedor' => $row['proveedor'],
            'precio_compra' => $row['precio_compra'],
            'precio_venta' => $row['precio_venta'],
            'valor_lote' => $valor_lote,
            'fecha_registro' => $row['fecha_registro'],
            'dias_restantes' => $row['dias_restantes'],
            'tipo_alerta' => $row['tipo_alerta']
        ];
    }
    
    // Obtener estadísticas del reporte
    $sql_stats = "SELECT 
                    COUNT(*) as total_productos,
                    SUM(cantidad_actual) as total_cantidad,
                    SUM(cantidad_actual * precio_venta) as valor_total,
                    COUNT(DISTINCT sucursal_id) as sucursales_afectadas,
                    COUNT(DISTINCT proveedor) as proveedores_afectados
                  FROM productos_lotes_caducidad 
                  WHERE 1=1";
    
    if ($sucursal) {
        $sql_stats .= " AND sucursal_id = ?";
    }
    if ($fecha_desde) {
        $sql_stats .= " AND fecha_caducidad >= ?";
    }
    if ($fecha_hasta) {
        $sql_stats .= " AND fecha_caducidad <= ?";
    }
    
    $stmt_stats = $conn->prepare($sql_stats);
    if (!empty($params)) {
        $stmt_stats->bind_param($types, ...$params);
    }
    $stmt_stats->execute();
    $stats = $stmt_stats->get_result()->fetch_assoc();
    
    // Obtener resumen por sucursal
    $sql_resumen_sucursal = "SELECT 
                                s.Nombre_Sucursal,
                                COUNT(*) as total_lotes,
                                SUM(plc.cantidad_actual) as total_cantidad,
                                SUM(plc.cantidad_actual * plc.precio_venta) as valor_total
                             FROM productos_lotes_caducidad plc
                             LEFT JOIN Sucursales s ON plc.sucursal_id = s.ID_Sucursal
                             WHERE 1=1";
    
    if ($sucursal) {
        $sql_resumen_sucursal .= " AND plc.sucursal_id = ?";
    }
    if ($fecha_desde) {
        $sql_resumen_sucursal .= " AND plc.fecha_caducidad >= ?";
    }
    if ($fecha_hasta) {
        $sql_resumen_sucursal .= " AND plc.fecha_caducidad <= ?";
    }
    
    $sql_resumen_sucursal .= " GROUP BY plc.sucursal_id, s.Nombre_Sucursal ORDER BY s.Nombre_Sucursal";
    
    $stmt_resumen = $conn->prepare($sql_resumen_sucursal);
    if (!empty($params)) {
        $stmt_resumen->bind_param($types, ...$params);
    }
    $stmt_resumen->execute();
    $resumen_sucursal = $stmt_resumen->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'estadisticas' => $stats,
        'resumen_sucursal' => $resumen_sucursal,
        'filtros_aplicados' => [
            'sucursal' => $sucursal,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'tipo_reporte' => $tipo_reporte
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
