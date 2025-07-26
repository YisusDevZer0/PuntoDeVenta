<?php
// Archivo de prueba para diagnosticar problemas del dashboard
session_start();

echo "<h2>Diagnóstico del Dashboard</h2>";

// 1. Verificar sesión
echo "<h3>1. Verificación de Sesión:</h3>";
if(isset($_SESSION['ControlMaestro'])) {
    echo "✅ Sesión ControlMaestro: ACTIVA<br>";
} else {
    echo "❌ Sesión ControlMaestro: INACTIVA<br>";
}

if(isset($_SESSION['AdministradorRH'])) {
    echo "✅ Sesión AdministradorRH: ACTIVA<br>";
} else {
    echo "❌ Sesión AdministradorRH: INACTIVA<br>";
}

if(isset($_SESSION['Marketing'])) {
    echo "✅ Sesión Marketing: ACTIVA<br>";
} else {
    echo "❌ Sesión Marketing: INACTIVA<br>";
}

// 2. Verificar conexión a base de datos
echo "<h3>2. Verificación de Base de Datos:</h3>";
try {
    include_once("../Consultas/db_connect.php");
    echo "✅ Conexión a BD: EXITOSA<br>";
    
    // 3. Probar consulta simple
    $testQuery = "SELECT 1 as test";
    $result = $conn->query($testQuery);
    if ($result) {
        echo "✅ Consulta de prueba: EXITOSA<br>";
    } else {
        echo "❌ Consulta de prueba: FALLIDA<br>";
    }
    
    // 4. Verificar tablas principales
    $tables = ['Cajas', 'Ventas_POS', 'Productos_POS', 'Traspasos_generados'];
    foreach ($tables as $table) {
        $checkTable = "SHOW TABLES LIKE '$table'";
        $result = $conn->query($checkTable);
        if ($result && $result->num_rows > 0) {
            echo "✅ Tabla $table: EXISTE<br>";
        } else {
            echo "❌ Tabla $table: NO EXISTE<br>";
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

// 5. Verificar archivos incluidos
echo "<h3>3. Verificación de Archivos:</h3>";
$files = [
    '../Consultas/db_connect.php',
    'Controladores/ControladorUsuario.php',
    'Controladores/ConsultaDashboard.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ Archivo $file: EXISTE<br>";
    } else {
        echo "❌ Archivo $file: NO EXISTE<br>";
    }
}

echo "<h3>4. Información del Sistema:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Directory: " . getcwd() . "<br>";
echo "Script Path: " . __FILE__ . "<br>";

echo "<h3>5. Variables de Sesión:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?> 