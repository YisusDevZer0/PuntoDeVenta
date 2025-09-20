<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/db_connect.php";

echo "<h1>Debug de Tareas</h1>";

// Verificar datos del usuario
echo "<h2>1. Datos del Usuario</h2>";
echo "Usuario ID: " . $row['Id_PvUser'] . "<br>";
echo "Sucursal ID: " . $row['Fk_Sucursal'] . "<br>";
echo "Nombre: " . $row['Nombre_Apellidos'] . "<br>";

// Verificar conexión a la base de datos
echo "<h2>2. Conexión a Base de Datos</h2>";
if ($conn) {
    echo "✅ Conexión exitosa<br>";
} else {
    echo "❌ Error de conexión: " . mysqli_connect_error() . "<br>";
    exit;
}

// Verificar si existe la tabla tareas
echo "<h2>3. Verificación de Tabla Tareas</h2>";
$sql = "SHOW TABLES LIKE 'tareas'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    echo "✅ La tabla 'tareas' existe<br>";
    
    // Contar tareas totales
    $sql = "SELECT COUNT(*) as total FROM tareas";
    $result = mysqli_query($conn, $sql);
    $count = mysqli_fetch_assoc($result);
    echo "Total de tareas: " . $count['total'] . "<br>";
    
    // Contar tareas del usuario
    $sql = "SELECT COUNT(*) as total FROM tareas WHERE asignado_a = " . $row['Id_PvUser'];
    $result = mysqli_query($conn, $sql);
    $count = mysqli_fetch_assoc($result);
    echo "Tareas del usuario: " . $count['total'] . "<br>";
    
} else {
    echo "❌ La tabla 'tareas' NO existe<br>";
    echo "<a href='crear_tabla_tareas.php'>Crear tabla de tareas</a><br>";
}

// Probar el controlador
echo "<h2>4. Prueba del Controlador</h2>";
try {
    include_once "Controladores/TareasController.php";
    $tareasController = new TareasController($conn, $row['Id_PvUser'], $row['Fk_Sucursal']);
    echo "✅ Controlador creado exitosamente<br>";
    
    // Probar obtener estadísticas
    $estadisticas = $tareasController->getEstadisticas();
    echo "Estadísticas obtenidas: " . $estadisticas->num_rows . " registros<br>";
    
    // Probar obtener tareas
    $tareas = $tareasController->getTareasAsignadas();
    echo "Tareas obtenidas: " . $tareas->num_rows . " registros<br>";
    
} catch (Exception $e) {
    echo "❌ Error en controlador: " . $e->getMessage() . "<br>";
}

// Probar ArrayTareas.php directamente
echo "<h2>5. Prueba de ArrayTareas.php</h2>";
echo "<a href='test_array_tareas.php'>Probar ArrayTareas.php</a><br>";

// Verificar archivos JavaScript
echo "<h2>6. Verificación de Archivos</h2>";
$archivos = [
    'header.php',
    'Menu.php',
    'navbar.php',
    'Footer.php',
    'Controladores/TareasController.php',
    'Controladores/ArrayTareas.php'
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "✅ $archivo existe<br>";
    } else {
        echo "❌ $archivo NO existe<br>";
    }
}
?>
