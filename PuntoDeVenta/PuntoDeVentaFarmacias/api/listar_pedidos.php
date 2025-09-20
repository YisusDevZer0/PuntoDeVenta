<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['VentasPos'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $sucursal_id = $row['Fk_Sucursal'];
    $filtro_estado = isset($_POST['estado']) ? $_POST['estado'] : '';
    $filtro_fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
    $filtro_fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
    $busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
    
    // Construir consulta base
    $sql = "SELECT 
                p.id,
                p.folio,
                p.estado,
                p.prioridad,
                p.observaciones,
                p.total_estimado,
                p.fecha_creacion,
                u.Nombre_Apellidos as usuario_nombre,
                s.Nombre_Sucursal as sucursal_nombre
            FROM pedidos p
            LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
            LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
            WHERE p.sucursal_id = ? 
            AND p.tipo_origen = 'farmacia'";
    
    $params = [$sucursal_id];
    $types = "i";
    
    // Aplicar filtros
    if (!empty($filtro_estado)) {
        $sql .= " AND p.estado = ?";
        $params[] = $filtro_estado;
        $types .= "s";
    }
    
    if (!empty($filtro_fecha_inicio)) {
        $sql .= " AND DATE(p.fecha_creacion) >= ?";
        $params[] = $filtro_fecha_inicio;
        $types .= "s";
    }
    
    if (!empty($filtro_fecha_fin)) {
        $sql .= " AND DATE(p.fecha_creacion) <= ?";
        $params[] = $filtro_fecha_fin;
        $types .= "s";
    }
    
    if (!empty($busqueda)) {
        $sql .= " AND (p.folio LIKE ? OR p.observaciones LIKE ? OR u.Nombre_Apellidos LIKE ?)";
        $busqueda_like = "%$busqueda%";
        $params[] = $busqueda_like;
        $params[] = $busqueda_like;
        $params[] = $busqueda_like;
        $types .= "sss";
    }
    
    $sql .= " ORDER BY p.fecha_creacion DESC LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pedidos = [];
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = [
            'id' => $row['id'],
            'folio' => $row['folio'],
            'estado' => $row['estado'],
            'prioridad' => $row['prioridad'],
            'observaciones' => $row['observaciones'],
            'total_estimado' => floatval($row['total_estimado']),
            'fecha_creacion' => $row['fecha_creacion'],
            'usuario_nombre' => $row['usuario_nombre'],
            'sucursal_nombre' => $row['sucursal_nombre']
        ];
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
?>
