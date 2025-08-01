<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db_connect.php");
include("ControladorUsuario.php");

try {
    // Obtener parámetros de filtro
    $tipo_usuario = isset($_GET['tipo_usuario']) ? $_GET['tipo_usuario'] : '';
    $sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
    $estatus = isset($_GET['estatus']) ? $_GET['estatus'] : '';
    
    // Obtener el valor de la licencia
    $licencia = isset($row['Licencia']) ? $row['Licencia'] : '';
    
    // Consulta base
    $sql = "SELECT Usuarios_PV.Id_PvUser, Usuarios_PV.Nombre_Apellidos, Usuarios_PV.file_name, 
    Usuarios_PV.Fk_Usuario, Usuarios_PV.Fecha_Nacimiento, Usuarios_PV.Correo_Electronico, 
    Usuarios_PV.Telefono, Usuarios_PV.AgregadoPor, Usuarios_PV.AgregadoEl, Usuarios_PV.Estatus,
    Usuarios_PV.Licencia, Tipos_Usuarios.ID_User, Tipos_Usuarios.TipoUsuario,
    Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
    FROM Usuarios_PV 
    INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User 
    INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal
    WHERE Usuarios_PV.Licencia = ?";
    
    // Agregar filtros si están presentes
    $params = [$licencia];
    $types = "s";
    
    if (!empty($tipo_usuario)) {
        $sql .= " AND Tipos_Usuarios.TipoUsuario = ?";
        $params[] = $tipo_usuario;
        $types .= "s";
    }
    
    if (!empty($sucursal)) {
        $sql .= " AND Sucursales.ID_Sucursal = ?";
        $params[] = $sucursal;
        $types .= "s";
    }
    
    if (!empty($estatus)) {
        $sql .= " AND Usuarios_PV.Estatus = ?";
        $params[] = $estatus;
        $types .= "s";
    } else {
        // Por defecto mostrar solo activos si no se especifica estatus
        $sql .= " AND Usuarios_PV.Estatus = 'Activo'";
    }
    
    $sql .= " ORDER BY Usuarios_PV.Id_PvUser DESC";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    // Vincular parámetros
    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception('Error al obtener resultados: ' . $stmt->error);
    }
    
    // Configurar headers para descarga HTML
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte_Personal_' . date('Y-m-d_H-i-s') . '.xls"');
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
    echo '.date { mso-number-format:"dd/mm/yyyy"; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    // Título del reporte
    echo '<div class="title">Reporte de Personal</div>';
    echo '<div class="subtitle" style="text-align: center;">Generado: ' . date('d/m/Y H:i:s') . '</div>';
    
    // Mostrar filtros aplicados
    $filtros = [];
    if (!empty($tipo_usuario)) $filtros[] = "Tipo de Usuario: " . $tipo_usuario;
    if (!empty($sucursal)) {
        // Obtener nombre de sucursal
        $sql_suc = "SELECT Nombre_Sucursal FROM Sucursales WHERE ID_Sucursal = ?";
        $stmt_suc = $conn->prepare($sql_suc);
        $stmt_suc->bind_param("s", $sucursal);
        $stmt_suc->execute();
        $result_suc = $stmt_suc->get_result();
        if ($row_suc = $result_suc->fetch_assoc()) {
            $filtros[] = "Sucursal: " . $row_suc['Nombre_Sucursal'];
        }
    }
    if (!empty($estatus)) $filtros[] = "Estatus: " . $estatus;
    
    if (!empty($filtros)) {
        echo '<div class="subtitle" style="text-align: center;">Filtros aplicados: ' . implode(', ', $filtros) . '</div>';
    }
    
    // Tabla de datos
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID Empleado</th>';
    echo '<th>Nombre Completo</th>';
    echo '<th>Tipo de Usuario</th>';
    echo '<th>Sucursal</th>';
    echo '<th>Correo Electrónico</th>';
    echo '<th>Teléfono</th>';
    echo '<th>Fecha de Nacimiento</th>';
    echo '<th>Fecha de Creación</th>';
    echo '<th>Estatus</th>';
    echo '<th>Creado Por</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $total_personal = 0;
    $personal_activo = 0;
    $sucursales_unicas = [];
    $tipos_usuario_unicos = [];
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['Id_PvUser'] . '</td>';
        echo '<td>' . htmlspecialchars($row['Nombre_Apellidos']) . '</td>';
        echo '<td>' . htmlspecialchars($row['TipoUsuario']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Nombre_Sucursal']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Correo_Electronico'] ?: 'No especificado') . '</td>';
        echo '<td>' . htmlspecialchars($row['Telefono'] ?: 'No especificado') . '</td>';
        echo '<td class="date">' . ($row['Fecha_Nacimiento'] ? date('d/m/Y', strtotime($row['Fecha_Nacimiento'])) : 'No especificada') . '</td>';
        echo '<td class="date">' . ($row['AgregadoEl'] ? date('d/m/Y H:i', strtotime($row['AgregadoEl'])) : '') . '</td>';
        echo '<td>' . htmlspecialchars($row['Estatus']) . '</td>';
        echo '<td>' . htmlspecialchars($row['AgregadoPor'] ?: 'Sistema') . '</td>';
        echo '</tr>';
        
        $total_personal++;
        if ($row['Estatus'] === 'Activo') {
            $personal_activo++;
        }
        if (!in_array($row['Nombre_Sucursal'], $sucursales_unicas)) {
            $sucursales_unicas[] = $row['Nombre_Sucursal'];
        }
        if (!in_array($row['TipoUsuario'], $tipos_usuario_unicos)) {
            $tipos_usuario_unicos[] = $row['TipoUsuario'];
        }
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Resumen
    echo '<table style="width: 50%; margin-top: 20px;">';
    echo '<tr class="summary">';
    echo '<td colspan="2" style="text-align: center; font-size: 14px;">RESUMEN</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Total Personal:</td>';
    echo '<td>' . $total_personal . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Personal Activo:</td>';
    echo '<td>' . $personal_activo . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Sucursales Activas:</td>';
    echo '<td>' . count($sucursales_unicas) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="font-weight: bold;">Tipos de Usuario:</td>';
    echo '<td>' . count($tipos_usuario_unicos) . '</td>';
    echo '</tr>';
    echo '</table>';
    
    echo '</body>';
    echo '</html>';
    
    // Cerrar conexión
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    // Si hay error, mostrar mensaje de error
    error_log('Error en exportar_reporte_personal.php: ' . $e->getMessage());
    
    // Enviar respuesta de error
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 