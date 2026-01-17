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
    
    // Consulta SQL para pagos de servicios
    $sql = "SELECT 
        ps.id,
        ps.NumTicket,
        ps.nombre_paciente,
        ps.Servicio,
        ps.costo,
        ps.FormaDePago,
        ps.Fk_Sucursal,
        ps.Fk_Caja,
        ps.Empleado,
        s.Nombre_Sucursal,
        c.ID_Caja
    FROM PagosServicios ps
    LEFT JOIN Sucursales s ON ps.Fk_Sucursal = s.ID_Sucursal
    LEFT JOIN Cajas c ON ps.Fk_Caja = c.ID_Caja
    WHERE 1=1";
    
    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND ps.Fk_Sucursal = ?";
    }
    
    $sql .= " ORDER BY ps.id DESC";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    // Bind parameters
    if (!empty($sucursal)) {
        $stmt->bind_param("s", $sucursal);
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
    header('Content-Disposition: attachment; filename="Reporte_Pagos_Servicios_' . date('Y-m-d_H-i-s') . '.xls"');
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
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    // Título del reporte
    echo '<div class="title">Reporte de Pagos de Servicios</div>';
    echo '<div class="subtitle" style="text-align: center;">Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) . '</div>';
    echo '<div class="subtitle" style="text-align: center;">Generado: ' . date('d/m/Y H:i:s') . '</div>';
    
    // Tabla de datos
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Número de Ticket</th>';
    echo '<th>Cliente</th>';
    echo '<th>Servicio</th>';
    echo '<th>Costo</th>';
    echo '<th>Forma de Pago</th>';
    echo '<th>Sucursal</th>';
    echo '<th>Empleado</th>';
    echo '<th>Caja</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $total_costo = 0;
    $total_pagos = 0;
    $clientes_unicos = [];
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['NumTicket'] ?: '') . '</td>';
        echo '<td>' . htmlspecialchars($row['nombre_paciente'] ?: 'Sin nombre') . '</td>';
        echo '<td>' . htmlspecialchars($row['Servicio'] ?: 'Sin servicio') . '</td>';
        echo '<td class="currency">' . ($row['costo'] ?: 0) . '</td>';
        echo '<td>' . htmlspecialchars($row['FormaDePago'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['Nombre_Sucursal'] ?: 'Sucursal no encontrada') . '</td>';
        echo '<td>' . htmlspecialchars($row['Empleado'] ?: '') . '</td>';
        echo '<td class="number">' . ($row['ID_Caja'] ?: '') . '</td>';
        echo '</tr>';
        
        $total_costo += $row['costo'] ?: 0;
        $total_pagos++;
        
        // Agregar cliente único
        if ($row['nombre_paciente'] && $row['nombre_paciente'] !== 'Sin nombre') {
            if (!in_array($row['nombre_paciente'], $clientes_unicos)) {
                $clientes_unicos[] = $row['nombre_paciente'];
            }
        }
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Resumen
    $promedio_pago = $total_pagos > 0 ? $total_costo / $total_pagos : 0;
    
    echo '<table style="width: 50%; margin-top: 20px;">';
    echo '<tr class="summary">';
    echo '<td colspan="2" style="text-align: center; font-size: 14px;">RESUMEN</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total de Pagos:</td>';
    echo '<td class="number">' . $total_pagos . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Recaudado:</td>';
    echo '<td class="currency">' . number_format($total_costo, 2) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Clientes Únicos:</td>';
    echo '<td class="number">' . count($clientes_unicos) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Promedio por Pago:</td>';
    echo '<td class="currency">' . number_format($promedio_pago, 2) . '</td>';
    echo '</tr>';
    echo '</table>';
    
    echo '</body>';
    echo '</html>';
    
    // Cerrar conexión
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    // Si hay error, mostrar mensaje de error
    error_log('Error en exportar_reporte_pagos_servicios.php: ' . $e->getMessage());
    
    // Enviar respuesta de error
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?>
