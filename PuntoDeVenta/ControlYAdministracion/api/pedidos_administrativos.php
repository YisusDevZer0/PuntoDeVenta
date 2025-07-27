<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';
    $fechaInicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : '';
    $fechaFin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : '';
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $exportar = isset($_GET['exportar']) ? true : false;
    
    // Construir consulta base usando tabla existente
    $sql = "SELECT 
                p.id,
                p.folio,
                p.estado,
                p.prioridad,
                p.tipo_origen,
                p.observaciones,
                p.fecha_creacion,
                p.fecha_aprobacion,
                p.fecha_completado,
                p.total_estimado,
                s.ID_Sucursal,
                s.Nombre_Sucursal,
                u.Nombre_Apellidos as usuario_nombre,
                aprobador.Nombre_Apellidos as aprobador_nombre,
                COUNT(pd.id) as cantidad_productos
            FROM pedidos p
            LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
            LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
            LEFT JOIN Usuarios_PV aprobador ON p.aprobado_por = aprobador.Id_PvUser
            LEFT JOIN pedido_detalles pd ON p.id = pd.pedido_id
            WHERE p.tipo_origen = 'admin'";
    
    $params = [];
    $types = '';
    
    // Aplicar filtros
    if (!empty($estado)) {
        $sql .= " AND p.estado = ?";
        $params[] = $estado;
        $types .= 's';
    }
    
    if (!empty($fechaInicio)) {
        $sql .= " AND DATE(p.fecha_creacion) >= ?";
        $params[] = $fechaInicio;
        $types .= 's';
    }
    
    if (!empty($fechaFin)) {
        $sql .= " AND DATE(p.fecha_creacion) <= ?";
        $params[] = $fechaFin;
        $types .= 's';
    }
    
    if (!empty($busqueda)) {
        $sql .= " AND (u.Nombre_Apellidos LIKE ? OR p.observaciones LIKE ? OR p.folio LIKE ?)";
        $searchTerm = "%$busqueda%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }
    
    $sql .= " GROUP BY p.id ORDER BY p.fecha_creacion DESC";
    
    if (!$exportar) {
        $sql .= " LIMIT 100";
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pedidos = [];
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = [
            'id' => $row['id'],
            'folio' => $row['folio'],
            'fecha' => $row['fecha_creacion'],
            'solicitante' => $row['usuario_nombre'],
            'total' => $row['total_estimado'],
            'observaciones' => $row['observaciones'],
            'prioridad' => $row['prioridad'],
            'estado' => $row['estado'],
            'cantidad_productos' => $row['cantidad_productos'],
            'sucursal' => $row['Nombre_Sucursal']
        ];
    }
    
    // Si es para exportar, generar archivo CSV
    if ($exportar) {
        exportarPedidos($pedidos);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos,
        'total' => count($pedidos)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener pedidos: ' . $e->getMessage()
    ]);
}

function exportarPedidos($pedidos) {
    // Crear archivo CSV para descarga
    $filename = 'pedidos_administrativos_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Encabezados
    fputcsv($output, [
        'ID',
        'Folio',
        'Fecha',
        'Solicitante',
        'Total',
        'Estado',
        'Prioridad',
        'Productos',
        'Observaciones'
    ]);
    
    // Datos
    foreach ($pedidos as $pedido) {
        fputcsv($output, [
            $pedido['id'],
            $pedido['folio'],
            $pedido['fecha'],
            $pedido['solicitante'],
            $pedido['total'],
            $pedido['estado'],
            $pedido['prioridad'],
            $pedido['cantidad_productos'],
            $pedido['observaciones']
        ]);
    }
    
    fclose($output);
}
?> 