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
    $pedidoId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nuevoEstado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
    
    if ($pedidoId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de pedido inválido']);
        exit();
    }
    
    if (!in_array($nuevoEstado, ['pendiente', 'aprobado', 'completado', 'cancelado'])) {
        echo json_encode(['success' => false, 'message' => 'Estado inválido']);
        exit();
    }
    
    // Verificar que el pedido existe y pertenece a la sucursal del usuario
    $sqlVerificar = "SELECT id, estado FROM pedidos 
                     WHERE id = ? AND sucursal_id = ? AND tipo_origen = 'admin'";
    
    $stmtVerificar = $conn->prepare($sqlVerificar);
    $sucursal_id = $row['Fk_Sucursal'];
    $stmtVerificar->bind_param("ii", $pedidoId, $sucursal_id);
    $stmtVerificar->execute();
    $resultVerificar = $stmtVerificar->get_result();
    $pedido = $resultVerificar->fetch_assoc();
    
    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido no encontrado o no tienes permisos']);
        exit();
    }
    
    $estadoAnterior = $pedido['estado'];
    
    // Actualizar estado del pedido usando tabla existente
    $sql = "UPDATE pedidos 
            SET estado = ?, 
                fecha_aprobacion = CASE WHEN ? = 'aprobado' THEN NOW() ELSE fecha_aprobacion END,
                fecha_completado = CASE WHEN ? = 'completado' THEN NOW() ELSE fecha_completado END,
                aprobado_por = CASE WHEN ? = 'aprobado' THEN ? ELSE aprobado_por END
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $usuario_id = $row['Id_PvUser'];
    $stmt->bind_param("ssssii", $nuevoEstado, $nuevoEstado, $nuevoEstado, $nuevoEstado, $usuario_id, $pedidoId);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al actualizar estado: " . $stmt->error);
    }
    
    // Registrar en historial
    $sqlHistorial = "INSERT INTO pedido_historial (
                        pedido_id,
                        usuario_id,
                        estado_anterior,
                        estado_nuevo,
                        comentario
                    ) VALUES (?, ?, ?, ?, ?)";
    
    $comentario = "Estado cambiado de '$estadoAnterior' a '$nuevoEstado'";
    $stmtHistorial = $conn->prepare($sqlHistorial);
    $stmtHistorial->bind_param("iisss", $pedidoId, $usuario_id, $estadoAnterior, $nuevoEstado, $comentario);
    $stmtHistorial->execute();
    
    // Si el estado es completado, actualizar inventario
    if ($nuevoEstado === 'completado') {
        actualizarInventario($pedidoId);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Estado del pedido actualizado exitosamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cambiar estado: ' . $e->getMessage()
    ]);
}

function actualizarInventario($pedidoId) {
    global $conn;
    
    // Obtener productos del pedido que no son encargos
    $sql = "SELECT pd.producto_id, pd.cantidad_solicitada
            FROM pedido_detalles pd
            WHERE pd.pedido_id = ? AND pd.producto_id IS NOT NULL AND pd.producto_id != 999999";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pedidoId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        if ($row['producto_id']) {
            // Actualizar inventario (reducir stock) usando Stock_POS
            $sqlUpdate = "UPDATE Stock_POS 
                         SET Existencias_R = Existencias_R - ? 
                         WHERE ID_Prod_POS = ?";
            
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ii", $row['cantidad_solicitada'], $row['producto_id']);
            $stmtUpdate->execute();
        }
    }
}
?> 