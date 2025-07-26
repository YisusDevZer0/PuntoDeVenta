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
    
    // Configurar headers para descarga HTML
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte_Ventas_Vendedor_' . date('Y-m-d_H-i-s') . '.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');
    
    // Crear el archivo HTML con colores del sistema
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; font-size: 12px; }';
    echo 'table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }';
    echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }';
    echo 'th { background-color: #ef7980; color: white; font-weight: bold; font-size: 12px; }';
    echo 'tr:nth-child(even) { background-color: #f8f9fa; }';
    echo 'tr:hover { background-color: #ffe6e7; }';
    echo '.title { font-size: 18px; font-weight: bold; color: #ef7980; margin-bottom: 10px; text-align: center; }';
    echo '.subtitle { font-size: 12px; color: #666; margin-bottom: 5px; }';
    echo '.summary { background-color: #ef7980; color: white; font-weight: bold; }';
    echo '.summary td { background-color: #ef7980; color: white; }';
    echo '.currency { mso-number-format:"$#,##0.00"; }';
    echo '.number { mso-number-format:"#,##0"; }';
    echo '.date { mso-number-format:"dd/mm/yyyy"; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    // Título del reporte
    echo '<div class="title">Reporte de Ventas por Vendedor</div>';
    echo '<div class="subtitle" style="text-align: center;">Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) . '</div>';
    echo '<div class="subtitle" style="text-align: center;">Generado: ' . date('d/m/Y H:i:s') . '</div>';
    
    // Tabla de datos
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Vendedor</th>';
    echo '<th>Sucursal</th>';
    echo '<th>Total Ventas</th>';
    echo '<th>Total Importe</th>';
    echo '<th>Total Descuento</th>';
    echo '<th>Número Transacciones</th>';
    echo '<th>Promedio por Venta</th>';
    echo '<th>Primera Venta</th>';
    echo '<th>Última Venta</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $total_importe = 0;
    $total_ventas = 0;
    $total_vendedores = 0;
    $total_transacciones = 0;
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['Vendedor'] ?: 'Sin vendedor') . '</td>';
        echo '<td>' . htmlspecialchars($row['Sucursal'] ?: 'Sucursal no encontrada') . '</td>';
        echo '<td class="currency">' . $row['Total_Ventas'] . '</td>';
        echo '<td class="currency">' . $row['Total_Importe'] . '</td>';
        echo '<td class="currency">' . $row['Total_Descuento'] . '</td>';
        echo '<td class="number">' . $row['Numero_Transacciones'] . '</td>';
        echo '<td class="currency">' . $row['Promedio_Venta'] . '</td>';
        echo '<td class="date">' . ($row['Primera_Venta'] ? date('d/m/Y', strtotime($row['Primera_Venta'])) : '') . '</td>';
        echo '<td class="date">' . ($row['Ultima_Venta'] ? date('d/m/Y', strtotime($row['Ultima_Venta'])) : '') . '</td>';
        echo '</tr>';
        
        $total_importe += $row['Total_Importe'];
        $total_ventas += $row['Total_Ventas'];
        $total_vendedores++;
        $total_transacciones += $row['Numero_Transacciones'];
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Resumen
    echo '<table style="width: 50%; margin-top: 20px;">';
    echo '<tr class="summary">';
    echo '<td colspan="2" style="text-align: center; font-size: 14px;">RESUMEN</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Vendedores:</td>';
    echo '<td class="number">' . $total_vendedores . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Transacciones:</td>';
    echo '<td class="number">' . $total_transacciones . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Ventas:</td>';
    echo '<td class="currency">' . $total_ventas . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Importe:</td>';
    echo '<td class="currency">' . $total_importe . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Promedio por Vendedor:</td>';
    echo '<td class="currency">' . ($total_vendedores > 0 ? $total_importe / $total_vendedores : 0) . '</td>';
    echo '</tr>';
    echo '</table>';
    
    echo '</body>';
    echo '</html>';
    
    // Cerrar conexión
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    // Si hay error, mostrar mensaje de error
    error_log('Error en exportar_reporte_ventas_vendedor.php: ' . $e->getMessage());
    
    // Enviar respuesta de error
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 