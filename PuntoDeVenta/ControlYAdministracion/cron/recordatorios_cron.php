<?php
/**
 * Cron Job para Procesar Recordatorios - Doctor Pez
 * Este archivo debe ejecutarse cada minuto para procesar recordatorios pendientes
 * 
 * Configuración del cron:
 * * * * * * /usr/bin/php /ruta/al/proyecto/ControlYAdministracion/cron/recordatorios_cron.php
 */

// Configurar zona horaria
date_default_timezone_set('America/Monterrey');

// Incluir dependencias
include_once "../Consultas/db_connect.php";
include_once "../Controladores/RecordatoriosSistemaController.php";

// Log de inicio
$log_file = __DIR__ . '/recordatorios_cron.log';
$log_message = "[" . date('Y-m-d H:i:s') . "] Iniciando procesamiento de recordatorios\n";
file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);

try {
    // Verificar conexión a la base de datos
    if (!$con) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    // Inicializar controlador (sin usuario específico para cron)
    $recordatoriosController = new RecordatoriosSistemaController($con);
    
    // Obtener recordatorios pendientes
    $recordatoriosPendientes = $recordatoriosController->obtenerRecordatoriosPendientes();
    
    if (!$recordatoriosPendientes['success']) {
        throw new Exception("Error al obtener recordatorios pendientes: " . $recordatoriosPendientes['message']);
    }
    
    $total_pendientes = count($recordatoriosPendientes['recordatorios']);
    $log_message = "[" . date('Y-m-d H:i:s') . "] Encontrados $total_pendientes recordatorios pendientes\n";
    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
    
    if ($total_pendientes === 0) {
        $log_message = "[" . date('Y-m-d H:i:s') . "] No hay recordatorios pendientes. Finalizando.\n";
        file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
        exit(0);
    }
    
    // Procesar cada recordatorio pendiente
    $procesados = 0;
    $exitosos = 0;
    $errores = 0;
    
    foreach ($recordatoriosPendientes['recordatorios'] as $recordatorio) {
        $procesados++;
        
        try {
            $log_message = "[" . date('Y-m-d H:i:s') . "] Procesando recordatorio ID: {$recordatorio['id_recordatorio']} - {$recordatorio['titulo']}\n";
            file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
            
            // Enviar recordatorio
            $resultado = $recordatoriosController->enviarRecordatorio($recordatorio['id_recordatorio']);
            
            if ($resultado['success']) {
                $exitosos++;
                $log_message = "[" . date('Y-m-d H:i:s') . "] Recordatorio ID {$recordatorio['id_recordatorio']} enviado exitosamente: {$resultado['message']}\n";
            } else {
                $errores++;
                $log_message = "[" . date('Y-m-d H:i:s') . "] Error en recordatorio ID {$recordatorio['id_recordatorio']}: {$resultado['message']}\n";
            }
            
            file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
            
        } catch (Exception $e) {
            $errores++;
            $log_message = "[" . date('Y-m-d H:i:s') . "] Excepción en recordatorio ID {$recordatorio['id_recordatorio']}: " . $e->getMessage() . "\n";
            file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
        }
        
        // Pequeña pausa entre recordatorios para no sobrecargar el sistema
        usleep(100000); // 0.1 segundos
    }
    
    // Resumen final
    $log_message = "[" . date('Y-m-d H:i:s') . "] Procesamiento completado. Total: $procesados, Exitosos: $exitosos, Errores: $errores\n";
    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
    
    // Limpiar logs antiguos (más de 7 días)
    $this->limpiarLogsAntiguos();
    
} catch (Exception $e) {
    $log_message = "[" . date('Y-m-d H:i:s') . "] ERROR CRÍTICO: " . $e->getMessage() . "\n";
    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
    exit(1);
}

/**
 * Limpiar logs antiguos
 */
function limpiarLogsAntiguos() {
    global $con;
    
    try {
        // Limpiar logs de recordatorios (más de 30 días)
        $sql = "DELETE FROM recordatorios_logs WHERE fecha_log < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $con->query($sql);
        
        // Limpiar notificaciones antiguas (más de 30 días)
        $sql = "DELETE FROM Notificaciones WHERE Fecha < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $con->query($sql);
        
        // Limpiar archivo de log local (más de 7 días)
        $log_file = __DIR__ . '/recordatorios_cron.log';
        if (file_exists($log_file)) {
            $file_time = filemtime($log_file);
            if ($file_time < strtotime('-7 days')) {
                unlink($log_file);
            }
        }
        
    } catch (Exception $e) {
        // No fallar por errores de limpieza
        error_log("Error al limpiar logs: " . $e->getMessage());
    }
}

exit(0);
?>
