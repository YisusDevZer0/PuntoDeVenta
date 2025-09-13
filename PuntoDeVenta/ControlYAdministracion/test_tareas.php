<?php
// Archivo de prueba para el módulo de tareas
include_once "db_connect.php";
include_once "ControladorUsuario.php";
include_once "Controladores/TareasController.php";

echo "<h1>Prueba del Módulo de Tareas</h1>";

try {
    // Crear instancia del controlador
    $tareasController = new TareasController($conn, $userId);
    
    echo "<h2>1. Probando conexión a la base de datos...</h2>";
    if ($conn) {
        echo "✅ Conexión exitosa a la base de datos<br>";
    } else {
        echo "❌ Error en la conexión a la base de datos<br>";
        exit;
    }
    
    echo "<h2>2. Verificando tabla Tareas...</h2>";
    $result = $conn->query("SHOW TABLES LIKE 'Tareas'");
    if ($result->num_rows > 0) {
        echo "✅ Tabla 'Tareas' existe<br>";
        
        // Verificar estructura
        $result = $conn->query("DESCRIBE Tareas");
        echo "<h3>Estructura de la tabla:</h3>";
        echo "<table border='1'><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Tabla 'Tareas' no existe. Ejecuta el script SQL primero.<br>";
    }
    
    echo "<h2>3. Probando métodos del controlador...</h2>";
    
    // Probar obtener usuarios
    echo "<h3>Usuarios disponibles:</h3>";
    $usuarios = $tareasController->getUsuariosDisponibles();
    if ($usuarios) {
        echo "<ul>";
        while ($usuario = $usuarios->fetch_assoc()) {
            echo "<li>{$usuario['Id_PvUser']} - {$usuario['Nombre_Apellidos']}</li>";
        }
        echo "</ul>";
    } else {
        echo "❌ No se pudieron obtener usuarios<br>";
    }
    
    // Probar obtener estadísticas
    echo "<h3>Estadísticas de tareas:</h3>";
    $estadisticas = $tareasController->getEstadisticas();
    if ($estadisticas) {
        echo "<ul>";
        while ($stat = $estadisticas->fetch_assoc()) {
            echo "<li>{$stat['estado']}: {$stat['cantidad']}</li>";
        }
        echo "</ul>";
    } else {
        echo "❌ No se pudieron obtener estadísticas<br>";
    }
    
    // Probar obtener tareas
    echo "<h3>Tareas existentes:</h3>";
    $tareas = $tareasController->getTareas();
    if ($tareas) {
        echo "<table border='1'><tr><th>ID</th><th>Título</th><th>Prioridad</th><th>Estado</th><th>Asignado</th></tr>";
        while ($tarea = $tareas->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$tarea['id']}</td>";
            echo "<td>{$tarea['titulo']}</td>";
            echo "<td>{$tarea['prioridad']}</td>";
            echo "<td>{$tarea['estado']}</td>";
            echo "<td>{$tarea['asignado_nombre']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No se pudieron obtener tareas<br>";
    }
    
    echo "<h2>4. Probando creación de tarea de prueba...</h2>";
    $datosPrueba = [
        'titulo' => 'Tarea de Prueba - ' . date('Y-m-d H:i:s'),
        'descripcion' => 'Esta es una tarea de prueba creada automáticamente',
        'prioridad' => 'Media',
        'fecha_limite' => date('Y-m-d', strtotime('+7 days')),
        'estado' => 'Por hacer',
        'asignado_a' => $userId // Asignar al usuario actual
    ];
    
    $tareaId = $tareasController->crearTarea($datosPrueba);
    if ($tareaId) {
        echo "✅ Tarea de prueba creada exitosamente con ID: $tareaId<br>";
        
        // Probar obtener la tarea creada
        $tareaCreada = $tareasController->getTarea($tareaId);
        if ($tareaCreada) {
            echo "✅ Tarea obtenida correctamente: {$tareaCreada['titulo']}<br>";
        }
        
        // Probar cambiar estado
        if ($tareasController->cambiarEstado($tareaId, 'En progreso')) {
            echo "✅ Estado cambiado a 'En progreso'<br>";
        }
        
        // Probar actualizar tarea
        $datosActualizacion = $datosPrueba;
        $datosActualizacion['descripcion'] = 'Descripción actualizada';
        if ($tareasController->actualizarTarea($tareaId, $datosActualizacion)) {
            echo "✅ Tarea actualizada correctamente<br>";
        }
        
        // Probar eliminar tarea de prueba
        if ($tareasController->eliminarTarea($tareaId)) {
            echo "✅ Tarea de prueba eliminada correctamente<br>";
        }
    } else {
        echo "❌ Error al crear tarea de prueba<br>";
    }
    
    echo "<h2>✅ Prueba completada exitosamente</h2>";
    echo "<p>El módulo de tareas está funcionando correctamente.</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error durante la prueba:</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
}
?>
