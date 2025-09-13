<?php
echo "Test 1: PHP funciona<br>";

session_start();
echo "Test 2: Session iniciada<br>";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo "Test 3: No hay sesión válida<br>";
    echo "Variables de sesión:<br>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    exit();
}

echo "Test 3: Sesión válida encontrada<br>";

// Probar conexión a base de datos
try {
    include_once "Consultas/db_connect.php";
    echo "Test 4: Archivo db_connect incluido<br>";
    
    if (isset($con)) {
        echo "Test 5: Variable \$con existe<br>";
        
        $resultado = $con->query("SELECT 1 as test");
        if ($resultado) {
            echo "Test 6: Consulta de prueba exitosa<br>";
        } else {
            echo "Test 6: Error en consulta: " . $con->error . "<br>";
        }
    } else {
        echo "Test 5: Variable \$con NO existe<br>";
    }
} catch (Exception $e) {
    echo "Test 4-6: Error: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Test completado</strong><br>";
echo "<a href='RecordatoriosSistema.php'>Ir a RecordatoriosSistema.php</a>";
?>
