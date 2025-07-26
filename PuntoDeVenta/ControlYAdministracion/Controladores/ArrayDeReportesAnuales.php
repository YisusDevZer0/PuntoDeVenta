<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include("../dbconect.php");
    include("ControladorUsuario.php");
    
    // Obtener parámetros de filtro
    $anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');
    $sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
    $tipo_periodo = isset($_GET['tipo_periodo']) ? $_GET['tipo_periodo'] : 'mes';
    
    // Construir la consulta SQL según el tipo de período
    $sql = "";
    
    switch($tipo_periodo) {
        case 'mes':
            $sql = "SELECT 
                CONCAT(YEAR(v.Fecha_venta), '-', LPAD(MONTH(v.Fecha_venta), 2, '0')) AS Periodo,
                SUM(v.Total_Venta) AS Total_Ventas,
                SUM(v.Importe) AS Total_Importe,
                SUM(v.DescuentoAplicado) AS Total_Descuento,
                COUNT(*) AS Numero_Transacciones,
                AVG(v.Total_Venta) AS Promedio_Venta,
                SUM(v.Cantidad_Venta) AS Productos_Vendidos,
                COUNT(DISTINCT v.Cliente) AS Clientes_Atendidos
            FROM Ventas_POS v
            LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
            WHERE YEAR(v.Fecha_venta) = ?
            AND v.Estatus = 'Pagado'";
            break;
            
        case 'trimestre':
            $sql = "SELECT 
                CONCAT(YEAR(v.Fecha_venta), '-Q', QUARTER(v.Fecha_venta)) AS Periodo,
                SUM(v.Total_Venta) AS Total_Ventas,
                SUM(v.Importe) AS Total_Importe,
                SUM(v.DescuentoAplicado) AS Total_Descuento,
                COUNT(*) AS Numero_Transacciones,
                AVG(v.Total_Venta) AS Promedio_Venta,
                SUM(v.Cantidad_Venta) AS Productos_Vendidos,
                COUNT(DISTINCT v.Cliente) AS Clientes_Atendidos
            FROM Ventas_POS v
            LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
            WHERE YEAR(v.Fecha_venta) = ?
            AND v.Estatus = 'Pagado'";
            break;
            
        case 'semana':
            $sql = "SELECT 
                CONCAT(YEAR(v.Fecha_venta), '-W', LPAD(WEEK(v.Fecha_venta), 2, '0')) AS Periodo,
                SUM(v.Total_Venta) AS Total_Ventas,
                SUM(v.Importe) AS Total_Importe,
                SUM(v.DescuentoAplicado) AS Total_Descuento,
                COUNT(*) AS Numero_Transacciones,
                AVG(v.Total_Venta) AS Promedio_Venta,
                SUM(v.Cantidad_Venta) AS Productos_Vendidos,
                COUNT(DISTINCT v.Cliente) AS Clientes_Atendidos
            FROM Ventas_POS v
            LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
            WHERE YEAR(v.Fecha_venta) = ?
            AND v.Estatus = 'Pagado'";
            break;
    }
    
    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND v.Fk_sucursal = ?";
    }
    
    $sql .= " GROUP BY Periodo ORDER BY Periodo ASC";
    
    // Preparar la consulta
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $con->error);
    }
    
    if (!empty($sucursal)) {
        $stmt->bind_param("ss", $anio, $sucursal);
    } else {
        $stmt->bind_param("s", $anio);
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
            "Periodo" => $row['Periodo'],
            "Total_Ventas" => '$' . number_format($row['Total_Ventas'], 2),
            "Total_Importe" => '$' . number_format($row['Total_Importe'], 2),
            "Total_Descuento" => '$' . number_format($row['Total_Descuento'], 2),
            "Numero_Transacciones" => number_format($row['Numero_Transacciones']),
            "Promedio_Venta" => '$' . number_format($row['Promedio_Venta'], 2),
            "Productos_Vendidos" => number_format($row['Productos_Vendidos']),
            "Clientes_Atendidos" => number_format($row['Clientes_Atendidos'])
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
    error_log('Error en ArrayDeReportesAnuales.php: ' . $e->getMessage());
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "error" => true,
        "message" => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 