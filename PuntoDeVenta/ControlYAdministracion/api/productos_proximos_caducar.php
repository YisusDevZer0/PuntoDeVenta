<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../Consultas/db_connect.php";

try {
    $sucursal = isset($_GET['sucursal']) ? (int)$_GET['sucursal'] : null;
    $estado = isset($_GET['estado']) ? $_GET['estado'] : '';
    $tipo_alerta = isset($_GET['tipo_alerta']) ? $_GET['tipo_alerta'] : '';
    
    // Construir consulta base
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
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) < 0 THEN 'vencido'
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 90 THEN '3_meses'
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 180 THEN '6_meses'
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 270 THEN '9_meses'
                    ELSE 'normal'
                END as tipo_alerta_calculada
            FROM productos_lotes_caducidad plc
            LEFT JOIN Sucursales s ON plc.sucursal_id = s.ID_Sucursal
            WHERE plc.estado IN ('activo', 'agotado')";
    
    $params = [];
    $types = '';
    
    // Filtro por sucursal
    if ($sucursal) {
        $sql .= " AND plc.sucursal_id = ?";
        $params[] = $sucursal;
        $types .= 'i';
    }
    
    // Filtro por estado
    if ($estado) {
        $sql .= " AND plc.estado = ?";
        $params[] = $estado;
        $types .= 's';
    }
    
    // Filtro por tipo de alerta
    if ($tipo_alerta) {
        switch ($tipo_alerta) {
            case 'vencido':
                $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) < 0";
                break;
            case '3_meses':
                $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) BETWEEN 0 AND 90";
                break;
            case '6_meses':
                $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) BETWEEN 91 AND 180";
                break;
            case '9_meses':
                $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) BETWEEN 181 AND 270";
                break;
        }
    }
    
    $sql .= " ORDER BY plc.fecha_caducidad ASC, plc.nombre_producto ASC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $productos = [];
    while ($row = $result->fetch_assoc()) {
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
            'fecha_registro' => $row['fecha_registro'],
            'dias_restantes' => $row['dias_restantes'],
            'tipo_alerta' => $row['tipo_alerta_calculada']
        ];
    }
    
    // Obtener estadísticas
    $sql_stats = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN DATEDIFF(fecha_caducidad, CURDATE()) BETWEEN 0 AND 90 THEN 1 ELSE 0 END) as alerta_3_meses,
                    SUM(CASE WHEN DATEDIFF(fecha_caducidad, CURDATE()) BETWEEN 91 AND 180 THEN 1 ELSE 0 END) as alerta_6_meses,
                    SUM(CASE WHEN DATEDIFF(fecha_caducidad, CURDATE()) BETWEEN 181 AND 270 THEN 1 ELSE 0 END) as alerta_9_meses,
                    SUM(CASE WHEN DATEDIFF(fecha_caducidad, CURDATE()) < 0 THEN 1 ELSE 0 END) as vencidos
                  FROM productos_lotes_caducidad 
                  WHERE estado IN ('activo', 'agotado')";
    
    if ($sucursal) {
        $sql_stats .= " AND sucursal_id = ?";
    }
    
    $stmt_stats = $conn->prepare($sql_stats);
    if ($sucursal) {
        $stmt_stats->bind_param("i", $sucursal);
    }
    $stmt_stats->execute();
    $stats = $stmt_stats->get_result()->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'estadisticas' => $stats
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
