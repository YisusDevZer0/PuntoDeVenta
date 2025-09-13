<?php
// Test ultra simple
session_start();

echo "<h1>Test de Recordatorios</h1>";
echo "<p>Página cargada correctamente</p>";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo "<p style='color: red;'>Error: No hay sesión válida</p>";
    echo "<p>Variables de sesión disponibles:</p>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    exit();
}

echo "<p style='color: green;'>Sesión válida encontrada</p>";

// Verificar conexión a base de datos
try {
    include_once "Consultas/db_connect.php";
    if (isset($con) && $con) {
        echo "<p style='color: green;'>Conexión a base de datos: OK</p>";
        
        // Probar consulta simple
        $resultado = $con->query("SELECT 1 as test");
        if ($resultado) {
            echo "<p style='color: green;'>Consulta de prueba: OK</p>";
        } else {
            echo "<p style='color: red;'>Error en consulta: " . $con->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Error: No se pudo conectar a la base de datos</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error de conexión: " . $e->getMessage() . "</p>";
}

echo "<p><a href='RecordatoriosSistema.php'>Ir a RecordatoriosSistema.php</a></p>";
echo "<p><a href='instalar_recordatorios.php'>Ir a instalar_recordatorios.php</a></p>";
?>
