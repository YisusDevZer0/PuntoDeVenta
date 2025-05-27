<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a la base de datos
include "../db_connection_Huellas";
session_start();

header('Content-Type: application/json');

// Verificar que la conexión existe
if (!isset($con) || $con === null) {
    echo json_encode([
        'error' => true,
        'message' => 'No se pudo establecer conexión con la base de datos',
        'registros' => [],
        'total' => 0
    ]);
    exit;
}

// ID de sucursal del usuario actual
$sucursalID = isset($_SESSION["ID_Sucursal"]) ? $_SESSION["ID_Sucursal"] : 1;

try {
    // Consulta para obtener las notificaciones de asistencia más recientes
    $query = "SELECT 
        na.id_notificacion as id,
        na.id_personal,
        na.nombre_completo,
        na.nombre_dia,
        na.hora_registro,
        na.tipo_evento,
        na.fecha_notificacion,
        na.estado_notificacion,
        TIMESTAMPDIFF(MINUTE, na.fecha_notificacion, NOW()) as minutos_transcurridos
    FROM notificaciones_asistencia na
    WHERE na.fecha_notificacion >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ORDER BY na.fecha_notificacion DESC
    LIMIT 20";

    $stmt = $con->prepare($query);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $con->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    $registros = [];
    $total = 0;

    while ($row = $result->fetch_assoc()) {
        // Formatear tiempo transcurrido
        $tiempo = "";
        $minutos = $row['minutos_transcurridos'];
        
        if ($minutos < 60) {
            $tiempo = $minutos . " minutos";
        } else if ($minutos < 1440) {
            $tiempo = floor($minutos / 60) . " horas";
        } else {
            $tiempo = floor($minutos / 1440) . " días";
        }
        
        // Crear mensaje personalizado según el tipo de evento
        $mensaje = "";
        switch($row['tipo_evento']) {
            case 'entrada':
                $mensaje = "Entrada registrada para {$row['nombre_completo']} a las {$row['hora_registro']}";
                break;
            case 'salida':
                $mensaje = "Salida registrada para {$row['nombre_completo']} a las {$row['hora_registro']}";
                break;
            default:
                $mensaje = "{$row['tipo_evento']} registrado para {$row['nombre_completo']} a las {$row['hora_registro']}";
        }
        
        // Determinar el tipo de notificación para el ícono
        $tipo = 'sistema';
        if ($row['estado_notificacion'] == 'pendiente') {
            $tipo = 'warning';
        } elseif ($row['estado_notificacion'] == 'atendida') {
            $tipo = 'success';
        }
        
        $registros[] = [
            'id' => $row['id'],
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'tiempo_transcurrido' => $tiempo,
            'fecha' => $row['fecha_notificacion'],
            'estado' => $row['estado_notificacion'],
            'nombre_completo' => $row['nombre_completo'],
            'hora_registro' => $row['hora_registro']
        ];
        
        $total++;
    }

    echo json_encode([
        'error' => false,
        'registros' => $registros,
        'total' => $total
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'registros' => [],
        'total' => 0
    ]);
}

// Cerrar la conexión
if (isset($stmt)) {
    $stmt->close();
}
?> 