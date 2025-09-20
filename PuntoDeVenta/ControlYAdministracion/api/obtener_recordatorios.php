<?php
header('Content-Type: application/json');
include_once "../../Consultas/db_connect.php";

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener filtros
    $estado = $_POST['estado'] ?? 'activo';
    
    // Construir consulta
    $sql = "SELECT 
                id_recordatorio,
                titulo,
                descripcion,
                fecha_recordatorio,
                prioridad,
                estado,
                fecha_creacion
            FROM Recordatorios_Limpieza 
            WHERE estado = ?
            ORDER BY fecha_recordatorio ASC";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Error preparando consulta: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $estado);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $recordatorios = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $recordatorios[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    
    echo json_encode([
        'success' => true,
        'data' => $recordatorios
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>