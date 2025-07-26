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
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception('Error al obtener resultados: ' . $stmt->error);
    }
    
    // Configurar headers para descarga HTML
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte_Sucursales_' . date('Y-m-d_H-i-s') . '.xls"');
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
    echo '<div class="title">Reporte de Rendimiento por Sucursal</div>';
    echo '<div class="subtitle" style="text-align: center;">Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) . '</div>';
    echo '<div class="subtitle" style="text-align: center;">Generado: ' . date('d/m/Y H:i:s') . '</div>';
    
    // Tabla de datos
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Sucursal</th>';
    echo '<th>Total Ventas</th>';
    echo '<th>Total Importe</th>';
    echo '<th>Total Descuento</th>';
    echo '<th>Número Transacciones</th>';
    echo '<th>Promedio por Venta</th>';
    echo '<th>Productos Vendidos</th>';
    echo '<th>Clientes Atendidos</th>';
    echo '<th>Última Venta</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $total_importe = 0;
    $total_ventas = 0;
    $total_transacciones = 0;
    $total_productos = 0;
    $total_clientes = 0;
    $sucursal_mejor = '';
    $max_importe = 0;
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['Sucursal'] ?: 'Sucursal no encontrada') . '</td>';
        echo '<td class="currency">' . $row['Total_Ventas'] . '</td>';
        echo '<td class="currency">' . $row['Total_Importe'] . '</td>';
        echo '<td class="currency">' . $row['Total_Descuento'] . '</td>';
        echo '<td class="number">' . $row['Numero_Transacciones'] . '</td>';
        echo '<td class="currency">' . $row['Promedio_Venta'] . '</td>';
        echo '<td class="number">' . $row['Productos_Vendidos'] . '</td>';
        echo '<td class="number">' . $row['Clientes_Atendidos'] . '</td>';
        echo '<td class="date">' . ($row['Ultima_Venta'] ? date('d/m/Y', strtotime($row['Ultima_Venta'])) : '') . '</td>';
        echo '</tr>';
        
        $total_importe += $row['Total_Importe'];
        $total_ventas += $row['Total_Ventas'];
        $total_transacciones += $row['Numero_Transacciones'];
        $total_productos += $row['Productos_Vendidos'];
        $total_clientes += $row['Clientes_Atendidos'];
        
        // Encontrar la mejor sucursal
        if ($row['Total_Importe'] > $max_importe) {
            $max_importe = $row['Total_Importe'];
            $sucursal_mejor = $row['Sucursal'];
        }
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Resumen
    echo '<table style="width: 50%; margin-top: 20px;">';
    echo '<tr class="summary">';
    echo '<td colspan="2" style="text-align: center; font-size: 14px;">RESUMEN POR SUCURSALES</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Sucursales:</td>';
    echo '<td class="number">' . $result->num_rows . '</td>';
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
    echo '<td style="font-weight: bold;">Total Transacciones:</td>';
    echo '<td class="number">' . $total_transacciones . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Productos Vendidos:</td>';
    echo '<td class="number">' . $total_productos . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Clientes Atendidos:</td>';
    echo '<td class="number">' . $total_clientes . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Mejor Sucursal:</td>';
    echo '<td>' . htmlspecialchars($sucursal_mejor) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Promedio por Sucursal:</td>';
    echo '<td class="currency">' . ($result->num_rows > 0 ? $total_importe / $result->num_rows : 0) . '</td>';
    echo '</tr>';
    echo '</table>';
    
    echo '</body>';
    echo '</html>';
    
    // Cerrar conexión
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    // Si hay error, mostrar mensaje de error
    error_log('Error en exportar_reporte_sucursales.php: ' . $e->getMessage());
    
    // Enviar respuesta de error
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 