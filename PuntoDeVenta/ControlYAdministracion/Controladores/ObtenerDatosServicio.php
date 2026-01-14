<?php
header('Content-Type: application/json');
include_once "db_connect.php";

// Verificar que se recibió el ID del servicio
if (!isset($_POST['servicio_id']) || empty($_POST['servicio_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de servicio no proporcionado']);
    exit;
}

$servicio_id = (int)$_POST['servicio_id'];

try {
    // Consulta para obtener los datos del servicio incluyendo costo y comisión
    $sql = "SELECT `Servicio_ID`, `Servicio`, `Costo`, `Comision`, `CostoVariable`, `Estado`
            FROM `ListadoServicios` 
            WHERE `Servicio_ID` = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error en la preparación de la consulta: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $servicio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'servicio' => [
                'Servicio_ID' => $row['Servicio_ID'],
                'Servicio' => $row['Servicio'],
                'Costo' => $row['Costo'] ?? 0.00,
                'Comision' => $row['Comision'] ?? 0.00,
                'CostoVariable' => $row['CostoVariable'] ?? 'N',
                'Estado' => $row['Estado']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Servicio no encontrado']);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
