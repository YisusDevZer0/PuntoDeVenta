<?php
// Mostrar la ruta actual
$logFile = __DIR__ . '/path_info.log';
$info = "=== Información del Sistema ===\n";
$info .= "Fecha y hora: " . date('Y-m-d H:i:s') . "\n";
$info .= "Ruta actual (__DIR__): " . __DIR__ . "\n";
$info .= "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
$info .= "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
$info .= "Usuario del servidor: " . get_current_user() . "\n";
$info .= "Permisos del directorio: " . substr(sprintf('%o', fileperms(__DIR__)), -4) . "\n\n";

// Intentar crear un archivo de prueba
$testFile = __DIR__ . '/test.txt';
if (file_put_contents($testFile, 'Test de escritura')) {
    $info .= "Archivo de prueba creado exitosamente en: " . $testFile . "\n";
} else {
    $info .= "Error al crear archivo de prueba. Error: " . error_get_last()['message'] . "\n";
}

// Intentar escribir el log
if (file_put_contents($logFile, $info)) {
    $info .= "\nLog creado exitosamente en: " . $logFile . "\n";
} else {
    $info .= "\nError al crear el log. Error: " . error_get_last()['message'] . "\n";
}

// Mostrar la información en la salida estándar también
echo $info;
?> 