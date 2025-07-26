<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db_connect.php");
include("ControladorUsuario.php");

try {
    // Obtener parámetros de filtro
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
    $sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
    
    // Consulta SQL con JOINs para obtener precios y nombres de sucursales
    $sql = "SELECT 
        v.ID_Prod_POS,
        v.Cod_Barra,
        v.Nombre_Prod,
        v.Tipo,
        v.Fk_sucursal,
        s.Nombre_Sucursal,
        p.Precio_Venta,
        p.Precio_C,
        p.Tipo_Servicio,
        p.Componente_Activo,
        st.Existencias_R,
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
    LEFT JOIN Stock_POS st ON v.ID_Prod_POS = st.ID_Prod_POS AND v.Fk_sucursal = st.Fk_sucursal
    LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
    WHERE v.Fecha_venta BETWEEN ? AND ?
    AND v.Estatus = 'Pagado'";
    
    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND v.Fk_sucursal = ?";
    }
    
    $sql .= " GROUP BY v.ID_Prod_POS, v.Cod_Barra, v.Nombre_Prod, v.Tipo, v.Fk_sucursal, s.Nombre_Sucursal, p.Precio_Venta, p.Precio_C, p.Tipo_Servicio, p.Componente_Activo, st.Existencias_R, v.AgregadoPor
    ORDER BY Total_Vendido DESC";
    
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
    
    // Configurar headers para descarga CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="Reporte_Ventas_Producto_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');
    
    // Crear el archivo CSV
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8 (Excel lo reconoce mejor)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Título del reporte
    fputcsv($output, array('Reporte de Ventas por Producto'));
    fputcsv($output, array('Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin))));
    fputcsv($output, array('Generado: ' . date('d/m/Y H:i:s')));
    fputcsv($output, array()); // Línea en blanco
    
    // Headers de la tabla
    $headers = array(
        'ID Producto', 'Código de Barras', 'Nombre del Producto', 'Tipo', 'Sucursal',
        'Precio Venta', 'Precio Compra', 'Existencias', 'Total Vendido', 'Total Importe',
        'Total Venta', 'Total Descuento', 'Número Ventas', 'Vendedor', 'Primera Venta', 'Última Venta'
    );
    fputcsv($output, $headers);
    
    // Datos de la tabla
    $total_importe = 0;
    $total_ventas = 0;
    $total_unidades = 0;
    
    while ($row = $result->fetch_assoc()) {
        $csv_row = array(
            $row['ID_Prod_POS'],
            $row['Cod_Barra'] ?: '',
            $row['Nombre_Prod'] ?: 'Sin nombre',
            $row['Tipo'] ?: '',
            $row['Nombre_Sucursal'] ?: 'Sucursal no encontrada',
            number_format($row['Precio_Venta'] ?: 0, 2),
            number_format($row['Precio_C'] ?: 0, 2),
            number_format($row['Existencias_R'] ?: 0),
            number_format($row['Total_Vendido']),
            number_format($row['Total_Importe'], 2),
            number_format($row['Total_Venta'], 2),
            number_format($row['Total_Descuento'], 2),
            number_format($row['Numero_Ventas']),
            $row['AgregadoPor'] ?: '',
            $row['Primera_Venta'] ? date('d/m/Y', strtotime($row['Primera_Venta'])) : '',
            $row['Ultima_Venta'] ? date('d/m/Y', strtotime($row['Ultima_Venta'])) : ''
        );
        fputcsv($output, $csv_row);
        
        $total_importe += $row['Total_Importe'];
        $total_ventas += $row['Total_Venta'];
        $total_unidades += $row['Total_Vendido'];
    }
    
    // Línea en blanco
    fputcsv($output, array());
    
    // Resumen
    fputcsv($output, array('RESUMEN'));
    fputcsv($output, array('Total Productos:', $result->num_rows));
    fputcsv($output, array('Total Unidades Vendidas:', number_format($total_unidades)));
    fputcsv($output, array('Total Importe:', number_format($total_importe, 2)));
    fputcsv($output, array('Total Ventas:', number_format($total_ventas, 2)));
    
    fclose($output);
    
    // Cerrar conexión
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    // Si hay error, mostrar mensaje de error
    error_log('Error en exportar_reporte_producto.php: ' . $e->getMessage());
    
    // Enviar respuesta de error
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 