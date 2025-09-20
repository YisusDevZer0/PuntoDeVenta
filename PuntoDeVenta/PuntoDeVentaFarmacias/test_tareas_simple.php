<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test Simple de Tareas</h1>";

try {
    // Incluir archivos necesarios
    include_once "Controladores/ControladorUsuario.php";
    include_once "Controladores/db_connect.php";
    include_once "Controladores/TareasController.php";
    
    $userId = $row['Id_PvUser'];
    $sucursalId = $row['Fk_Sucursal'];
    
    echo "<h2>Datos del Usuario</h2>";
    echo "Usuario ID: $userId<br>";
    echo "Sucursal ID: $sucursalId<br>";
    
    echo "<h2>1. Verificando tareas directamente en la base de datos</h2>";
    
    $sql = "SELECT * FROM Tareas WHERE asignado_a = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Tareas encontradas: " . $result->num_rows . "<br>";
    
    if ($result->num_rows > 0) {
        echo "<h3>Tareas del usuario:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Prioridad</th><th>Asignado a</th><th>Creado por</th></tr>";
        
        while ($tarea = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $tarea['id'] . "</td>";
            echo "<td>" . htmlspecialchars($tarea['titulo']) . "</td>";
            echo "<td>" . $tarea['estado'] . "</td>";
            echo "<td>" . $tarea['prioridad'] . "</td>";
            echo "<td>" . $tarea['asignado_a'] . "</td>";
            echo "<td>" . $tarea['creado_por'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No se encontraron tareas asignadas al usuario<br>";
        
        // Verificar si hay tareas en general
        $sql = "SELECT COUNT(*) as total FROM Tareas";
        $result = $conn->query($sql);
        $count = $result->fetch_assoc();
        echo "Total de tareas en la base de datos: " . $count['total'] . "<br>";
        
        if ($count['total'] > 0) {
            echo "<h3>Mostrando todas las tareas para verificar:</h3>";
            $sql = "SELECT * FROM Tareas LIMIT 10";
            $result = $conn->query($sql);
            
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Asignado a</th><th>Creado por</th></tr>";
            
            while ($tarea = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $tarea['id'] . "</td>";
                echo "<td>" . htmlspecialchars($tarea['titulo']) . "</td>";
                echo "<td>" . $tarea['estado'] . "</td>";
                echo "<td>" . $tarea['asignado_a'] . "</td>";
                echo "<td>" . $tarea['creado_por'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<h2>2. Probando TareasController</h2>";
    
    $tareasController = new TareasController($conn, $userId, $sucursalId);
    
    echo "<h3>2.1. Probando getTareasAsignadas()</h3>";
    try {
        $tareas = $tareasController->getTareasAsignadas();
        echo "✅ getTareasAsignadas() ejecutado<br>";
        echo "Tareas encontradas: " . $tareas->num_rows . "<br>";
        
        if ($tareas->num_rows > 0) {
            echo "<h4>Tareas encontradas por el controlador:</h4>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Asignado a</th><th>Nombre Asignado</th></tr>";
            
            while ($tarea = $tareas->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $tarea['id'] . "</td>";
                echo "<td>" . htmlspecialchars($tarea['titulo']) . "</td>";
                echo "<td>" . $tarea['estado'] . "</td>";
                echo "<td>" . $tarea['asignado_a'] . "</td>";
                echo "<td>" . ($tarea['asignado_nombre'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error en getTareasAsignadas(): " . $e->getMessage() . "<br>";
    }
    
    echo "<h3>2.2. Probando getEstadisticas()</h3>";
    try {
        $estadisticas = $tareasController->getEstadisticas();
        echo "✅ getEstadisticas() ejecutado<br>";
        
        $stats = [];
        while ($row_stat = $estadisticas->fetch_assoc()) {
            $stats[$row_stat['estado']] = $row_stat['cantidad'];
        }
        
        echo "Estadísticas: " . json_encode($stats) . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Error en getEstadisticas(): " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>3. Simulando ArrayTareas.php</h2>";
    
    // Simular petición AJAX
    $_POST['accion'] = 'listar';
    $_POST['estado'] = '';
    $_POST['prioridad'] = '';
    $_POST['asignado_a'] = '';
    
    ob_start();
    include 'Controladores/ArrayTareas.php';
    $output = ob_get_clean();
    
    echo "✅ ArrayTareas.php ejecutado<br>";
    echo "<h3>Respuesta JSON:</h3>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    // Decodificar JSON para verificar
    $response = json_decode($output, true);
    if ($response) {
        echo "<h3>Datos decodificados:</h3>";
        echo "Success: " . ($response['success'] ? 'true' : 'false') . "<br>";
        echo "Total tareas: " . count($response['data']) . "<br>";
        
        if (isset($response['debug'])) {
            echo "Debug info: " . json_encode($response['debug'], JSON_PRETTY_PRINT) . "<br>";
        }
    }
    
    echo "<h2>✅ Test Completado</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
