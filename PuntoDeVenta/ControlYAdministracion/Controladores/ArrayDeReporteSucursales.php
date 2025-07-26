<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include("../dbconect.php");
    include("ControladorUsuario.php");
    
    // Obtener parámetros de filtro
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
    
    // Consulta SQL para rendimiento por sucursal
    $sql = "SELECT 
        s.Nombre_Sucursal AS Sucursal,
        SUM(v.Total_Venta) AS Total_Ventas,
        SUM(v.Importe) AS Total_Importe,
        SUM(v.DescuentoAplicado) AS Total_Descuento,
        COUNT(*) AS Numero_Transacciones,
        AVG(v.Total_Venta) AS Promedio_Venta,
        SUM(v.Cantidad_Venta) AS Productos_Vendidos,
        COUNT(DISTINCT v.Cliente) AS Clientes_Atendidos,
        MAX(v.Fecha_venta) AS Ultima_Venta
    FROM Ventas_POS v
    LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
    WHERE v.Fecha_venta BETWEEN ? AND ?
    AND v.Estatus = 'Pagado'
    AND s.Nombre_Sucursal IS NOT NULL
    GROUP BY s.ID_Sucursal, s.Nombre_Sucursal
    ORDER BY Total_Importe DESC";
    
    // Preparar la consulta
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $con->error);
    }
    
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    
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
            "Sucursal" => $row['Sucursal'] ?: 'Sucursal no encontrada',
            "Total_Ventas" => '$' . number_format($row['Total_Ventas'], 2),
            "Total_Importe" => '$' . number_format($row['Total_Importe'], 2),
            "Total_Descuento" => '$' . number_format($row['Total_Descuento'], 2),
            "Numero_Transacciones" => number_format($row['Numero_Transacciones']),
            "Promedio_Venta" => '$' . number_format($row['Promedio_Venta'], 2),
            "Productos_Vendidos" => number_format($row['Productos_Vendidos']),
            "Clientes_Atendidos" => number_format($row['Clientes_Atendidos']),
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
    $con->close();
    
} catch (Exception $e) {
    error_log('Error en ArrayDeReporteSucursales.php: ' . $e->getMessage());
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "error" => true,
        "message" => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 