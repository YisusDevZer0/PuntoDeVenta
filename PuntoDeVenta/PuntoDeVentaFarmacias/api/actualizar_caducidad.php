<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $idLote = $input['id_lote'] ?? null;
    $fechaCaducidadNueva = $input['fecha_caducidad_nueva'] ?? null;
    $motivo = $input['motivo'] ?? null;
    $observaciones = $input['observaciones'] ?? '';
    $usuarioMovimiento = $input['usuario_movimiento'] ?? 1;
    
    // Validar datos requeridos
    if (!$idLote || !$fechaCaducidadNueva || !$motivo) {
        throw new Exception('Todos los campos son requeridos');
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
    
    // Actualizar fecha de caducidad
    $sqlUpdate = "UPDATE productos_lotes_caducidad 
                  SET fecha_caducidad = ? 
                  WHERE id_lote = ?";
    $stmtUpdate = $con->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $fechaCaducidadNueva, $idLote);
    
    if (!$stmtUpdate->execute()) {
        throw new Exception('Error al actualizar la fecha de caducidad');
    }
    
    // Registrar en historial (comentado temporalmente por problemas de estructura)
    /*
    $sqlHistorial = "INSERT INTO caducados_historial
                     (id_lote, tipo_movimiento, cantidad_anterior, cantidad_nueva,
                      fecha_caducidad_anterior, fecha_caducidad_nueva,
                      sucursal_origen, sucursal_destino, usuario_movimiento, fecha_movimiento, observaciones)
                     VALUES (?, 'actualizacion', ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    
    $stmtHistorial = $con->prepare($sqlHistorial);
    $stmtHistorial->bind_param("iisssiis",
        $idLote,
        $loteActual['cantidad_actual'],
        $loteActual['cantidad_actual'],
        $loteActual['fecha_caducidad'],
        $fechaCaducidadNueva,
        $loteActual['sucursal_id'],
        $loteActual['sucursal_id'],
        $usuarioMovimiento,
        "Motivo: $motivo. $observaciones"
    );
    $stmtHistorial->execute();
    */
    
    echo json_encode([
        'success' => true,
        'message' => 'Fecha de caducidad actualizada exitosamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
