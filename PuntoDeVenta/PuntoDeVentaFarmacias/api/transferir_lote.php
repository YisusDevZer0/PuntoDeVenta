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
    
    // Actualizar cantidad del lote origen
    $nuevaCantidadOrigen = $loteActual['cantidad_actual'] - $cantidadTransferir;
    $sqlUpdateOrigen = "UPDATE productos_lotes_caducidad 
                       SET cantidad_actual = ? 
                       WHERE id_lote = ?";
    $stmtUpdateOrigen = $con->prepare($sqlUpdateOrigen);
    $stmtUpdateOrigen->bind_param("ii", $nuevaCantidadOrigen, $idLote);
    
    if (!$stmtUpdateOrigen->execute()) {
        throw new Exception('Error al actualizar el lote origen');
    }
    
    // Crear nuevo lote en sucursal destino
    $sqlInsertDestino = "INSERT INTO productos_lotes_caducidad 
                        (folio_stock, cod_barra, nombre_producto, lote, fecha_caducidad, 
                         fecha_ingreso, cantidad_inicial, cantidad_actual, sucursal_id, 
                         proveedor, precio_compra, precio_venta, estado, usuario_registro, observaciones)
                        VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, 'activo', ?, ?)";
    
    $stmtInsertDestino = $con->prepare($sqlInsertDestino);
    // Asegurar que los valores no sean null
    $folioStock = $loteActual['folio_stock'] ?? 0;
    $codBarra = $loteActual['cod_barra'] ?? '';
    $nombreProducto = $loteActual['nombre_producto'] ?? '';
    $loteTransfer = $loteActual['lote'] . '_TRANSFER';
    $fechaCaducidad = $loteActual['fecha_caducidad'] ?? date('Y-m-d');
    $proveedor = $loteActual['proveedor'] ?? null;
    $precioCompra = $loteActual['precio_compra'] ?? null;
    $precioVenta = $loteActual['precio_venta'] ?? 0;
    $observacionesTransfer = "Transferencia: $observaciones";
    
    $stmtInsertDestino->bind_param("issssiiisssis",
        $folioStock,
        $codBarra,
        $nombreProducto,
        $loteTransfer,
        $fechaCaducidad,
        $cantidadTransferir,
        $cantidadTransferir,
        $sucursalDestino,
        $proveedor,
        $precioCompra,
        $precioVenta,
        $usuarioMovimiento,
        $observacionesTransfer
    );
    
    if (!$stmtInsertDestino->execute()) {
        throw new Exception('Error al crear el lote destino: ' . $con->error);
    }
    
    $idLoteDestino = $con->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => "Transferencia realizada exitosamente. Se creó el lote destino con ID: $idLoteDestino"
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
