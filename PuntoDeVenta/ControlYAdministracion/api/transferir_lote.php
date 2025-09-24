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
    $required_fields = ['id_lote', 'sucursal_destino', 'cantidad_transferir', 'usuario_movimiento'];
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
    
    if ($lote_actual['cantidad_actual'] < $data['cantidad_transferir']) {
        throw new Exception('Cantidad insuficiente para transferir');
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Actualizar cantidad en lote origen
        $nueva_cantidad_origen = $lote_actual['cantidad_actual'] - $data['cantidad_transferir'];
        $sql_update_origen = "UPDATE productos_lotes_caducidad 
                              SET cantidad_actual = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                              WHERE id_lote = ?";
        $stmt_update_origen = $conn->prepare($sql_update_origen);
        $stmt_update_origen->bind_param("ii", $nueva_cantidad_origen, $data['id_lote']);
        $stmt_update_origen->execute();
        
        // Verificar si existe el producto en la sucursal destino
        $sql_destino = "SELECT sp.Folio_Prod_Stock FROM Stock_POS sp 
                        WHERE sp.Cod_Barra = ? AND sp.Fk_sucursal = ?";
        $stmt_destino = $conn->prepare($sql_destino);
        $stmt_destino->bind_param("si", $lote_actual['cod_barra'], $data['sucursal_destino']);
        $stmt_destino->execute();
        $result_destino = $stmt_destino->get_result();
        
        if ($result_destino->num_rows === 0) {
            throw new Exception('Producto no existe en la sucursal destino');
        }
        
        $producto_destino = $result_destino->fetch_assoc();
        
        // Crear nuevo lote en sucursal destino
        $sql_insert_destino = "INSERT INTO productos_lotes_caducidad 
                               (folio_stock, cod_barra, nombre_producto, lote, fecha_caducidad, fecha_ingreso, 
                                cantidad_inicial, cantidad_actual, sucursal_id, proveedor, precio_compra, precio_venta, 
                                estado, usuario_registro, observaciones) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo', ?, ?)";
        
        $stmt_insert_destino = $conn->prepare($sql_insert_destino);
        $observaciones_destino = "Transferencia desde sucursal " . $lote_actual['sucursal_id'];
        $stmt_insert_destino->bind_param("isssssiiisssis", 
            $producto_destino['Folio_Prod_Stock'],
            $lote_actual['cod_barra'],
            $lote_actual['nombre_producto'],
            $lote_actual['lote'],
            $lote_actual['fecha_caducidad'],
            date('Y-m-d'),
            $data['cantidad_transferir'],
            $data['cantidad_transferir'],
            $data['sucursal_destino'],
            $lote_actual['proveedor'],
            $lote_actual['precio_compra'],
            $lote_actual['precio_venta'],
            $data['usuario_movimiento'],
            $observaciones_destino
        );
        $stmt_insert_destino->execute();
        $id_lote_destino = $conn->insert_id;
        
        // Registrar en historial - origen
        $sql_historial_origen = "INSERT INTO caducados_historial 
                                (id_lote, tipo_movimiento, cantidad_anterior, cantidad_nueva, 
                                 sucursal_origen, sucursal_destino, usuario_movimiento, observaciones) 
                                VALUES (?, 'transferencia', ?, ?, ?, ?, ?, ?)";
        
        $stmt_historial_origen = $conn->prepare($sql_historial_origen);
        $observaciones_historial = "Transferencia de " . $data['cantidad_transferir'] . " unidades a sucursal " . $data['sucursal_destino'];
        $stmt_historial_origen->bind_param("iiiiis", 
            $data['id_lote'],
            $lote_actual['cantidad_actual'],
            $nueva_cantidad_origen,
            $lote_actual['sucursal_id'],
            $data['sucursal_destino'],
            $data['usuario_movimiento'],
            $observaciones_historial
        );
        $stmt_historial_origen->execute();
        
        // Registrar en historial - destino
        $sql_historial_destino = "INSERT INTO caducados_historial 
                                  (id_lote, tipo_movimiento, cantidad_nueva, 
                                   sucursal_origen, sucursal_destino, usuario_movimiento, observaciones) 
                                  VALUES (?, 'transferencia', ?, ?, ?, ?, ?)";
        
        $stmt_historial_destino = $conn->prepare($sql_historial_destino);
        $observaciones_historial_destino = "Recibido de sucursal " . $lote_actual['sucursal_id'];
        $stmt_historial_destino->bind_param("iiiiis", 
            $id_lote_destino,
            $data['cantidad_transferir'],
            $lote_actual['sucursal_id'],
            $data['sucursal_destino'],
            $data['usuario_movimiento'],
            $observaciones_historial_destino
        );
        $stmt_historial_destino->execute();
        
        // Actualizar stock en Stock_POS - origen
        $sql_update_stock_origen = "UPDATE Stock_POS 
                                    SET Existencias_R = Existencias_R - ? 
                                    WHERE Folio_Prod_Stock = ?";
        $stmt_update_stock_origen = $conn->prepare($sql_update_stock_origen);
        $stmt_update_stock_origen->bind_param("ii", $data['cantidad_transferir'], $lote_actual['folio_stock']);
        $stmt_update_stock_origen->execute();
        
        // Actualizar stock en Stock_POS - destino
        $sql_update_stock_destino = "UPDATE Stock_POS 
                                     SET Existencias_R = Existencias_R + ?, 
                                         Lote = ?, Fecha_Caducidad = ? 
                                     WHERE Folio_Prod_Stock = ?";
        $stmt_update_stock_destino = $conn->prepare($sql_update_stock_destino);
        $stmt_update_stock_destino->bind_param("issi", 
            $data['cantidad_transferir'], 
            $lote_actual['lote'], 
            $lote_actual['fecha_caducidad'],
            $producto_destino['Folio_Prod_Stock']
        );
        $stmt_update_stock_destino->execute();
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Lote transferido exitosamente',
            'id_lote_destino' => $id_lote_destino
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
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
