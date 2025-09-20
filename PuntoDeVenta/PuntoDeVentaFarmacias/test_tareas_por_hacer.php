<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test TareasPorHacer.php</h1>";

try {
    echo "<h2>1. Simulando includes de TareasPorHacer.php</h2>";
    
    // Simular los includes que hace TareasPorHacer.php
    include_once "Controladores/ControladorUsuario.php";
    echo "✅ ControladorUsuario.php incluido<br>";
    
    include_once "Controladores/TareasController.php";
    echo "✅ TareasController.php incluido<br>";
    
    echo "<h2>2. Verificando variables necesarias</h2>";
    
    if (!isset($row)) {
        throw new Exception("Variable \$row no está definida");
    }
    
    if (!isset($row['Id_PvUser'])) {
        throw new Exception("Variable \$row['Id_PvUser'] no está definida");
    }
    
    if (!isset($row['Fk_Sucursal'])) {
        throw new Exception("Variable \$row['Fk_Sucursal'] no está definida");
    }
    
    if (!isset($conn)) {
        throw new Exception("Variable \$conn no está definida");
    }
    
    echo "✅ Todas las variables necesarias están disponibles<br>";
    
    echo "<h2>3. Instanciando TareasController</h2>";
    
    $userId = $row['Id_PvUser'];
    $sucursalId = $row['Fk_Sucursal'];
    
    echo "Usuario ID: $userId<br>";
    echo "Sucursal ID: $sucursalId<br>";
    
    $tareasController = new TareasController($conn, $userId, $sucursalId);
    echo "✅ TareasController instanciado correctamente<br>";
    
    echo "<h2>4. Probando métodos del controlador</h2>";
    
    // Probar getEstadisticas
    try {
        $estadisticas = $tareasController->getEstadisticas();
        echo "✅ getEstadisticas() funciona<br>";
        
        $stats = [];
        while ($row_stat = $estadisticas->fetch_assoc()) {
            $stats[$row_stat['estado']] = $row_stat['cantidad'];
        }
        
        echo "Estadísticas obtenidas: " . json_encode($stats) . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Error en getEstadisticas(): " . $e->getMessage() . "<br>";
    }
    
    // Probar getTareasProximasVencer
    try {
        $proximas = $tareasController->getTareasProximasVencer();
        echo "✅ getTareasProximasVencer() funciona<br>";
        echo "Tareas próximas a vencer: " . $proximas->num_rows . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Error en getTareasProximasVencer(): " . $e->getMessage() . "<br>";
    }
    
    // Probar getUsuariosDisponibles
    try {
        $usuarios = $tareasController->getUsuariosDisponibles();
        echo "✅ getUsuariosDisponibles() funciona<br>";
        echo "Usuarios disponibles: " . $usuarios->num_rows . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Error en getUsuariosDisponibles(): " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>5. Probando ArrayTareas.php</h2>";
    
    // Simular petición AJAX
    $_POST['accion'] = 'listar';
    $_POST['estado'] = '';
    $_POST['prioridad'] = '';
    $_POST['asignado_a'] = '';
    
    ob_start();
    include 'Controladores/ArrayTareas.php';
    $output = ob_get_clean();
    
    echo "✅ ArrayTareas.php se ejecutó<br>";
    
    // Decodificar JSON para verificar
    $response = json_decode($output, true);
    if ($response) {
        echo "✅ Respuesta JSON válida<br>";
        echo "Success: " . ($response['success'] ? 'true' : 'false') . "<br>";
        if (isset($response['data'])) {
            echo "Datos: " . count($response['data']) . " tareas<br>";
        }
    } else {
        echo "❌ Respuesta JSON inválida<br>";
        echo "Output: " . htmlspecialchars($output) . "<br>";
    }
    
    echo "<h2>✅ Test Completado - TareasPorHacer.php debería funcionar</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error encontrado:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
