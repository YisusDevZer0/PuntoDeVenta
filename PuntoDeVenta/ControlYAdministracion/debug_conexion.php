<?php
/**
 * Script de debug para conexión a la base de datos
 * Prueba diferentes configuraciones y muestra información detallada
 */

echo "<h2>Debug de Conexión a la Base de Datos</h2>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";

// Mostrar información del servidor
echo "<h3>1. Información del servidor:</h3>";
echo "<ul>";
echo "<li><strong>PHP Version:</strong> " . phpversion() . "</li>";
echo "<li><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li><strong>Directorio actual:</strong> " . __DIR__ . "</li>";
echo "<li><strong>Archivo de conexión:</strong> " . __DIR__ . "/Controladores/db_connect.php</li>";
echo "</ul>";

// Verificar si existe el archivo de conexión
echo "<h3>2. Verificando archivo de conexión:</h3>";
$archivo_conexion = __DIR__ . "/Controladores/db_connect.php";
if (file_exists($archivo_conexion)) {
    echo "<div style='color: green;'>✅ Archivo de conexión existe</div>";
    
    // Mostrar contenido del archivo
    echo "<h4>Contenido del archivo de conexión:</h4>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
    echo htmlspecialchars(file_get_contents($archivo_conexion));
    echo "</pre>";
} else {
    echo "<div style='color: red;'>❌ Archivo de conexión no existe</div>";
}

// Probar conexión manual
echo "<h3>3. Probando conexión manual:</h3>";

// Configuración de la base de datos
$servername = 'localhost';
$username = 'u858848268_devpezer0';
$password = 'F9+nIIOuCh8yI6wu4!08';
$dbname = 'u858848268_doctorpez';

echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>Configuración:</strong><br>";
echo "Servidor: $servername<br>";
echo "Usuario: $username<br>";
echo "Base de datos: $dbname<br>";
echo "</div>";

// Probar conexión
$conn_test = mysqli_connect($servername, $username, $password, $dbname);

if ($conn_test) {
    echo "<div style='color: green; padding: 10px; background: #e8f5e8; border-radius: 5px;'>";
    echo "✅ Conexión manual exitosa";
    echo "</div>";
    
    // Probar consulta
    $query = "SELECT COUNT(*) as count FROM Usuarios_PV WHERE Estatus = 'Activo'";
    $result = $conn_test->query($query);
    
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<div style='color: green; padding: 10px; background: #e8f5e8; border-radius: 5px;'>";
        echo "✅ Consulta exitosa. Usuarios activos: $count";
        echo "</div>";
    } else {
        echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
        echo "❌ Error en consulta: " . mysqli_error($conn_test);
        echo "</div>";
    }
    
    mysqli_close($conn_test);
} else {
    echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
    echo "❌ Error de conexión manual: " . mysqli_connect_error();
    echo "</div>";
}

// Probar incluir el archivo de conexión
echo "<h3>4. Probando include del archivo de conexión:</h3>";

try {
    ob_start();
    include_once $archivo_conexion;
    $output = ob_get_clean();
    
    if (isset($conn) && $conn) {
        echo "<div style='color: green; padding: 10px; background: #e8f5e8; border-radius: 5px;'>";
        echo "✅ Include exitoso. Variable \$conn disponible";
        echo "</div>";
        
        // Probar consulta con la conexión incluida
        $query = "SELECT COUNT(*) as count FROM Sucursales";
        $result = $conn->query($query);
        
        if ($result) {
            $count = $result->fetch_assoc()['count'];
            echo "<div style='color: green; padding: 10px; background: #e8f5e8; border-radius: 5px;'>";
            echo "✅ Consulta con conexión incluida exitosa. Sucursales: $count";
            echo "</div>";
        } else {
            echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
            echo "❌ Error en consulta con conexión incluida: " . mysqli_error($conn);
            echo "</div>";
        }
    } else {
        echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
        echo "❌ Include falló. Variable \$conn no disponible";
        echo "</div>";
        
        if (!empty($output)) {
            echo "<div style='background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>Output del include:</strong><br>";
            echo htmlspecialchars($output);
            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; background: #ffe8e8; border-radius: 5px;'>";
    echo "❌ Error en include: " . $e->getMessage();
    echo "</div>";
}

// Verificar extensiones de PHP
echo "<h3>5. Verificando extensiones de PHP:</h3>";
$extensiones = ['mysqli', 'json', 'curl'];
foreach ($extensiones as $ext) {
    if (extension_loaded($ext)) {
        echo "<div style='color: green;'>✅ $ext: Disponible</div>";
    } else {
        echo "<div style='color: red;'>❌ $ext: No disponible</div>";
    }
}

// Verificar permisos de archivos
echo "<h3>6. Verificando permisos de archivos:</h3>";
$archivos_importantes = [
    'Controladores/db_connect.php',
    'Controladores/ChatController.php',
    'api/chat_api.php',
    'Mensajes.php'
];

foreach ($archivos_importantes as $archivo) {
    $ruta_completa = __DIR__ . '/' . $archivo;
    if (file_exists($ruta_completa)) {
        $permisos = fileperms($ruta_completa);
        $permisos_oct = substr(sprintf('%o', $permisos), -4);
        echo "<div style='color: green;'>✅ $archivo: Existe (permisos: $permisos_oct)</div>";
    } else {
        echo "<div style='color: red;'>❌ $archivo: No existe</div>";
    }
}

echo "<hr>";
echo "<h3>Resumen del Debug:</h3>";

if (isset($conn) && $conn) {
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='color: green; margin: 0 0 10px 0;'>✅ Conexión funcionando</h4>";
    echo "<p>La conexión a la base de datos está funcionando correctamente. Puedes proceder con la instalación.</p>";
    echo "<p><a href='instalar_chat_completo.php' style='background: #009CFF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Instalar Chat Completo</a></p>";
    echo "</div>";
} else {
    echo "<div style='background: #ffe8e8; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='color: red; margin: 0 0 10px 0;'>❌ Problema de conexión</h4>";
    echo "<p>Hay un problema con la conexión a la base de datos. Revisa la configuración y los permisos.</p>";
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

pre {
    font-size: 12px;
    line-height: 1.4;
}

code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}
</style>
