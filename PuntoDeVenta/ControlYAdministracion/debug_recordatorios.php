<?php
// Debug para verificar tablas de recordatorios
session_start();

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo "Error: No hay sesión válida";
    exit();
}

// Incluir controlador de usuario
include_once "Controladores/ControladorUsuario.php";

echo "<h1>Debug de Tablas de Recordatorios</h1>";

// Verificar conexión
if (!isset($con) || !$con) {
    echo "<p style='color: red;'>Error: No hay conexión a la base de datos</p>";
    exit();
}

echo "<p style='color: green;'>Conexión a base de datos: OK</p>";

// Verificar tablas con diferentes métodos
$tablas_requeridas = [
    'recordatorios_sistema',
    'recordatorios_destinatarios', 
    'recordatorios_grupos',
    'recordatorios_logs'
];

echo "<h2>Método 1: SHOW TABLES LIKE</h2>";
foreach ($tablas_requeridas as $tabla) {
    $resultado = $con->query("SHOW TABLES LIKE '$tabla'");
    if ($resultado) {
        $count = $resultado->num_rows;
        echo "<p>$tabla: " . ($count > 0 ? "ENCONTRADA ($count filas)" : "NO ENCONTRADA") . "</p>";
    } else {
        echo "<p>$tabla: ERROR - " . $con->error . "</p>";
    }
}

echo "<h2>Método 2: information_schema</h2>";
foreach ($tablas_requeridas as $tabla) {
    $resultado = $con->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '$tabla'");
    if ($resultado) {
        $row = $resultado->fetch_assoc();
        echo "<p>$tabla: " . ($row['count'] > 0 ? "ENCONTRADA" : "NO ENCONTRADA") . "</p>";
    } else {
        echo "<p>$tabla: ERROR - " . $con->error . "</p>";
    }
}

echo "<h2>Método 3: SHOW TABLES (todas las tablas)</h2>";
$resultado = $con->query("SHOW TABLES");
if ($resultado) {
    echo "<p>Tablas que contienen 'recordatorios':</p><ul>";
    while ($row = $resultado->fetch_array()) {
        $tabla = $row[0];
        if (strpos($tabla, 'recordatorios') !== false) {
            echo "<li>$tabla</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>Error: " . $con->error . "</p>";
}

echo "<h2>Método 4: Probar consulta directa</h2>";
try {
    $resultado = $con->query("SELECT COUNT(*) as count FROM recordatorios_sistema");
    if ($resultado) {
        $row = $resultado->fetch_assoc();
        echo "<p>recordatorios_sistema: EXISTE y tiene " . $row['count'] . " registros</p>";
    } else {
        echo "<p>recordatorios_sistema: ERROR - " . $con->error . "</p>";
    }
} catch (Exception $e) {
    echo "<p>recordatorios_sistema: EXCEPCIÓN - " . $e->getMessage() . "</p>";
}

echo "<p><a href='RecordatoriosSistema.php'>Volver a RecordatoriosSistema.php</a></p>";
?>
