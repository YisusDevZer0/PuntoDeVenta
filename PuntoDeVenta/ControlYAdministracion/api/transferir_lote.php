<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $idLote = $input['id_lote'] ?? null;
    $sucursalDestino = $input['sucursal_destino'] ?? null;
    $cantidadTransferir = $input['cantidad_transferir'] ?? null;
    $motivo = $input['motivo'] ?? null;
    $observaciones = $input['observaciones'] ?? '';
    $usuarioMovimiento = $input['usuario_movimiento'] ?? 1;
    
    // Validar datos requeridos
    if (!$idLote || !$sucursalDestino || !$cantidadTransferir || !$motivo) {
        throw new Exception('Todos los campos son requeridos');
    }
    
    if ($cantidadTransferir <= 0) {
        throw new Exception('La cantidad debe ser mayor a 0');
    }
    
    // Obtener datos actuales del lote
    $sqlActual = "SELECT * FROM productos_lotes_caducidad WHERE id_lote = ?";
    $stmtActual = $con->prepare($sqlActual);
    $stmtActual->bind_param("i", $idLote);
    $stmtActual->execute();
    $resultActual = $stmtActual->get_result();
    
    if ($resultActual->num_rows === 0) {
        throw new Exception('Lote no encontrado');
    }
    
    $loteActual = $resultActual->fetch_assoc();
    
    // Verificar que hay suficiente cantidad
    if ($loteActual['cantidad_actual'] < $cantidadTransferir) {
        throw new Exception('No hay suficiente cantidad disponible para transferir');
    }
    
    // Verificar que no se transfiera a la misma sucursal
    if ($loteActual['sucursal_id'] == $sucursalDestino) {
        throw new Exception('No se puede transferir a la misma sucursal');
    }
    
    // Iniciar transacción
    $con->begin_transaction();
    
    try {
        // Actualizar cantidad del lote origen
        $nuevaCantidadOrigen = $loteActual['cantidad_actual'] - $cantidadTransferir;
        $sqlUpdateOrigen = "UPDATE productos_lotes_caducidad 
                           SET cantidad_actual = ? 
                           WHERE id_lote = ?";
        $stmtUpdateOrigen = $con->prepare($sqlUpdateOrigen);
        $stmtUpdateOrigen->bind_param("ii", $nuevaCantidadOrigen, $idLote);
        $stmtUpdateOrigen->execute();
        
        // Crear nuevo lote en sucursal destino
        $sqlInsertDestino = "INSERT INTO productos_lotes_caducidad 
                            (folio_stock, cod_barra, nombre_producto, lote, fecha_caducidad, 
                             fecha_ingreso, cantidad_inicial, cantidad_actual, sucursal_id, 
                             proveedor, precio_compra, precio_venta, estado, usuario_registro, observaciones)
                            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, 'activo', ?, ?)";
        
        $stmtInsertDestino = $con->prepare($sqlInsertDestino);
        $stmtInsertDestino->bind_param("issssiiisssis",
            $loteActual['folio_stock'],
            $loteActual['cod_barra'],
            $loteActual['nombre_producto'],
            $loteActual['lote'] . '_TRANSFER',
            $loteActual['fecha_caducidad'],
            $cantidadTransferir,
            $cantidadTransferir,
            $sucursalDestino,
            $loteActual['proveedor'],
            $loteActual['precio_compra'],
            $loteActual['precio_venta'],
            $usuarioMovimiento,
            "Transferencia: $observaciones"
        );
        
        $stmtInsertDestino->execute();
        $idLoteDestino = $con->insert_id;
        
        // Registrar en historial (comentado temporalmente por problemas de estructura)
        /*
        $sqlHistorialOrigen = "INSERT INTO caducados_historial 
                              (id_lote, tipo_movimiento, cantidad_anterior, cantidad_nueva, 
                               sucursal_origen, sucursal_destino, usuario_movimiento, fecha_movimiento, observaciones)
                              VALUES (?, 'transferencia', ?, ?, ?, ?, ?, NOW(), ?)";
        
        $stmtHistorialOrigen = $con->prepare($sqlHistorialOrigen);
        $stmtHistorialOrigen->bind_param("iiiiis",
            $idLote,
            $loteActual['cantidad_actual'],
            $nuevaCantidadOrigen,
            $loteActual['sucursal_id'],
            $sucursalDestino,
            $usuarioMovimiento,
            "Motivo: $motivo. $observaciones"
        );
        $stmtHistorialOrigen->execute();
        
        // Registrar en historial (destino)
        $sqlHistorialDestino = "INSERT INTO caducados_historial 
                               (id_lote, tipo_movimiento, cantidad_anterior, cantidad_nueva, 
                                sucursal_origen, sucursal_destino, usuario_movimiento, fecha_movimiento, observaciones)
                               VALUES (?, 'transferencia', 0, ?, ?, ?, ?, NOW(), ?)";
        
        $stmtHistorialDestino = $con->prepare($sqlHistorialDestino);
        $stmtHistorialDestino->bind_param("iiis",
            $idLoteDestino,
            $cantidadTransferir,
            $loteActual['sucursal_id'],
            $sucursalDestino,
            $usuarioMovimiento,
            "Transferencia recibida. Motivo: $motivo. $observaciones"
        );
        $stmtHistorialDestino->execute();
        */
        
        // Confirmar transacción
        $con->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "Transferencia realizada exitosamente. Se creó el lote destino con ID: $idLoteDestino"
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $con->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>