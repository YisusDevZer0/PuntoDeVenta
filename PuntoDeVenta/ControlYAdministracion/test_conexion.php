<?php
/**
 * Script de prueba de conexión a la base de datos
 * Verifica que la conexión funcione correctamente
 */

echo "<h2>Prueba de Conexión a la Base de Datos</h2>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";

// Incluir conexión a la base de datos
include_once "Controladores/db_connect.php";

echo "<h3>1. Verificando conexión...</h3>";

if ($conn) {
    echo "<div style='color: green; padding: 10px; background: #e8f5e8; border-radius: 5px;'>";
    echo "✅ Conexión exitosa a la base de datos";
    echo "</div>";
    
    // Obtener información de la conexión
    echo "<h3>2. Información de la conexión:</h3>";
    echo "<ul>";
    echo "<li><strong>Servidor:</strong> " . mysqli_get_host_info($conn) . "</li>";
    echo "<li><strong>Base de datos:</strong> " . mysqli_get_server_info($conn) . "</li>";
    echo "<li><strong>Versión MySQL:</strong> " . mysqli_get_server_info($conn) . "</li>";
    echo "</ul>";
    
    // Probar consulta simple
    echo "<h3>3. Probando consulta simple...</h3>";
    $query = "SELECT COUNT(*) as count FROM Usuarios_PV WHERE Estatus = 'Activo'";
    $result = $conn->query($query);
    
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<div style='color: green; padding: 10px; background: #e8f5e8; border-radius: 5px;'>";
        echo "✅ Consulta exitosa. Usuarios activos: $count";
        echo "</div>";
    } else {
        echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
        echo "❌ Error en consulta: " . mysqli_error($conn);
        echo "</div>";
    }
    
    // Probar consulta de sucursales
    echo "<h3>4. Probando consulta de sucursales...</h3>";
    $query = "SELECT COUNT(*) as count FROM Sucursales";
    $result = $conn->query($query);
    
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<div style='color: green; padding: 10px; background: #e8f5e8; border-radius: 5px;'>";
        echo "✅ Consulta exitosa. Sucursales: $count";
        echo "</div>";
    } else {
        echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
        echo "❌ Error en consulta: " . mysqli_error($conn);
        echo "</div>";
    }
    
    // Probar consulta de tipos de usuario
    echo "<h3>5. Probando consulta de tipos de usuario...</h3>";
    $query = "SELECT COUNT(*) as count FROM Tipos_Usuarios";
    $result = $conn->query($query);
    
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<div style='color: green; padding: 10px; background: #e8f5e8; border-radius: 5px;'>";
        echo "✅ Consulta exitosa. Tipos de usuario: $count";
        echo "</div>";
    } else {
        echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
        echo "❌ Error en consulta: " . mysqli_error($conn);
        echo "</div>";
    }
    
    // Verificar si las tablas del chat existen
    echo "<h3>6. Verificando tablas del chat...</h3>";
    $tablas_chat = [
        'chat_conversaciones',
        'chat_participantes', 
        'chat_mensajes',
        'chat_lecturas',
        'chat_reacciones',
        'chat_configuraciones',
        'chat_estados_usuario',
        'chat_mensajes_eliminados'
    ];
    
    $tablas_existentes = 0;
    foreach ($tablas_chat as $tabla) {
        $query = "SHOW TABLES LIKE '$tabla'";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            $tablas_existentes++;
            echo "<div style='color: green;'>✅ Tabla $tabla: Existe</div>";
        } else {
            echo "<div style='color: red;'>❌ Tabla $tabla: No existe</div>";
        }
    }
    
    echo "<div style='margin: 20px 0; padding: 15px; background: " . ($tablas_existentes > 0 ? '#e8f5e8' : '#ffe8e8') . "; border-radius: 5px;'>";
    echo "<strong>Tablas del chat: $tablas_existentes/" . count($tablas_chat) . "</strong>";
    echo "</div>";
    
    // Verificar permisos de escritura
    echo "<h3>7. Verificando permisos de escritura...</h3>";
    $upload_dir = __DIR__ . '/uploads/chat/';
    if (is_dir($upload_dir)) {
        if (is_writable($upload_dir)) {
            echo "<div style='color: green; padding: 10px; background: #e8f5e8; border-radius: 5px;'>";
            echo "✅ Carpeta de uploads: Escribible";
            echo "</div>";
        } else {
            echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
            echo "❌ Carpeta de uploads: No escribible";
            echo "</div>";
        }
    } else {
        echo "<div style='color: orange; padding: 10px; background: #fff3cd; border-radius: 5px;'>";
        echo "⚠️ Carpeta de uploads: No existe (se creará automáticamente)";
        echo "</div>";
    }
    
} else {
    echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
    echo "❌ Error de conexión a la base de datos";
    echo "</div>";
    
    // Mostrar información de error
    echo "<h3>Información del error:</h3>";
    echo "<ul>";
    echo "<li><strong>Error:</strong> " . mysqli_connect_error() . "</li>";
    echo "<li><strong>Código de error:</strong> " . mysqli_connect_errno() . "</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h3>Resumen:</h3>";

if ($conn) {
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='color: green; margin: 0 0 10px 0;'>✅ Conexión funcionando correctamente</h4>";
    echo "<p>La base de datos está accesible y las consultas funcionan. Puedes proceder con la instalación del chat.</p>";
    echo "<p><a href='instalar_chat_completo.php' style='background: #009CFF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Instalar Chat Completo</a></p>";
    echo "</div>";
} else {
    echo "<div style='background: #ffe8e8; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='color: red; margin: 0 0 10px 0;'>❌ Error de conexión</h4>";
    echo "<p>No se pudo conectar a la base de datos. Revisa la configuración en <code>Controladores/db_connect.php</code></p>";
    echo "</div>";
}

echo "</div>";
?>

<style>
body {
    background-color: #f5f5f5;
    margin: 0;
    padding: 20px;
}

h2, h3 {
    color: #333;
}

h2 {
    text-align: center;
    border-bottom: 2px solid #009CFF;
    padding-bottom: 10px;
}

h3 {
    border-left: 4px solid #009CFF;
    padding-left: 10px;
    margin-top: 30px;
}

ul {
    margin: 10px 0;
    padding-left: 20px;
}

li {
    margin: 5px 0;
}

code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}
</style>
