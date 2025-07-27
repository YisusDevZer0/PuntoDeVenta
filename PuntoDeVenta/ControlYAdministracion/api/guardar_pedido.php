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
    $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
    $prioridad = isset($_POST['prioridad']) ? trim($_POST['prioridad']) : 'normal';
    
    if (empty($productos)) {
        echo json_encode(['success' => false, 'message' => 'No hay productos en el pedido']);
        exit();
    }
    
    // Calcular total del pedido
    $total = 0;
    foreach ($productos as $producto) {
        $total += $producto['precio'] * $producto['cantidad'];
    }
    
    // Generar folio único
    $fecha_actual = date('Ymd');
    $sql_count = "SELECT COUNT(*) as total FROM pedidos WHERE folio LIKE ?";
    $stmt = $conn->prepare($sql_count);
    $folio_pattern = "PED{$fecha_actual}%";
    $stmt->bind_param("s", $folio_pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['total'];
    $folio = "PED{$fecha_actual}" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    
    // Insertar pedido principal usando tabla existente
    $sql = "INSERT INTO pedidos (
                folio,
                sucursal_id,
                usuario_id,
                estado,
                prioridad,
                tipo_origen,
                observaciones,
                total_estimado
            ) VALUES (?, ?, ?, 'pendiente', ?, 'admin', ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $sucursal_id = $row['Fk_Sucursal'];
    $usuario_id = $row['Id_PvUser'];
    $stmt->bind_param("siissd", $folio, $sucursal_id, $usuario_id, $prioridad, $observaciones, $total);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al insertar pedido: " . $stmt->error);
    }
    
    $pedidoId = $conn->insert_id;
    
    // Insertar productos del pedido usando tabla existente
    foreach ($productos as $producto) {
        $sqlDetalle = "INSERT INTO pedido_detalles (
                        pedido_id,
                        producto_id,
                        cantidad_solicitada,
                        precio_unitario,
                        subtotal
                    ) VALUES (?, ?, ?, ?, ?)";
        
        $stmtDetalle = $conn->prepare($sqlDetalle);
        $subtotal = $producto['precio'] * $producto['cantidad'];
        
        // Si es un encargo, usar un ID especial
        if (isset($producto['es_encargo']) && $producto['es_encargo']) {
            // Para encargos, usar un ID especial que identifique que es un encargo
            $producto_id = 999999; // ID especial para encargos
        } else {
            $producto_id = $producto['id'];
        }
        
        $stmtDetalle->bind_param("iiddd", $pedidoId, $producto_id, $producto['cantidad'], 
                                $producto['precio'], $subtotal);
        
        if (!$stmtDetalle->execute()) {
            throw new Exception("Error al insertar detalle del pedido: " . $stmtDetalle->error);
        }
    }
    
    // Registrar en historial
    $sqlHistorial = "INSERT INTO pedido_historial (
                        pedido_id,
                        usuario_id,
                        estado_anterior,
                        estado_nuevo,
                        comentario
                    ) VALUES (?, ?, NULL, 'pendiente', 'Pedido administrativo creado')";
    
    $stmtHistorial = $conn->prepare($sqlHistorial);
    $stmtHistorial->bind_param("ii", $pedidoId, $usuario_id);
    $stmtHistorial->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Pedido guardado exitosamente',
        'pedido_id' => $pedidoId,
        'folio' => $folio
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar pedido: ' . $e->getMessage()
    ]);
}
?> 