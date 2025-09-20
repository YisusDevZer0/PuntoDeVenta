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
    $pedido_id = isset($_POST['pedido_id']) ? intval($_POST['pedido_id']) : 0;
    $sucursal_id = $row['Fk_Sucursal'];
    
    if ($pedido_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de pedido inválido']);
        exit();
    }
    
    // Obtener información del pedido
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
            WHERE p.id = ? AND p.sucursal_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $pedido_id, $sucursal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
        exit();
    }
    
    $pedido = $result->fetch_assoc();
    
    // Obtener detalles del pedido
    $sql_detalles = "SELECT 
                        pd.cantidad_solicitada,
                        pd.precio_unitario,
                        pd.subtotal,
                        pr.Nombre_Prod as nombre_producto
                    FROM pedido_detalles pd
                    LEFT JOIN Productos_POS pr ON pd.producto_id = pr.ID_Prod_POS
                    WHERE pd.pedido_id = ?";
    
    $stmt_detalles = $conn->prepare($sql_detalles);
    $stmt_detalles->bind_param("i", $pedido_id);
    $stmt_detalles->execute();
    $result_detalles = $stmt_detalles->get_result();
    
    $detalles = [];
    while ($row = $result_detalles->fetch_assoc()) {
        $detalles[] = [
            'nombre_producto' => $row['nombre_producto'],
            'cantidad_solicitada' => $row['cantidad_solicitada'],
            'precio_unitario' => $row['precio_unitario'],
            'subtotal' => $row['subtotal']
        ];
    }
    
    // Obtener historial del pedido
    $sql_historial = "SELECT 
                        ph.estado_anterior,
                        ph.estado_nuevo,
                        ph.comentario,
                        ph.fecha_cambio,
                        u.Nombre_Apellidos as usuario_nombre
                    FROM pedido_historial ph
                    LEFT JOIN Usuarios_PV u ON ph.usuario_id = u.Id_PvUser
                    WHERE ph.pedido_id = ?
                    ORDER BY ph.fecha_cambio DESC";
    
    $stmt_historial = $conn->prepare($sql_historial);
    $stmt_historial->bind_param("i", $pedido_id);
    $stmt_historial->execute();
    $result_historial = $stmt_historial->get_result();
    
    $historial = [];
    while ($row = $result_historial->fetch_assoc()) {
        $historial[] = [
            'estado_anterior' => $row['estado_anterior'],
            'estado_nuevo' => $row['estado_nuevo'],
            'comentario' => $row['comentario'],
            'fecha_cambio' => $row['fecha_cambio'],
            'usuario_nombre' => $row['usuario_nombre']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'pedido' => $pedido,
        'detalles' => $detalles,
        'historial' => $historial
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener detalle del pedido: ' . $e->getMessage()
    ]);
}
?>
