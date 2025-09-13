<?php
/**
 * Instalador del Sistema de Recordatorios - Doctor Pez
 * Configura e instala el sistema completo de recordatorios
 */

session_start();

// Verificar permisos de administrador
if (!isset($_SESSION["Id_PvUser"]) || $_SESSION["Nivel_Usuario"] !== "Administrador") {
    die("Acceso denegado. Se requieren permisos de administrador.");
}

include_once "Consultas/db_connect.php";

$mensajes = [];
$errores = [];

try {
    // 1. Crear tablas de base de datos
    $sql_file = __DIR__ . '/database/recordatorios_tables.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("Archivo SQL no encontrado: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    $statements = explode(';', $sql_content);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !str_starts_with($statement, '--')) {
            if (!$con->query($statement)) {
                throw new Exception("Error al ejecutar SQL: " . $con->error . "\nStatement: " . $statement);
            }
        }
    }
    
    $mensajes[] = "✅ Tablas de base de datos creadas exitosamente";
    
    // 2. Crear directorios necesarios
    $directorios = [
        'cron',
        'uploads/recordatorios',
        'logs'
    ];
    
    foreach ($directorios as $dir) {
        $ruta_completa = __DIR__ . '/' . $dir;
        if (!is_dir($ruta_completa)) {
            if (!mkdir($ruta_completa, 0755, true)) {
                throw new Exception("No se pudo crear el directorio: $dir");
            }
        }
        $mensajes[] = "✅ Directorio creado: $dir";
    }
    
    // 3. Configurar permisos de archivos
    $archivos_permisos = [
        'cron/recordatorios_cron.php' => 0755,
        'uploads/recordatorios' => 0755,
        'logs' => 0755
    ];
    
    foreach ($archivos_permisos as $archivo => $permisos) {
        $ruta_completa = __DIR__ . '/' . $archivo;
        if (file_exists($ruta_completa) || is_dir($ruta_completa)) {
            chmod($ruta_completa, $permisos);
        }
    }
    
    $mensajes[] = "✅ Permisos de archivos configurados";
    
    // 4. Insertar configuración inicial de WhatsApp
    $sql = "SELECT COUNT(*) as count FROM recordatorios_config_whatsapp";
    $result = $con->query($sql);
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        $sql = "INSERT INTO recordatorios_config_whatsapp 
                (api_url, api_token, numero_telefono, usuario_configurador) 
                VALUES ('https://api.whatsapp.com/send', 'CONFIGURAR_TOKEN', 'CONFIGURAR_NUMERO', ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $_SESSION["Id_PvUser"]);
        $stmt->execute();
        
        $mensajes[] = "✅ Configuración inicial de WhatsApp creada";
    }
    
    // 5. Crear grupos por defecto
    $sql = "SELECT COUNT(*) as count FROM recordatorios_grupos";
    $result = $con->query($sql);
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        $grupos_default = [
            ['Administradores', 'Grupo de administradores del sistema'],
            ['Supervisores', 'Grupo de supervisores'],
            ['Empleados', 'Grupo de empleados general'],
            ['Sucursal Principal', 'Empleados de la sucursal principal']
        ];
        
        foreach ($grupos_default as $grupo) {
            $sql = "INSERT INTO recordatorios_grupos (nombre_grupo, descripcion, usuario_creador) VALUES (?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssi", $grupo[0], $grupo[1], $_SESSION["Id_PvUser"]);
            $stmt->execute();
        }
        
        $mensajes[] = "✅ Grupos por defecto creados";
    }
    
    // 6. Verificar integridad de la instalación
    $tablas_requeridas = [
        'recordatorios_sistema',
        'recordatorios_destinatarios',
        'recordatorios_grupos',
        'recordatorios_grupos_miembros',
        'recordatorios_logs',
        'recordatorios_config_whatsapp',
        'recordatorios_plantillas'
    ];
    
    foreach ($tablas_requeridas as $tabla) {
        $sql = "SHOW TABLES LIKE '$tabla'";
        $result = $con->query($sql);
        if ($result->num_rows == 0) {
            throw new Exception("Tabla $tabla no fue creada correctamente");
        }
    }
    
    $mensajes[] = "✅ Verificación de integridad completada";
    
    // 7. Crear archivo de configuración del cron
    $cron_config = "<?php
// Configuración del Cron para Recordatorios
// Agregar esta línea al crontab del servidor:
// * * * * * /usr/bin/php " . __DIR__ . "/cron/recordatorios_cron.php

define('CRON_ENABLED', true);
define('LOG_FILE', '" . __DIR__ . "/logs/recordatorios_cron.log');
define('MAX_RECORDATORIOS_POR_EJECUCION', 10);
define('PAUSA_ENTRE_RECORDATORIOS', 100000); // microsegundos
?>";
    
    file_put_contents(__DIR__ . '/cron/cron_config.php', $cron_config);
    $mensajes[] = "✅ Archivo de configuración del cron creado";
    
    // 8. Crear script de prueba
    $test_script = "<?php
