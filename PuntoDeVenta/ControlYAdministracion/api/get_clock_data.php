<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a la base de datos
include "../Controladores/db_connection_Huellas.php";  // Ruta relativa desde api/ a Controladores/
session_start();

header('Content-Type: application/json');

// Verificar que la conexión existe
if (!isset($conn) || $conn === null) {
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

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    $registros = [];
    $total = 0;

    while ($row = $result->fetch_assoc()) {
        // Calcular tiempo transcurrido
        $fechaHoraRegistro = $row['fecha_notificacion'];
        $fechaHoraRegistroDT = new DateTime($fechaHoraRegistro);
        $ahora = new DateTime();
        $intervalo = $fechaHoraRegistroDT->diff($ahora);
        if ($intervalo->y > 0) {
            $tiempoTranscurrido = $intervalo->y . ' año(s)';
        } elseif ($intervalo->m > 0) {
            $tiempoTranscurrido = $intervalo->m . ' mes(es)';
        } elseif ($intervalo->d > 0) {
            $tiempoTranscurrido = $intervalo->d . ' día(s)';
        } elseif ($intervalo->h > 0) {
            $tiempoTranscurrido = $intervalo->h . ' hora(s)';
        } elseif ($intervalo->i > 0) {
            $tiempoTranscurrido = $intervalo->i . ' minuto(s)';
        } else {
            $tiempoTranscurrido = 'hace segundos';
        }
        // Crear mensaje simple
        $mensaje = "{$row['nombre_completo']} - {$row['tipo_evento']}";
        
        // Determinar el tipo de notificación para el ícono
        $tipo = 'sistema';
        if (strtolower($row['estado_notificacion']) == 'pendiente') {
            $tipo = 'warning';
        } elseif (strtolower($row['estado_notificacion']) == 'atendida') {
            $tipo = 'success';
        }
        
        $registros[] = [
            'id' => $row['id_notificacion'],
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'nombre_completo' => $row['nombre_completo'],
            'hora_registro' => $row['hora_registro'],
            'estado' => $row['estado_notificacion'],
            'tiempo_transcurrido' => $tiempoTranscurrido
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