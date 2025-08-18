<?php
// Script de diagnóstico del servidor
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico del Servidor - Controlador del Checador</h1>";

// Verificar información del servidor
echo "<h2>Información del Servidor:</h2>";
echo "<ul>";
echo "<li><strong>PHP Version:</strong> " . phpversion() . "</li>";
echo "<li><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No disponible') . "</li>";
echo "<li><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'No disponible') . "</li>";
echo "<li><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'No disponible') . "</li>";
echo "<li><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'No disponible') . "</li>";
echo "</ul>";

// Verificar archivos necesarios
echo "<h2>Verificación de Archivos:</h2>";

$filesToCheck = [
    'ChecadorController.php' => __DIR__ . '/ChecadorController.php',
    'db_connect.php' => __DIR__ . '/db_connect.php',
    'ControladorUsuario.php' => __DIR__ . '/ControladorUsuario.php'
];

foreach ($filesToCheck as $name => $path) {
    if (file_exists($path)) {
        echo "<p style='color: green;'>✅ $name existe en: $path</p>";
        echo "<p>Permisos: " . substr(sprintf('%o', fileperms($path)), -4) . "</p>";
        echo "<p>Tamaño: " . filesize($path) . " bytes</p>";
    } else {
        echo "<p style='color: red;'>❌ $name NO existe en: $path</p>";
    }
}

// Verificar conexión a la base de datos
echo "<h2>Verificación de Base de Datos:</h2>";

try {
    include_once "db_connect.php";
    if (isset($conn) && $conn) {
        echo "<p style='color: green;'>✅ Conexión a la base de datos establecida</p>";
        
        // Verificar tablas necesarias
        $tables = ['asistencias', 'ubicaciones_trabajo', 'Usuarios_PV'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<p style='color: green;'>✅ Tabla '$table' existe</p>";
            } else {
                echo "<p style='color: red;'>❌ Tabla '$table' NO existe</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ No se pudo establecer conexión a la base de datos</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error al conectar con la base de datos: " . $e->getMessage() . "</p>";
}

// Verificar extensiones PHP necesarias
echo "<h2>Extensiones PHP:</h2>";
$extensions = ['mysqli', 'curl', 'json'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✅ Extensión $ext está cargada</p>";
    } else {
        echo "<p style='color: red;'>❌ Extensión $ext NO está cargada</p>";
    }
}

// Verificar permisos de escritura
echo "<h2>Permisos de Escritura:</h2>";
$dirsToCheck = [
    'Directorio actual' => __DIR__,
    'Directorio temporal' => sys_get_temp_dir()
];

foreach ($dirsToCheck as $name => $dir) {
    if (is_writable($dir)) {
        echo "<p style='color: green;'>✅ $name es escribible</p>";
    } else {
        echo "<p style='color: red;'>❌ $name NO es escribible</p>";
    }
}

// Verificar configuración de errores
echo "<h2>Configuración de Errores:</h2>";
echo "<ul>";
echo "<li><strong>error_reporting:</strong> " . error_reporting() . "</li>";
echo "<li><strong>display_errors:</strong> " . ini_get('display_errors') . "</li>";
echo "<li><strong>log_errors:</strong> " . ini_get('log_errors') . "</li>";
echo "<li><strong>error_log:</strong> " . ini_get('error_log') . "</li>";
echo "</ul>";

echo "<h2>Diagnóstico completado</h2>";
?>
