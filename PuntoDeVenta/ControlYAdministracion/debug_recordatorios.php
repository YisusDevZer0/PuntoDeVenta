<?php
// Debug para verificar tablas de recordatorios
session_start();

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo "Error: No hay sesión válida";
    exit();
}

echo "<h1>Debug de Tablas de Recordatorios</h1>";

// Incluir conexión directamente
include_once "Controladores/db_connect.php";

// El archivo db_connect.php usa $conn, así que lo asignamos a $con
if (isset($conn) && $conn) {
    $con = $conn;
}

// Verificar conexión
if (!isset($con) || !$con) {
    echo "<p style='color: red;'>Error: No hay conexión a la base de datos</p>";
    echo "<p>Variable \$con: " . (isset($con) ? "definida" : "NO definida") . "</p>";
    echo "<p>Tipo de \$con: " . (isset($con) ? gettype($con) : "N/A") . "</p>";
    
    // Intentar crear conexión manualmente con las credenciales correctas
    echo "<h2>Intentando conexión manual...</h2>";
    try {
        $manual_con = new mysqli("localhost", "u858848268_devpezer0", "F9+nIIOuCh8yI6wu4!08", "u858848268_doctorpez");
        if ($manual_con->connect_error) {
            echo "<p style='color: red;'>Error de conexión manual: " . $manual_con->connect_error . "</p>";
        } else {
            echo "<p style='color: green;'>Conexión manual: OK</p>";
            $con = $manual_con;
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Excepción en conexión manual: " . $e->getMessage() . "</p>";
    }
    
    if (!isset($con) || !$con) {
        exit();
    }
} else {
    echo "<p style='color: green;'>Conexión a base de datos: OK</p>";
}

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