<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a la base de datos
include "../../db_connection_Huellas.php";
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
    // Consulta simplificada sin restricción de tiempo
    $query = "SELECT 
        id_notificacion,
        nombre_completo,
        domicilio,
        nombre_dia,
        hora_registro,
        tipo_evento,
        fecha_notificacion,
        estado_notificacion
    FROM notificaciones_asistencia
    ORDER BY fecha_notificacion DESC
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
        // Crear mensaje simple
        $mensaje = "{$row['nombre_completo']} - {$row['tipo_evento']}";
        
        // Determinar el tipo de notificación para el ícono
        $tipo = 'sistema';
        if ($row['estado_notificacion'] == 'pendiente') {
            $tipo = 'warning';
        } elseif ($row['estado_notificacion'] == 'atendida') {
            $tipo = 'success';
        }
        
        $registros[] = [
            'id' => $row['id_notificacion'],
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'nombre_completo' => $row['nombre_completo'],
            'hora_registro' => $row['hora_registro'],
            'estado' => $row['estado_notificacion']
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