<?php
// Establecer zona horaria
date_default_timezone_set('America/Mexico_City');

// Definir rutas absolutas
define('ROOT_PATH', '/home/u858848268/domains/doctorpez.mx/public_html/PuntoDeVenta/ControlYAdministracion');
define('LOG_PATH', ROOT_PATH . '/cron/logs');

// Incluir archivo de conexión a la base de datos
require_once ROOT_PATH . '/Controladores/db_connect.php';

// Función para escribir en el log
function escribirLog($mensaje, $tipo = 'INFO') {
    $fecha = date('Y-m-d H:i:s');
    $logFile = LOG_PATH . '/notificaciones_' . date('Y-m-d') . '.log';
    
    // Crear directorio de logs si no existe
    if (!file_exists(LOG_PATH)) {
        if (!@mkdir(LOG_PATH, 0755, true)) {
            error_log("No se pudo crear el directorio de logs: " . LOG_PATH);
            return false;
        }
    }
    
    $logMessage = "[$fecha] [$tipo] $mensaje" . PHP_EOL;
    if (!@file_put_contents($logFile, $logMessage, FILE_APPEND)) {
        error_log("No se pudo escribir en el archivo de log: " . $logFile);
        return false;
    }
    return true;
}

// Función para enviar correo de error
function enviarCorreoError($error) {
    $to = "jesusemutul@gmail.com";
    $subject = "Error en generación de notificaciones - Hostinger";
    $message = "Se ha producido un error al generar las notificaciones automáticas:\n\n";
    $message .= "Error: " . $error . "\n";
    $message .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
    $message .= "Servidor: " . $_SERVER['SERVER_NAME'] . "\n";
    $headers = "From: sistema@doctorpez.mx\r\n";
    
    @mail($to, $subject, $message, $headers);
}

try {
    // Verificar conexión a la base de datos
    if (!isset($conn) || !$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Verificar permisos de escritura
    if (!is_writable(LOG_PATH)) {
        throw new Exception("El directorio de logs no tiene permisos de escritura: " . LOG_PATH);
    }

    // Ejecutar el procedimiento almacenado
    $sql = "CALL generar_notificaciones_automaticas()";
    if ($conn->query($sql)) {
        // Obtener estadísticas de las notificaciones generadas
        $sql_stats = "SELECT 
            COUNT(*) as total_notificaciones,
            SUM(CASE WHEN tipo = 'error' THEN 1 ELSE 0 END) as errores,
            SUM(CASE WHEN tipo = 'warning' THEN 1 ELSE 0 END) as advertencias,
            SUM(CASE WHEN tipo = 'info' THEN 1 ELSE 0 END) as informativas
            FROM notificaciones 
            WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        $result = $conn->query($sql_stats);
        if ($result && $row = $result->fetch_assoc()) {
            escribirLog("Notificaciones generadas exitosamente:");
            escribirLog("- Total: " . $row['total_notificaciones']);
            escribirLog("- Errores: " . $row['errores']);
            escribirLog("- Advertencias: " . $row['advertencias']);
            escribirLog("- Informativas: " . $row['informativas']);
        }
    } else {
        throw new Exception("Error al ejecutar el procedimiento almacenado: " . $conn->error);
    }

} catch (Exception $e) {
    $error = $e->getMessage();
    escribirLog($error, 'ERROR');
    enviarCorreoError($error);
    
    // Registrar el error en el log de PHP
    error_log("Error en generar_notificaciones.php: " . $error);
}

// Cerrar conexión
if (isset($conn) && $conn) {
    $conn->close();
}
?> 