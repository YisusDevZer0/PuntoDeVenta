<?php
/**
 * Script de instalación mejorado para el sistema de chat v2.0
 * Detecta versiones existentes y migra automáticamente
 */

// Incluir conexión a la base de datos
include_once "Consultas/db_connect.php";

// Verificar sesión de administrador
session_start();
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])) {
    die("Acceso denegado. Solo administradores pueden ejecutar este script.");
}

echo "<h2>Instalación del Sistema de Chat v2.0</h2>";
echo "<p>Iniciando instalación...</p>";

try {
    // Verificar si ya existe el sistema de chat
    $checkQuery = "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'chat_conversaciones'";
    $result = $conn->query($checkQuery);
    $tableExists = $result->fetch_assoc()['count'] > 0;
    
    if ($tableExists) {
        echo "<h3>🔄 Sistema de chat detectado - Iniciando migración a v2.0</h3>";
        
        // Verificar versión actual
        $versionQuery = "SELECT COUNT(*) as count FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'chat_conversaciones' AND column_name = 'descripcion'";
        $result = $conn->query($versionQuery);
        $isV2 = $result->fetch_assoc()['count'] > 0;
        
        if ($isV2) {
            echo "<p style='color: blue;'>ℹ El sistema ya está en la versión 2.0</p>";
            echo "<p>¿Deseas reinstalar? <a href='?force=1' style='color: red;'>Sí, reinstalar</a> | <a href='Mensajes.php'>No, ir al chat</a></p>";
            
            if (!isset($_GET['force'])) {
                exit();
            }
        }
        
        // Ejecutar migración
        $migrationFile = 'database/migrar_chat_v2.sql';
        if (file_exists($migrationFile)) {
            echo "<p>Ejecutando migración...</p>";
            $migrationSql = file_get_contents($migrationFile);
            $queries = array_filter(array_map('trim', explode(';', $migrationSql)));
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($queries as $query) {
                if (empty($query) || strpos($query, '--') === 0) {
                    continue;
                }
                
                try {
                    if ($conn->query($query)) {
                        $successCount++;
                        echo "<p style='color: green;'>✓ Migración ejecutada correctamente</p>";
                    } else {
                        $errorCount++;
                        echo "<p style='color: red;'>✗ Error en migración: " . $conn->error . "</p>";
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    echo "<p style='color: red;'>✗ Excepción en migración: " . $e->getMessage() . "</p>";
                }
            }
            
            echo "<h4>Resumen de la migración:</h4>";
            echo "<p>Consultas exitosas: <strong style='color: green;'>$successCount</strong></p>";
            echo "<p>Consultas con error: <strong style='color: red;'>$errorCount</strong></p>";
            
        } else {
            echo "<p style='color: red;'>Archivo de migración no encontrado. Usando instalación completa...</p>";
            $tableExists = false; // Forzar instalación completa
        }
    }
    
    if (!$tableExists) {
        echo "<h3>🆕 Instalación nueva del sistema de chat v2.0</h3>";
        
        // Leer el archivo SQL mejorado
        $sqlFile = 'database/chat_tables_mejorado.sql';
        if (!file_exists($sqlFile)) {
            // Intentar con el archivo original si el mejorado no existe
            $sqlFile = 'database/chat_tables.sql';
            if (!file_exists($sqlFile)) {
                throw new Exception("Archivo SQL no encontrado: $sqlFile");
            }
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Dividir en consultas individuales
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($queries as $query) {
            if (empty($query) || strpos($query, '--') === 0) {
                continue;
            }
            
            try {
                if ($conn->query($query)) {
                    $successCount++;
                    echo "<p style='color: green;'>✓ Consulta ejecutada correctamente</p>";
                } else {
                    $errorCount++;
                    echo "<p style='color: red;'>✗ Error en consulta: " . $conn->error . "</p>";
                    echo "<p style='color: gray;'>Consulta: " . substr($query, 0, 100) . "...</p>";
                }
            } catch (Exception $e) {
                $errorCount++;
                echo "<p style='color: red;'>✗ Excepción en consulta: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<hr>";
        echo "<h3>Resumen de la instalación:</h3>";
        echo "<p>Consultas exitosas: <strong style='color: green;'>$successCount</strong></p>";
        echo "<p>Consultas con error: <strong style='color: red;'>$errorCount</strong></p>";
    }
    
    if ($errorCount === 0) {
        echo "<p style='color: green; font-weight: bold;'>¡Instalación/Migración completada exitosamente!</p>";
        echo "<p>El sistema de chat v2.0 está listo para usar.</p>";
        
        // Verificar funcionalidades instaladas
        echo "<h4>🔍 Verificando funcionalidades instaladas:</h4>";
        
        $features = [
            'Tablas principales' => "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name LIKE 'chat_%'",
            'Triggers automáticos' => "SELECT COUNT(*) FROM information_schema.triggers WHERE trigger_schema = DATABASE() AND trigger_name LIKE 'tr_chat_%'",
            'Procedimientos almacenados' => "SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema = DATABASE() AND routine_name LIKE 'sp_chat_%'",
            'Vistas optimizadas' => "SELECT COUNT(*) FROM information_schema.views WHERE table_schema = DATABASE() AND table_name LIKE 'v_chat_%'",
            'Conversaciones iniciales' => "SELECT COUNT(*) FROM chat_conversaciones WHERE nombre_conversacion IN ('Chat General', 'Soporte Técnico', 'Notificaciones del Sistema')"
        ];
        
        foreach ($features as $feature => $query) {
            $result = $conn->query($query);
            $count = $result->fetch_assoc()['count'];
            $status = $count > 0 ? '✅' : '❌';
            echo "<p>$status <strong>$feature:</strong> $count elementos</p>";
        }
        
        // Crear conversaciones iniciales si no existen
        echo "<h4>📝 Creando conversaciones iniciales...</h4>";
        
        $conversacionesIniciales = [
            ['Chat General', 'general', 'Conversación general para todos los usuarios del sistema'],
            ['Soporte Técnico', 'canal', 'Canal de soporte técnico para reportar problemas'],
            ['Notificaciones del Sistema', 'sistema', 'Notificaciones automáticas del sistema']
        ];
        
        foreach ($conversacionesIniciales as $conv) {
            $checkConv = "SELECT COUNT(*) as count FROM chat_conversaciones WHERE nombre_conversacion = ?";
            $stmt = $conn->prepare($checkConv);
            $stmt->bind_param("s", $conv[0]);
            $stmt->execute();
            $exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
            
            if (!$exists) {
                $insertConv = "INSERT INTO chat_conversaciones (nombre_conversacion, descripcion, tipo_conversacion, creado_por, privado) VALUES (?, ?, ?, 1, 0)";
                $stmt = $conn->prepare($insertConv);
                $stmt->bind_param("sss", $conv[0], $conv[2], $conv[1]);
                if ($stmt->execute()) {
                    echo "<p style='color: green;'>✓ Conversación '$conv[0]' creada</p>";
                } else {
                    echo "<p style='color: red;'>✗ Error al crear '$conv[0]': " . $stmt->error . "</p>";
                }
            } else {
                echo "<p style='color: blue;'>ℹ Conversación '$conv[0]' ya existe</p>";
            }
        }
        
        // Configurar usuarios existentes
        echo "<h4>👥 Configurando usuarios existentes...</h4>";
        
        $usuariosQuery = "SELECT Id_PvUser FROM Usuarios_PV WHERE Estatus = 'Activo'";
        $result = $conn->query($usuariosQuery);
        $usuarios = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($usuarios as $usuario) {
            $userId = $usuario['Id_PvUser'];
            
            // Crear configuración si no existe
            $checkConfig = "SELECT COUNT(*) as count FROM chat_configuraciones WHERE usuario_id = ?";
            $stmt = $conn->prepare($checkConfig);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $hasConfig = $stmt->get_result()->fetch_assoc()['count'] > 0;
            
            if (!$hasConfig) {
                $insertConfig = "INSERT INTO chat_configuraciones (usuario_id) VALUES (?)";
                $stmt = $conn->prepare($insertConfig);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
            }
            
            // Crear estado de usuario si no existe
            $checkEstado = "SELECT COUNT(*) as count FROM chat_estados_usuario WHERE usuario_id = ?";
            $stmt = $conn->prepare($checkEstado);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $hasEstado = $stmt->get_result()->fetch_assoc()['count'] > 0;
            
            if (!$hasEstado) {
                $insertEstado = "INSERT INTO chat_estados_usuario (usuario_id, estado) VALUES (?, 'offline')";
                $stmt = $conn->prepare($insertEstado);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
            }
        }
        
        echo "<p style='color: green;'>✓ Usuarios configurados: " . count($usuarios) . "</p>";
        
        echo "<hr>";
        echo "<h4>🎉 ¡Sistema de Chat v2.0 listo!</h4>";
        echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h5>Nuevas funcionalidades disponibles:</h5>";
        echo "<ul>";
        echo "<li>✅ <strong>Roles de usuario</strong> (admin, moderador, miembro)</li>";
        echo "<li>✅ <strong>Estados de conexión</strong> (online, offline, ausente, ocupado)</li>";
        echo "<li>✅ <strong>Auditoría de mensajes</strong> eliminados</li>";
        echo "<li>✅ <strong>Prioridades de mensajes</strong> (baja, normal, alta, urgente)</li>";
        echo "<li>✅ <strong>Búsqueda de texto completo</strong></li>";
        echo "<li>✅ <strong>Configuración avanzada</strong> por usuario</li>";
        echo "<li>✅ <strong>Estadísticas del chat</strong></li>";
        echo "<li>✅ <strong>Limpieza automática</strong> de mensajes antiguos</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<h4>Próximos pasos:</h4>";
        echo "<ol>";
        echo "<li>Ve a <a href='Mensajes.php' style='color: #009CFF; font-weight: bold;'>Mensajes</a> para probar el sistema</li>";
        echo "<li>Configura las notificaciones push si es necesario</li>";
        echo "<li>Personaliza los permisos según tus necesidades</li>";
        echo "<li>Revisa la <a href='README_CHAT.md' style='color: #009CFF;'>documentación completa</a></li>";
        echo "</ol>";
        
    } else {
        echo "<p style='color: red; font-weight: bold;'>La instalación/migración tuvo errores. Revisa los mensajes anteriores.</p>";
        echo "<p>Puedes intentar ejecutar el script nuevamente o revisar los logs del servidor.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>Error fatal: " . $e->getMessage() . "</p>";
    echo "<p>Detalles del error: " . $e->getTraceAsString() . "</p>";
}

// Cerrar conexión
$conn->close();
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

h2, h3, h4, h5 {
    color: #2c3e50;
    margin-top: 20px;
}

h2 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 30px;
}

p {
    margin: 8px 0;
    line-height: 1.6;
}

hr {
    margin: 25px 0;
    border: none;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 1px;
}

ul {
    margin: 10px 0;
    padding-left: 20px;
}

li {
    margin: 5px 0;
}

a {
    color: #009CFF;
    text-decoration: none;
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
}

.success-box {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    padding: 15px;
    border-radius: 5px;
    margin: 10px 0;
}

.error-box {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    padding: 15px;
    border-radius: 5px;
    margin: 10px 0;
}

.info-box {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
    padding: 15px;
    border-radius: 5px;
    margin: 10px 0;
}

.feature-list {
    background: #e8f5e8;
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
    border-left: 5px solid #28a745;
}

.step-list {
    background: #fff3cd;
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
    border-left: 5px solid #ffc107;
}

@media (max-width: 768px) {
    body {
        padding: 10px;
    }
    
    h2 {
        font-size: 1.5em;
        padding: 10px;
    }
}
</style>
