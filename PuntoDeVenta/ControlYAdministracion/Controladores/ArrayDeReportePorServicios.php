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
    
    // Consulta SQL para servicios con JOINs
    $sql = "SELECT 
        v.ID_Prod_POS,
        v.Cod_Barra,
        v.Nombre_Prod,
        s.Nom_Serv AS Tipo_Servicio,
        v.Fk_sucursal,
        suc.Nombre_Sucursal,
        p.Precio_Venta,
        p.Precio_C,
        SUM(v.Cantidad_Venta) AS Total_Vendido,
        SUM(v.Importe) AS Total_Importe,
        SUM(v.Total_Venta) AS Total_Venta,
        SUM(v.DescuentoAplicado) AS Total_Descuento,
        COUNT(*) AS Numero_Ventas,
        v.AgregadoPor,
        MIN(v.Fecha_venta) AS Primera_Venta,
        MAX(v.Fecha_venta) AS Ultima_Venta
    FROM Ventas_POS v
    LEFT JOIN Productos_POS p ON v.ID_Prod_POS = p.ID_Prod_POS
    LEFT JOIN Servicios_POS s ON p.Tipo_Servicio = s.Servicio_ID
    LEFT JOIN Sucursales suc ON v.Fk_sucursal = suc.ID_Sucursal
    WHERE v.Fecha_venta BETWEEN ? AND ?
    AND v.Estatus = 'Pagado'
    AND p.Tipo_Servicio IS NOT NULL";
    
    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND v.Fk_sucursal = ?";
    }
    
    $sql .= " GROUP BY v.ID_Prod_POS, v.Cod_Barra, v.Nombre_Prod, s.Nom_Serv, v.Fk_sucursal, suc.Nombre_Sucursal, p.Precio_Venta, p.Precio_C, v.AgregadoPor
    ORDER BY Total_Vendido DESC";
    
    // Preparar la consulta
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $con->error);
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
            "ID_Prod_POS" => $row['ID_Prod_POS'],
            "Cod_Barra" => $row['Cod_Barra'] ?: '',
            "Nombre_Prod" => $row['Nombre_Prod'] ?: 'Sin nombre',
            "Tipo_Servicio" => $row['Tipo_Servicio'] ?: 'Sin servicio',
            "Nombre_Sucursal" => $row['Nombre_Sucursal'] ?: 'Sucursal no encontrada',
            "Precio_Venta" => '$' . number_format($row['Precio_Venta'] ?: 0, 2),
            "Precio_C" => '$' . number_format($row['Precio_C'] ?: 0, 2),
            "Total_Vendido" => number_format($row['Total_Vendido']),
            "Total_Importe" => '$' . number_format($row['Total_Importe'], 2),
            "Total_Venta" => '$' . number_format($row['Total_Venta'], 2),
            "Total_Descuento" => '$' . number_format($row['Total_Descuento'], 2),
            "Numero_Ventas" => number_format($row['Numero_Ventas']),
            "AgregadoPor" => $row['AgregadoPor'] ?: '',
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
    $con->close();
    
} catch (Exception $e) {
    error_log('Error en ArrayDeReportePorServicios.php: ' . $e->getMessage());
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "error" => true,
        "message" => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 