<?php
/**
 * Script de instalación para el sistema de chat
 * Ejecuta las consultas SQL necesarias para crear las tablas
 */

// Incluir conexión a la base de datos
include_once "Consultas/db_connect.php";

// Verificar sesión de administrador
session_start();
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])) {
    die("Acceso denegado. Solo administradores pueden ejecutar este script.");
}

echo "<h2>Instalación del Sistema de Chat</h2>";
echo "<p>Iniciando instalación...</p>";

try {
    // Leer el archivo SQL
    $sqlFile = 'database/chat_tables.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Archivo SQL no encontrado: $sqlFile");
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
    
    if ($errorCount === 0) {
        echo "<p style='color: green; font-weight: bold;'>¡Instalación completada exitosamente!</p>";
        echo "<p>El sistema de chat está listo para usar.</p>";
        
        // Crear conversaciones iniciales
        echo "<h4>Creando conversaciones iniciales...</h4>";
        
        // Verificar si ya existen las conversaciones
        $checkQuery = "SELECT COUNT(*) as count FROM chat_conversaciones WHERE nombre_conversacion IN ('Chat General', 'Soporte Técnico')";
        $result = $conn->query($checkQuery);
        $count = $result->fetch_assoc()['count'];
        
        if ($count == 0) {
            // Crear conversación general
            $insertGeneral = "INSERT INTO chat_conversaciones (nombre_conversacion, tipo_conversacion, creado_por) VALUES ('Chat General', 'general', 1)";
            if ($conn->query($insertGeneral)) {
                echo "<p style='color: green;'>✓ Conversación 'Chat General' creada</p>";
            }
            
            // Crear conversación de soporte
            $insertSoporte = "INSERT INTO chat_conversaciones (nombre_conversacion, tipo_conversacion, creado_por) VALUES ('Soporte Técnico', 'grupo', 1)";
            if ($conn->query($insertSoporte)) {
                echo "<p style='color: green;'>✓ Conversación 'Soporte Técnico' creada</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ Las conversaciones iniciales ya existen</p>";
        }
        
        echo "<hr>";
        echo "<h4>Próximos pasos:</h4>";
        echo "<ol>";
        echo "<li>Ve a <a href='Mensajes.php'>Mensajes</a> para probar el sistema</li>";
        echo "<li>Configura las notificaciones push si es necesario</li>";
        echo "<li>Personaliza los permisos según tus necesidades</li>";
        echo "</ol>";
        
    } else {
        echo "<p style='color: red; font-weight: bold;'>La instalación tuvo errores. Revisa los mensajes anteriores.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>Error fatal: " . $e->getMessage() . "</p>";
}

// Cerrar conexión
$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}
h2, h3, h4 {
    color: #333;
}
p {
    margin: 5px 0;
}
hr {
    margin: 20px 0;
    border: 1px solid #ddd;
}
</style>
