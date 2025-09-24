<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../Consultas/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Datos no válidos');
    }
    
    // Validar datos requeridos
    $required_fields = ['id_lote', 'fecha_caducidad_nueva', 'usuario_movimiento'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Campo requerido: $field");
        }
    }
    
    // Obtener datos actuales del lote
    $sql_actual = "SELECT * FROM productos_lotes_caducidad WHERE id_lote = ?";
    $stmt_actual = $conn->prepare($sql_actual);
    $stmt_actual->bind_param("i", $data['id_lote']);
    $stmt_actual->execute();
    $lote_actual = $stmt_actual->get_result()->fetch_assoc();
    
    if (!$lote_actual) {
        throw new Exception('Lote no encontrado');
    }
    
    // Actualizar fecha de caducidad
    $sql_update = "UPDATE productos_lotes_caducidad 
                   SET fecha_caducidad = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                   WHERE id_lote = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $data['fecha_caducidad_nueva'], $data['id_lote']);
    
    if ($stmt_update->execute()) {
        // Registrar en historial
        $sql_historial = "INSERT INTO caducados_historial 
                         (id_lote, tipo_movimiento, fecha_caducidad_anterior, fecha_caducidad_nueva, 
                          sucursal_origen, usuario_movimiento, observaciones) 
                         VALUES (?, 'actualizacion', ?, ?, ?, ?, ?)";
        
        $stmt_historial = $conn->prepare($sql_historial);
        $observaciones = $data['observaciones'] ?? "Actualización de fecha de caducidad";
        $stmt_historial->bind_param("isssiis", 
            $data['id_lote'],
            $lote_actual['fecha_caducidad'],
            $data['fecha_caducidad_nueva'],
            $lote_actual['sucursal_id'],
            $data['usuario_movimiento'],
            $observaciones
        );
        $stmt_historial->execute();
        
        // Actualizar también en Stock_POS
        $sql_update_stock = "UPDATE Stock_POS 
                             SET Fecha_Caducidad = ? 
                             WHERE Folio_Prod_Stock = ?";
        $stmt_update_stock = $conn->prepare($sql_update_stock);
        $stmt_update_stock->bind_param("si", 
            $data['fecha_caducidad_nueva'], 
            $lote_actual['folio_stock']
        );
        $stmt_update_stock->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Fecha de caducidad actualizada exitosamente'
        ]);
        
    } else {
        throw new Exception('Error al actualizar la fecha de caducidad: ' . $conn->error);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
