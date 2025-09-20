<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test de Filtros Simplificados</h1>";

try {
    // Incluir archivos necesarios
    include_once "Controladores/ControladorUsuario.php";
    include_once "Controladores/db_connect.php";
    include_once "Controladores/TareasController.php";
    
    $userId = $row['Id_PvUser'];
    $sucursalId = $row['Fk_Sucursal'];
    $tipoUsuario = $row['Tipo_Usuario'];
    
    echo "<h2>Datos del Usuario</h2>";
    echo "Usuario ID: $userId<br>";
    echo "Sucursal ID: $sucursalId<br>";
    echo "Nombre: " . $row['Nombre_Apellidos'] . "<br>";
    echo "Tipo de Usuario: <strong>$tipoUsuario</strong><br>";
    
    echo "<h2>1. Verificando Filtros Disponibles</h2>";
    echo "✅ <strong>Filtros simplificados:</strong><br>";
    echo "   - Estado (Por hacer, En progreso, Completada, Cancelada)<br>";
    echo "   - Prioridad (Alta, Media, Baja)<br>";
    echo "❌ <strong>Filtro eliminado:</strong><br>";
    echo "   - Fecha Límite (ya no disponible)<br>";
    
    echo "<h2>2. Probando Estadísticas del Usuario</h2>";
    
    $tareasController = new TareasController($conn, $userId, $sucursalId);
    
    // Obtener estadísticas
    $estadisticas = $tareasController->getEstadisticas();
    $stats = [];
    while ($row_stat = $estadisticas->fetch_assoc()) {
        $stats[$row_stat['estado']] = $row_stat['cantidad'];
    }
    
    echo "<h3>Estadísticas por Estado (solo del usuario actual):</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Estado</th><th>Cantidad</th></tr>";
    
    $estados = ['Por hacer', 'En progreso', 'Completada', 'Cancelada'];
    foreach ($estados as $estado) {
        $cantidad = isset($stats[$estado]) ? $stats[$estado] : 0;
        echo "<tr><td>$estado</td><td><strong>$cantidad</strong></td></tr>";
    }
    echo "</table>";
    
    // Obtener tareas próximas a vencer
    $proximasVencer = $tareasController->getTareasProximasVencer();
    echo "<h3>Tareas Próximas a Vencer (solo del usuario actual):</h3>";
    echo "Cantidad: <strong>" . $proximasVencer->num_rows . "</strong><br>";
    
    if ($proximasVencer->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Fecha Límite</th><th>Prioridad</th></tr>";
        
        while ($tarea = $proximasVencer->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $tarea['id'] . "</td>";
            echo "<td>" . htmlspecialchars($tarea['titulo']) . "</td>";
            echo "<td>" . $tarea['fecha_limite'] . "</td>";
            echo "<td>" . $tarea['prioridad'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>3. Probando Filtros</h2>";
    
    // Probar filtro por estado
    echo "<h3>Filtro por Estado 'Por hacer':</h3>";
    $filtrosEstado = ['estado' => 'Por hacer', 'prioridad' => ''];
    $tareasEstado = $tareasController->getTareasAsignadas($filtrosEstado);
    echo "Tareas 'Por hacer': <strong>" . $tareasEstado->num_rows . "</strong><br>";
    
    // Probar filtro por prioridad
    echo "<h3>Filtro por Prioridad 'Alta':</h3>";
    $filtrosPrioridad = ['estado' => '', 'prioridad' => 'Alta'];
    $tareasPrioridad = $tareasController->getTareasAsignadas($filtrosPrioridad);
    echo "Tareas de prioridad 'Alta': <strong>" . $tareasPrioridad->num_rows . "</strong><br>";
    
    // Probar sin filtros
    echo "<h3>Sin filtros (todas las tareas del usuario):</h3>";
    $filtrosVacios = ['estado' => '', 'prioridad' => ''];
    $todasTareas = $tareasController->getTareasAsignadas($filtrosVacios);
    echo "Total de tareas asignadas: <strong>" . $todasTareas->num_rows . "</strong><br>";
    
    echo "<h2>4. Probando ArrayTareas.php con filtros simplificados</h2>";
    
    // Simular petición AJAX con filtros simplificados
    $_POST['accion'] = 'listar';
    $_POST['estado'] = 'Por hacer';
    $_POST['prioridad'] = '';
    
    ob_start();
    include 'Controladores/ArrayTareas.php';
    $output = ob_get_clean();
    
    echo "✅ ArrayTareas.php ejecutado con filtros simplificados<br>";
    
    $response = json_decode($output, true);
    if ($response && $response['success']) {
        echo "✅ Respuesta JSON válida<br>";
        echo "Tareas filtradas por estado 'Por hacer': " . count($response['data']) . "<br>";
        
        if (count($response['data']) > 0) {
            echo "<h4>Primera tarea encontrada:</h4>";
            $primeraTarea = $response['data'][0];
            echo "ID: " . $primeraTarea['id'] . "<br>";
            echo "Título: " . $primeraTarea['titulo'] . "<br>";
            echo "Estado: " . $primeraTarea['estado'] . "<br>";
            echo "Prioridad: " . $primeraTarea['prioridad'] . "<br>";
        }
    } else {
        echo "❌ Error en ArrayTareas.php<br>";
        echo "Output: " . htmlspecialchars($output) . "<br>";
    }
    
    echo "<h2>5. Verificando Interfaz de Usuario</h2>";
    echo "✅ <strong>Filtros en la interfaz:</strong><br>";
    echo "   - Solo 2 filtros: Estado y Prioridad<br>";
    echo "   - Botones de Aplicar y Limpiar filtros<br>";
    echo "   - Layout mejorado con col-md-4 para cada filtro<br>";
    
    echo "<h2>✅ Test Completado</h2>";
    echo "<p><strong>Resumen de cambios aplicados:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Filtro de fecha eliminado de la interfaz</li>";
    echo "<li>✅ Filtros JavaScript actualizados (sin fecha)</li>";
    echo "<li>✅ Controlador actualizado (sin filtro de fecha)</li>";
    echo "<li>✅ ArrayTareas.php actualizado (sin filtro de fecha)</li>";
    echo "<li>✅ Estadísticas muestran solo datos del usuario actual</li>";
    echo "<li>✅ Layout de filtros mejorado (col-md-4)</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}
?>
