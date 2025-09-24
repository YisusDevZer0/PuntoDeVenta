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
    $required_fields = ['sucursal_id', 'dias_alerta_3_meses', 'dias_alerta_6_meses', 'dias_alerta_9_meses'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Campo requerido: $field");
        }
    }
    
    // Verificar si ya existe configuración para esta sucursal
    $sql_verificar = "SELECT id_config FROM caducados_configuracion WHERE sucursal_id = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("i", $data['sucursal_id']);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();
    
    if ($result_verificar->num_rows > 0) {
        // Actualizar configuración existente
        $sql_update = "UPDATE caducados_configuracion 
                       SET dias_alerta_3_meses = ?, 
                           dias_alerta_6_meses = ?, 
                           dias_alerta_9_meses = ?, 
                           notificaciones_activas = ?, 
                           email_responsable = ?, 
                           telefono_whatsapp = ?
                       WHERE sucursal_id = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iiisssi", 
            $data['dias_alerta_3_meses'],
            $data['dias_alerta_6_meses'],
            $data['dias_alerta_9_meses'],
            $data['notificaciones_activas'],
            $data['email_responsable'],
            $data['telefono_whatsapp'],
            $data['sucursal_id']
        );
        
        if ($stmt_update->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Configuración actualizada exitosamente'
            ]);
        } else {
            throw new Exception('Error al actualizar la configuración: ' . $conn->error);
        }
        
    } else {
        // Crear nueva configuración
        $sql_insert = "INSERT INTO caducados_configuracion 
                       (sucursal_id, dias_alerta_3_meses, dias_alerta_6_meses, dias_alerta_9_meses, 
                        notificaciones_activas, email_responsable, telefono_whatsapp) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiiisss", 
            $data['sucursal_id'],
            $data['dias_alerta_3_meses'],
            $data['dias_alerta_6_meses'],
            $data['dias_alerta_9_meses'],
            $data['notificaciones_activas'],
            $data['email_responsable'],
            $data['telefono_whatsapp']
        );
        
        if ($stmt_insert->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Configuración creada exitosamente'
            ]);
        } else {
            throw new Exception('Error al crear la configuración: ' . $conn->error);
        }
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
