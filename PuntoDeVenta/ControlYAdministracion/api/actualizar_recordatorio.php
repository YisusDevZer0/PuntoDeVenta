<?php
header('Content-Type: application/json');
include_once "../../Consultas/db_connect.php";

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener datos del POST
    $id_recordatorio = $_POST['id_recordatorio'] ?? null;
    $estado = $_POST['estado'] ?? '';
    
    if (!$id_recordatorio || !is_numeric($id_recordatorio)) {
        throw new Exception('ID de recordatorio inválido');
    }
    
    if (empty($estado)) {
        throw new Exception('Estado requerido');
    }
    
    // Validar estado
    $estados_validos = ['activo', 'completado', 'cancelado'];
    if (!in_array($estado, $estados_validos)) {
        throw new Exception('Estado no válido');
    }
    
    // Actualizar recordatorio
    $sql = "UPDATE Recordatorios_Limpieza 
            SET estado = ?, 
                fecha_actualizacion = NOW() 
            WHERE id_recordatorio = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Error preparando consulta: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "si", $estado, $id_recordatorio);
    
    if (mysqli_stmt_execute($stmt)) {
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        if ($affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Recordatorio actualizado exitosamente'
            ]);
        } else {
            throw new Exception('No se encontró el recordatorio');
        }
    } else {
        throw new Exception('Error al actualizar el recordatorio: ' . mysqli_stmt_error($stmt));
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>