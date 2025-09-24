<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    // Por ahora, devolver datos de ejemplo hasta que se implementen las tablas
    $productos = [];
    $estadisticas = [
        'total' => 0,
        'alerta_3_meses' => 0,
        'alerta_6_meses' => 0,
        'alerta_9_meses' => 0,
        'vencidos' => 0
    ];
    
    // Verificar si las tablas existen
    $checkTable = $con->query("SHOW TABLES LIKE 'productos_lotes_caducidad'");
    if ($checkTable->num_rows > 0) {
        // Las tablas existen, obtener datos reales
        $sql = "SELECT 
                    plc.id_lote,
                    plc.cod_barra,
                    plc.nombre_producto,
                    plc.lote,
                    plc.fecha_caducidad,
                    plc.cantidad_actual,
                    plc.estado,
                    s.Nombre_Sucursal as sucursal,
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
                    END as tipo_alerta
                FROM productos_lotes_caducidad plc
                LEFT JOIN Sucursales s ON plc.sucursal_id = s.ID_Sucursal
                WHERE plc.estado IN ('activo', 'agotado')
                ORDER BY plc.fecha_caducidad ASC";
        
        $result = $con->query($sql);
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
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
        
        $result_stats = $con->query($sql_stats);
        $estadisticas = $result_stats->fetch_assoc();
    } else {
        // Las tablas no existen, mostrar mensaje informativo
        $productos = [];
        $estadisticas = [
            'total' => 0,
            'alerta_3_meses' => 0,
            'alerta_6_meses' => 0,
            'alerta_9_meses' => 0,
            'vencidos' => 0,
            'mensaje' => 'Las tablas del módulo de caducados no han sido creadas. Ejecute el script SQL primero.'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'estadisticas' => $estadisticas
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($con)) {
        $con->close();
    }
}
?>
