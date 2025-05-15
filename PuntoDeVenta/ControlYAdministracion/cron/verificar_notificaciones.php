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
    $logFile = LOG_PATH . '/verificacion_' . date('Y-m-d') . '.log';
    
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
    $subject = "Error en verificación de notificaciones - Hostinger";
    $message = "Se ha producido un error al verificar las notificaciones:\n\n";
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

    // Obtener estadísticas generales
    $sql_stats = "SELECT 
        COUNT(*) as total_notificaciones,
        SUM(CASE WHEN leida = 0 THEN 1 ELSE 0 END) as no_leidas,
        SUM(CASE WHEN tipo = 'error' THEN 1 ELSE 0 END) as errores,
        SUM(CASE WHEN tipo = 'warning' THEN 1 ELSE 0 END) as advertencias,
        SUM(CASE WHEN tipo = 'info' THEN 1 ELSE 0 END) as informativas
        FROM notificaciones 
        WHERE fecha_creacion >= CURDATE()";
    
    $result = $conn->query($sql_stats);
    if ($result && $row = $result->fetch_assoc()) {
        escribirLog("=== Estadísticas de Notificaciones del Día ===");
        escribirLog("Total de notificaciones: " . $row['total_notificaciones']);
        escribirLog("Notificaciones no leídas: " . $row['no_leidas']);
        escribirLog("Errores: " . $row['errores']);
        escribirLog("Advertencias: " . $row['advertencias']);
        escribirLog("Informativas: " . $row['informativas']);
    }

    // Verificar cajas abiertas
    $sql_cajas = "SELECT 
        s.Nombre_Sucursal,
        COUNT(*) as cajas_abiertas,
        SUM(c.Valor_Total_Caja) as total_en_cajas
        FROM Cajas c
        INNER JOIN Sucursales s ON c.Sucursal = s.ID_Sucursal
        WHERE c.Estatus = 'Abierta'
        GROUP BY s.ID_Sucursal";
    
    $result_cajas = $conn->query($sql_cajas);
    if ($result_cajas && $result_cajas->num_rows > 0) {
        escribirLog("\n=== Estado de Cajas ===");
        while ($caja = $result_cajas->fetch_assoc()) {
            escribirLog("Sucursal: " . $caja['Nombre_Sucursal']);
            escribirLog("- Cajas abiertas: " . $caja['cajas_abiertas']);
            escribirLog("- Total en cajas: $" . number_format($caja['total_en_cajas'], 2));
        }
    }

    // Verificar cortes pendientes
    $sql_cortes = "SELECT 
        s.Nombre_Sucursal,
        COUNT(DISTINCT c.ID_Caja) as cortes_pendientes,
        MAX(c.Fecha_Apertura) as ultima_apertura
        FROM Cajas c
        INNER JOIN Sucursales s ON c.Sucursal = s.ID_Sucursal
        WHERE c.Estatus = 'Abierta'
        AND TIMESTAMPDIFF(HOUR, c.Fecha_Apertura, NOW()) >= 4
        GROUP BY s.ID_Sucursal";
    
    $result_cortes = $conn->query($sql_cortes);
    if ($result_cortes && $result_cortes->num_rows > 0) {
        escribirLog("\n=== Cortes Pendientes ===");
        while ($corte = $result_cortes->fetch_assoc()) {
            escribirLog("Sucursal: " . $corte['Nombre_Sucursal']);
            escribirLog("- Cortes pendientes: " . $corte['cortes_pendientes']);
            escribirLog("- Última apertura: " . $corte['ultima_apertura']);
        }
    }

    // Verificar notificaciones no leídas por usuario
    $sql_usuarios = "SELECT 
        u.Nombre_Apellidos,
        s.Nombre_Sucursal,
        COUNT(n.id) as notificaciones_pendientes
        FROM notificaciones n
        INNER JOIN Usuarios_PV u ON n.usuario_id = u.Id_PvUser
        INNER JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal
        WHERE n.leida = 0
        AND n.fecha_creacion >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        GROUP BY u.Id_PvUser, s.ID_Sucursal
        HAVING COUNT(n.id) > 0";
    
    $result_usuarios = $conn->query($sql_usuarios);
    if ($result_usuarios && $result_usuarios->num_rows > 0) {
        escribirLog("\n=== Notificaciones Pendientes por Usuario ===");
        while ($usuario = $result_usuarios->fetch_assoc()) {
            escribirLog("Usuario: " . $usuario['Nombre_Apellidos']);
            escribirLog("Sucursal: " . $usuario['Nombre_Sucursal']);
            escribirLog("Notificaciones pendientes: " . $usuario['notificaciones_pendientes']);
        }
    }

} catch (Exception $e) {
    $error = $e->getMessage();
    escribirLog($error, 'ERROR');
    enviarCorreoError($error);
    
    // Registrar el error en el log de PHP
    error_log("Error en verificar_notificaciones.php: " . $error);
}

// Cerrar conexión
if (isset($conn) && $conn) {
    $conn->close();
}
?> 