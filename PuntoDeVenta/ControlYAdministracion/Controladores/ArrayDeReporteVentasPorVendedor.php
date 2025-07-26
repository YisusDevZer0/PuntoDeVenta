<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include("../dbconect.php");
    include("ControladorUsuario.php");
    
    // Obtener parámetros de filtro
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
    $sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
    
    // Consulta SQL para ventas por vendedor con JOINs
    $sql = "SELECT 
        v.AgregadoPor AS Vendedor,
        s.Nombre_Sucursal AS Sucursal,
        SUM(v.Total_Venta) AS Total_Ventas,
        SUM(v.Importe) AS Total_Importe,
        SUM(v.DescuentoAplicado) AS Total_Descuento,
        COUNT(*) AS Numero_Transacciones,
        AVG(v.Total_Venta) AS Promedio_Venta,
        MIN(v.Fecha_venta) AS Primera_Venta,
        MAX(v.Fecha_venta) AS Ultima_Venta
    FROM Ventas_POS v
    LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
    WHERE v.Fecha_venta BETWEEN ? AND ?
    AND v.Estatus = 'Pagado'
    AND v.AgregadoPor IS NOT NULL
    AND v.AgregadoPor != ''";
    
    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND v.Fk_sucursal = ?";
    }
    
    $sql .= " GROUP BY v.AgregadoPor, s.Nombre_Sucursal
    ORDER BY Total_Importe DESC";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    if (!empty($sucursal)) {
        $stmt->bind_param("sss", $fecha_inicio, $fecha_fin, $sucursal);
    } else {
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception('Error al obtener resultados: ' . $stmt->error);
    }
    
    // Procesar resultados
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "Vendedor" => $row['Vendedor'] ?: 'Sin vendedor',
            "Sucursal" => $row['Sucursal'] ?: 'Sucursal no encontrada',
            "Total_Ventas" => '$' . number_format($row['Total_Ventas'], 2),
            "Total_Importe" => '$' . number_format($row['Total_Importe'], 2),
            "Total_Descuento" => '$' . number_format($row['Total_Descuento'], 2),
            "Numero_Transacciones" => number_format($row['Numero_Transacciones']),
            "Promedio_Venta" => '$' . number_format($row['Promedio_Venta'], 2),
            "Primera_Venta" => $row['Primera_Venta'] ? date('d/m/Y', strtotime($row['Primera_Venta'])) : '',
            "Ultima_Venta" => $row['Ultima_Venta'] ? date('d/m/Y', strtotime($row['Ultima_Venta'])) : ''
        ];
    }
    
    // Construir respuesta JSON para DataTables
    $response = [
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => count($data),
        "recordsFiltered" => count($data),
        "data" => $data
    ];
    
    // Configurar headers para JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    
    // Cerrar conexión
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log('Error en ArrayDeReporteVentasPorVendedor.php: ' . $e->getMessage());
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "error" => true,
        "message" => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 