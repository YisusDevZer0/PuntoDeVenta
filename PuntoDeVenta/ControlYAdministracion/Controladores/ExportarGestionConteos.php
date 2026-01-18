<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db_connect.php");
include("ControladorUsuario.php");

// Si $conn no existe, crearlo
if (!isset($conn)) {
    $conn = new mysqli(getenv('DB_HOST') ?: 'localhost', 
                       getenv('DB_USER') ?: 'u858848268_devpezer0', 
                       getenv('DB_PASS') ?: 'F9+nIIOuCh8yI6wu4!08', 
                       getenv('DB_NAME') ?: 'u858848268_doctorpez');
    
    if ($conn->connect_error) {
        die('Error de conexión: ' . $conn->connect_error);
    }
    
    $conn->query("SET time_zone = '-6:00'");
}

try {
    // Obtener parámetros de filtros
    $sucursal_param = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
    $usuario_param = isset($_GET['usuario']) ? $_GET['usuario'] : '';
    $fecha_desde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : '';
    $fecha_hasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : '';
    
    // Validar y convertir sucursal
    $sucursal = 0;
    if (!empty($sucursal_param) && $sucursal_param !== '' && $sucursal_param !== '0') {
        $sucursal = (int)$sucursal_param;
    }
    
    // Validar usuario
    $usuario = '';
    if (!empty($usuario_param) && $usuario_param !== '') {
        $usuario = $usuario_param;
    }
    
    // Construir consulta (igual que DataGestionConteos.php)
    $sql = "SELECT 
        itp.Folio_Turno,
        itp.Cod_Barra,
        itp.Nombre_Producto,
        s.Nombre_Sucursal,
        itp.Usuario_Selecciono,
        itp.Existencias_Sistema,
        itp.Existencias_Fisicas,
        itp.Diferencia,
        it.Fecha_Turno,
        itp.Fecha_Conteo,
        it.Estado as Estado_Turno,
        itp.Estado as Estado_Producto
    FROM Inventario_Turnos_Productos itp
    INNER JOIN Inventario_Turnos it ON itp.ID_Turno = it.ID_Turno
    INNER JOIN Sucursales s ON itp.Fk_sucursal = s.ID_Sucursal
    WHERE (itp.Estado = 'completado' OR itp.Estado = 'liberado')
      AND itp.Existencias_Fisicas IS NOT NULL";
    
    $params = [];
    $types = "";
    
    // Aplicar filtros
    if ($sucursal > 0) {
        $sql .= " AND itp.Fk_sucursal = ?";
        $params[] = $sucursal;
        $types .= "i";
    }
    
    if (!empty($usuario)) {
        $sql .= " AND TRIM(itp.Usuario_Selecciono) = TRIM(?)";
        $params[] = $usuario;
        $types .= "s";
    }
    
    if (!empty($fecha_desde)) {
        $sql .= " AND DATE(it.Fecha_Turno) >= ?";
        $params[] = $fecha_desde;
        $types .= "s";
    }
    
    if (!empty($fecha_hasta)) {
        $sql .= " AND DATE(it.Fecha_Turno) <= ?";
        $params[] = $fecha_hasta;
        $types .= "s";
    }
    
    $sql .= " ORDER BY it.Fecha_Turno DESC, itp.Fecha_Conteo DESC";
    
    // Ejecutar consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    // Configurar headers para descarga Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte_Conteos_Inventario_' . date('Y-m-d_H-i-s') . '.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');
    
    // Crear el archivo HTML con estilos
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; font-size: 12px; }';
    echo 'table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }';
    echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
    echo 'th { background-color: #0172b6; color: white; font-weight: bold; font-size: 12px; text-align: center; }';
    echo 'tr:nth-child(even) { background-color: #f8f9fa; }';
    echo '.title { font-size: 18px; font-weight: bold; color: #0172b6; margin-bottom: 10px; text-align: center; }';
    echo '.subtitle { font-size: 12px; color: #666; margin-bottom: 5px; }';
    echo '.summary { background-color: #0172b6; color: white; font-weight: bold; }';
    echo '.number { mso-number-format:"#,##0"; }';
    echo '.con-diferencia { background-color: #fff3cd; }';
    echo '.sin-diferencia { background-color: #d1ecf1; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    // Título del reporte
    echo '<div class="title">Reporte de Conteos de Inventario</div>';
    
    // Información de filtros
    $filtros_texto = [];
    if ($sucursal > 0) {
        $sql_suc = "SELECT Nombre_Sucursal FROM Sucursales WHERE ID_Sucursal = ? LIMIT 1";
        $stmt_suc = $conn->prepare($sql_suc);
        $stmt_suc->bind_param("i", $sucursal);
        $stmt_suc->execute();
        $suc_data = $stmt_suc->get_result()->fetch_assoc();
        $stmt_suc->close();
        if ($suc_data) {
            $filtros_texto[] = 'Sucursal: ' . $suc_data['Nombre_Sucursal'];
        }
    } else {
        $filtros_texto[] = 'Sucursal: Todas';
    }
    
    if (!empty($usuario)) {
        $filtros_texto[] = 'Usuario: ' . htmlspecialchars($usuario);
    } else {
        $filtros_texto[] = 'Usuario: Todos';
    }
    
    if (!empty($fecha_desde) && !empty($fecha_hasta)) {
        $filtros_texto[] = 'Período: ' . date('d/m/Y', strtotime($fecha_desde)) . ' - ' . date('d/m/Y', strtotime($fecha_hasta));
    } else {
        $filtros_texto[] = 'Período: Todos';
    }
    
    echo '<div class="subtitle" style="text-align: center;">' . implode(' | ', $filtros_texto) . '</div>';
    echo '<div class="subtitle" style="text-align: center;">Generado: ' . date('d/m/Y H:i:s') . '</div>';
    
    // Tabla de datos
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Folio Turno</th>';
    echo '<th>Código</th>';
    echo '<th>Producto</th>';
    echo '<th>Sucursal</th>';
    echo '<th>Usuario</th>';
    echo '<th>Exist. Sistema</th>';
    echo '<th>Exist. Físicas</th>';
    echo '<th>Diferencia</th>';
    echo '<th>Fecha Turno</th>';
    echo '<th>Fecha Conteo</th>';
    echo '<th>Estado Turno</th>';
    echo '<th>Estado Producto</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $total_productos = 0;
    $sin_diferencias = 0;
    $con_diferencias = 0;
    $suma_diferencias = 0;
    
    while ($row = $result->fetch_assoc()) {
        $diferencia_valor = (int)$row['Diferencia'];
        $tiene_diferencia = abs($diferencia_valor) > 0;
        
        $clase_fila = $tiene_diferencia ? 'con-diferencia' : 'sin-diferencia';
        
        if ($tiene_diferencia) {
            $con_diferencias++;
        } else {
            $sin_diferencias++;
        }
        
        $total_productos++;
        $suma_diferencias += $diferencia_valor;
        
        $signo_diferencia = $diferencia_valor > 0 ? '+' : '';
        
        echo '<tr class="' . $clase_fila . '">';
        echo '<td>' . htmlspecialchars($row['Folio_Turno'] ?: '') . '</td>';
        echo '<td>' . htmlspecialchars($row['Cod_Barra'] ?: '') . '</td>';
        echo '<td>' . htmlspecialchars($row['Nombre_Producto'] ?: '') . '</td>';
        echo '<td>' . htmlspecialchars($row['Nombre_Sucursal'] ?: '') . '</td>';
        echo '<td>' . htmlspecialchars($row['Usuario_Selecciono'] ?: '') . '</td>';
        echo '<td class="number">' . number_format($row['Existencias_Sistema'] ?: 0) . '</td>';
        echo '<td class="number">' . number_format($row['Existencias_Fisicas'] ?: 0) . '</td>';
        echo '<td class="number">' . $signo_diferencia . number_format($diferencia_valor) . '</td>';
        echo '<td>' . ($row['Fecha_Turno'] ? date('d/m/Y', strtotime($row['Fecha_Turno'])) : '-') . '</td>';
        echo '<td>' . ($row['Fecha_Conteo'] ? date('d/m/Y H:i', strtotime($row['Fecha_Conteo'])) : '-') . '</td>';
        echo '<td>' . ucfirst($row['Estado_Turno'] ?: '') . '</td>';
        echo '<td>' . ucfirst($row['Estado_Producto'] ?: '') . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Resumen
    echo '<table style="width: 50%; margin-top: 20px;">';
    echo '<tr class="summary">';
    echo '<td colspan="2" style="text-align: center; font-size: 14px;">RESUMEN</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Productos Contados:</td>';
    echo '<td class="number">' . $total_productos . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Sin Diferencias:</td>';
    echo '<td class="number">' . $sin_diferencias . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Con Diferencias:</td>';
    echo '<td class="number">' . $con_diferencias . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Suma Total de Diferencias:</td>';
    $signo_suma = $suma_diferencias >= 0 ? '+' : '';
    echo '<td class="number">' . $signo_suma . number_format($suma_diferencias) . '</td>';
    echo '</tr>';
    echo '</table>';
    
    echo '</body>';
    echo '</html>';
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo '<html><body>';
    echo '<h3>Error al generar el reporte</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</body></html>';
}
?>
