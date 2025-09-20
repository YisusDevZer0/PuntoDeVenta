<?php
// Archivo de diagnóstico para verificar la configuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnóstico del Sistema</h2>";

// Verificar PHP
echo "<h3>Información de PHP</h3>";
echo "Versión de PHP: " . phpversion() . "<br>";
echo "Sistema operativo: " . php_uname() . "<br>";
echo "Directorio actual: " . getcwd() . "<br>";

// Verificar sesión
echo "<h3>Estado de la Sesión</h3>";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
echo "Estado de sesión: " . (session_status() == PHP_SESSION_ACTIVE ? "Activa" : "Inactiva") . "<br>";
echo "ID de sesión: " . session_id() . "<br>";

// Verificar archivos
echo "<h3>Verificación de Archivos</h3>";
$files_to_check = [
    '../../../vendor/autoload.php',
    '../Controladores/ControladorUsuario.php',
    '../Controladores/db_connect.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    echo "Archivo: $file - " . (file_exists($full_path) ? "✅ Existe" : "❌ No existe") . "<br>";
    if (file_exists($full_path)) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;Ruta completa: $full_path<br>";
    }
}

// Verificar PhpSpreadsheet
echo "<h3>Verificación de PhpSpreadsheet</h3>";
try {
    require_once '../../../vendor/autoload.php';
    echo "✅ Autoloader cargado correctamente<br>";
    
    if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        echo "✅ Clase Spreadsheet disponible<br>";
    } else {
        echo "❌ Clase Spreadsheet no disponible<br>";
    }
    
    if (class_exists('PhpOffice\PhpSpreadsheet\Writer\Xlsx')) {
        echo "✅ Clase Xlsx Writer disponible<br>";
    } else {
        echo "❌ Clase Xlsx Writer no disponible<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error al cargar PhpSpreadsheet: " . $e->getMessage() . "<br>";
}

// Verificar permisos de escritura
echo "<h3>Permisos de Escritura</h3>";
$temp_dir = sys_get_temp_dir();
echo "Directorio temporal: $temp_dir<br>";
echo "Permisos de escritura: " . (is_writable($temp_dir) ? "✅ Escribible" : "❌ No escribible") . "<br>";

// Verificar memoria
echo "<h3>Configuración de Memoria</h3>";
echo "Límite de memoria: " . ini_get('memory_limit') . "<br>";
echo "Tiempo máximo de ejecución: " . ini_get('max_execution_time') . " segundos<br>";

// Verificar headers
echo "<h3>Headers HTTP</h3>";
echo "Content-Type actual: " . (headers_sent() ? "Headers ya enviados" : "Headers no enviados") . "<br>";

echo "<h3>Prueba de Descarga</h3>";
echo '<a href="test_excel.php" target="_blank">Probar descarga de Excel</a><br>';
?>

