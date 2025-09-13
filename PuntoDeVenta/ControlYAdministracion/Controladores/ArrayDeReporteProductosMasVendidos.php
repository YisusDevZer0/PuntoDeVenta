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
    $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 25;
    
    // Consulta SQL para productos más vendidos
    $sql = "SELECT 
        v.ID_Prod_POS,
        v.Cod_Barra,
        v.Nombre_Prod,
        c.Nom_Cat AS Categoria,
        SUM(v.Cantidad_Venta) AS Total_Vendido,
        SUM(v.Importe) AS Total_Importe,
        AVG(v.Total_Venta) AS Promedio_Venta,
        COUNT(*) AS Numero_Ventas,
        MAX(v.Fecha_venta) AS Ultima_Venta
    FROM Ventas_POS v
    LEFT JOIN Productos_POS p ON v.ID_Prod_POS = p.ID_Prod_POS
    LEFT JOIN Categorias_POS c ON p.FkCategoria = c.Cat_ID
    LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
    WHERE v.Fecha_venta BETWEEN ? AND ?
    AND v.Estatus = 'Pagado'
    AND v.ID_Prod_POS IS NOT NULL";
    
    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND v.Fk_sucursal = ?";
    }
    
    $sql .= " GROUP BY v.ID_Prod_POS, v.Cod_Barra, v.Nombre_Prod, c.Nom_Cat
    ORDER BY Total_Vendido DESC
    LIMIT ?";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    if (!empty($sucursal)) {
        $stmt->bind_param("sssi", $fecha_inicio, $fecha_fin, $sucursal, $limite);
    } else {
        $stmt->bind_param("ssi", $fecha_inicio, $fecha_fin, $limite);
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
    $ranking = 1;
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "Ranking" => $ranking,
            "ID_Prod_POS" => $row['ID_Prod_POS'],
            "Cod_Barra" => $row['Cod_Barra'] ?: '',
            "Nombre_Prod" => $row['Nombre_Prod'] ?: 'Sin nombre',
            "Categoria" => $row['Categoria'] ?: 'Sin categoría',
            "Total_Vendido" => number_format($row['Total_Vendido']),
            "Total_Importe" => '$' . number_format($row['Total_Importe'], 2),
            "Promedio_Venta" => '$' . number_format($row['Promedio_Venta'], 2),
            "Numero_Ventas" => number_format($row['Numero_Ventas']),
            "Ultima_Venta" => $row['Ultima_Venta'] ? date('d/m/Y', strtotime($row['Ultima_Venta'])) : ''
        ];
        $ranking++;
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
    error_log('Error en ArrayDeReporteProductosMasVendidos.php: ' . $e->getMessage());
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "error" => true,
        "message" => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 