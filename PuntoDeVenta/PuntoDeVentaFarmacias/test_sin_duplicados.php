<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test Sin Duplicados - Solo Tareas Asignadas</h1>";

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
    echo "Nombre: " . $row['Nombre_Apellidos'] . "<br>";
    
    echo "<h2>1. Probando consulta corregida (solo asignadas)</h2>";
    
    $tareasController = new TareasController($conn, $userId, $sucursalId);
    
    // Probar getTareasAsignadas con la lógica corregida
    $tareas = $tareasController->getTareasAsignadas();
    echo "Tareas encontradas (solo asignadas): " . $tareas->num_rows . "<br>";
    
    if ($tareas->num_rows > 0) {
        echo "<h3>Tareas del usuario (sin duplicados):</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Asignado a</th><th>Creado por</th><th>Nombre Asignado</th><th>Nombre Creador</th></tr>";
        
        while ($tarea = $tareas->fetch_assoc()) {
            $esAsignada = ($tarea['asignado_a'] == $userId) ? '✅' : '❌';
            $esCreada = ($tarea['creado_por'] == $userId) ? '✅' : '❌';
            
            echo "<tr>";
            echo "<td>" . $tarea['id'] . "</td>";
            echo "<td>" . htmlspecialchars($tarea['titulo']) . "</td>";
            echo "<td>" . $tarea['estado'] . "</td>";
            echo "<td>$esAsignada " . $tarea['asignado_a'] . "</td>";
            echo "<td>$esCreada " . $tarea['creado_por'] . "</td>";
            echo "<td>" . ($tarea['asignado_nombre'] ?? 'NULL') . "</td>";
            echo "<td>" . ($tarea['creador_nombre'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No se encontraron tareas asignadas<br>";
    }
    
    echo "<h2>2. Verificando estadísticas</h2>";
    
    $estadisticas = $tareasController->getEstadisticas();
    $stats = [];
    while ($row_stat = $estadisticas->fetch_assoc()) {
        $stats[$row_stat['estado']] = $row_stat['cantidad'];
    }
    
    echo "Estadísticas: " . json_encode($stats) . "<br>";
    
    echo "<h2>3. Probando ArrayTareas.php</h2>";
    
    // Simular petición AJAX
    $_POST['accion'] = 'listar';
    $_POST['estado'] = '';
    $_POST['prioridad'] = '';
    $_POST['fecha'] = '';
    
    ob_start();
    include 'Controladores/ArrayTareas.php';
    $output = ob_get_clean();
    
    echo "✅ ArrayTareas.php ejecutado<br>";
    
    // Decodificar JSON para verificar
    $response = json_decode($output, true);
    if ($response) {
        echo "Success: " . ($response['success'] ? 'true' : 'false') . "<br>";
        echo "Total tareas: " . count($response['data']) . "<br>";
        
        if (count($response['data']) > 0) {
            echo "<h3>Primera tarea encontrada:</h3>";
            $primeraTarea = $response['data'][0];
            echo "ID: " . $primeraTarea['id'] . "<br>";
            echo "Título: " . $primeraTarea['titulo'] . "<br>";
            echo "Asignado a: " . $primeraTarea['asignado_a'] . "<br>";
            echo "Creado por: " . $primeraTarea['creado_por'] . "<br>";
        }
    } else {
        echo "❌ Respuesta JSON inválida<br>";
        echo "Output: " . htmlspecialchars($output) . "<br>";
    }
    
    echo "<h2>4. Verificando consulta SQL directa (sin duplicados)</h2>";
    
    $sql = "SELECT 
                t.id,
                t.titulo,
                t.estado,
                t.asignado_a,
                t.creado_por,
                u_asignado.Nombre_Apellidos as asignado_nombre,
                u_creador.Nombre_Apellidos as creador_nombre
            FROM Tareas t
            LEFT JOIN Usuarios_PV u_asignado ON t.asignado_a = u_asignado.Id_PvUser
            LEFT JOIN Usuarios_PV u_creador ON t.creado_por = u_creador.Id_PvUser
            WHERE t.asignado_a = ?
            ORDER BY t.id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Consulta directa - Tareas encontradas: " . $result->num_rows . "<br>";
    
    if ($result->num_rows > 0) {
        echo "<h3>Resultado de consulta directa (sin duplicados):</h3>";
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
    
    echo "<h2>✅ Test Completado</h2>";
    echo "<p>Ahora el sistema debería mostrar solo las tareas asignadas al usuario actual, sin duplicados.</p>";
    echo "<p>Las tareas creadas por el usuario para otros usuarios NO aparecerán en esta vista.</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}
?>