// Script de prueba para el sistema de recordatorios
include_once '../Consultas/db_connect.php';
include_once '../Controladores/RecordatoriosSistemaController.php';

echo \"Probando sistema de recordatorios...\n\";

try {
    \$controller = new RecordatoriosSistemaController(\$con, 1);
    
    // Crear recordatorio de prueba
    \$datos = [
        'titulo' => 'Recordatorio de Prueba',
        'descripcion' => 'Este es un recordatorio de prueba del sistema',
        'fecha_programada' => date('Y-m-d H:i:s', strtotime('+1 minute')),
        'prioridad' => 'media',
        'tipo_envio' => 'notificacion',
        'destinatarios' => 'todos'
    ];
    
    \$resultado = \$controller->crearRecordatorio(\$datos);
    
    if (\$resultado['success']) {
        echo \"✅ Recordatorio de prueba creado exitosamente\n\";
        echo \"ID: \" . \$resultado['recordatorio_id'] . \"\n\";
    } else {
        echo \"❌ Error al crear recordatorio: \" . \$resultado['message'] . \"\n\";
    }
    
} catch (Exception \$e) {
    echo \"❌ Error: \" . \$e->getMessage() . \"\n\";
}
?>";
    
    file_put_contents(__DIR__ . '/test_recordatorios.php', $test_script);
    $mensajes[] = "✅ Script de prueba creado";
    
    $instalacion_exitosa = true;
    
} catch (Exception $e) {
    $errores[] = "❌ Error: " . $e->getMessage();
    $instalacion_exitosa = false;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación del Sistema de Recordatorios</title>
    <link rel="stylesheet" href="../css/material.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .instalacion-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .mensaje {
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }
        .mensaje.exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .instrucciones {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .codigo {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="instalacion-container">
        <h1>🔔 Instalación del Sistema de Recordatorios</h1>
        
        <?php if ($instalacion_exitosa): ?>
            <div class="mensaje exito">
                <h3>✅ Instalación Completada Exitosamente</h3>
                <p>El sistema de recordatorios ha sido instalado correctamente.</p>
            </div>
        <?php else: ?>
            <div class="mensaje error">
                <h3>❌ Error en la Instalación</h3>
                <p>Hubo errores durante la instalación del sistema.</p>
            </div>
        <?php endif; ?>
        
        <h3>Mensajes de Instalación:</h3>
        <?php foreach ($mensajes as $mensaje): ?>
            <div class="mensaje exito"><?= htmlspecialchars($mensaje) ?></div>
        <?php endforeach; ?>
        
        <?php foreach ($errores as $error): ?>
            <div class="mensaje error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
        
        <?php if ($instalacion_exitosa): ?>
            <div class="instrucciones">
                <h3>📋 Próximos Pasos</h3>
                
                <h4>1. Configurar WhatsApp</h4>
                <p>Ve a <strong>Recordatorios > Configuración</strong> y configura tu API de WhatsApp:</p>
                <ul>
                    <li>URL de la API</li>
                    <li>Token de autenticación</li>
                    <li>Número de teléfono</li>
                </ul>
                
                <h4>2. Configurar Cron Job</h4>
                <p>Agrega esta línea al crontab de tu servidor para procesar recordatorios automáticamente:</p>
                <div class="codigo">
                    * * * * * /usr/bin/php <?= __DIR__ ?>/cron/recordatorios_cron.php
                </div>
                
                <h4>3. Crear Grupos de Destinatarios</h4>
                <p>Ve a <strong>Recordatorios > Grupos</strong> y crea grupos de usuarios para organizar los destinatarios.</p>
                
                <h4>4. Crear Plantillas de Mensajes</h4>
                <p>Ve a <strong>Recordatorios > Plantillas</strong> y crea plantillas personalizadas para tus mensajes.</p>
                
                <h4>5. Probar el Sistema</h4>
                <p>Ejecuta el script de prueba para verificar que todo funciona correctamente:</p>
                <div class="codigo">
                    <a href="test_recordatorios.php" target="_blank">Ejecutar Prueba</a>
                </div>
                
                <h4>6. Acceder al Sistema</h4>
                <p>Ve a <strong>Recordatorios</strong> en el menú principal para comenzar a usar el sistema.</p>
                
                <div style="margin-top: 30px; text-align: center;">
                    <a href="RecordatoriosSistema.php" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
                        🚀 Ir al Sistema de Recordatorios
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="instrucciones">
                <h3>🔧 Solución de Problemas</h3>
                <p>Si hubo errores durante la instalación, verifica:</p>
                <ul>
                    <li>Permisos de escritura en el directorio del proyecto</li>
                    <li>Conexión a la base de datos</li>
                    <li>Espacio disponible en disco</li>
                    <li>Versión de PHP (requerida: 7.4+)</li>
                </ul>
                <p>Intenta ejecutar la instalación nuevamente después de resolver los problemas.</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
            <p>Sistema de Recordatorios - Doctor Pez v1.0</p>
        </div>
    </div>
</body>
</html>
