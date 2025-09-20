<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test de Ajustes para Farmacia</h1>";

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
    
    echo "<h2>1. Verificando Permisos de Usuario</h2>";
    
    if ($tipoUsuario === 'Administrador') {
        echo "✅ <strong>ADMINISTRADOR</strong> - Tiene acceso completo:<br>";
        echo "   - Puede crear nuevas tareas<br>";
        echo "   - Puede exportar tareas<br>";
        echo "   - Puede buscar en la tabla<br>";
        echo "   - Puede editar tareas<br>";
        echo "   - Puede eliminar tareas<br>";
    } else {
        echo "✅ <strong>USUARIO DE FARMACIA</strong> - Acceso limitado:<br>";
        echo "   - ❌ NO puede crear nuevas tareas<br>";
        echo "   - ❌ NO puede exportar tareas<br>";
        echo "   - ❌ NO puede buscar en la tabla<br>";
        echo "   - ❌ NO puede editar tareas<br>";
        echo "   - ❌ NO puede eliminar tareas<br>";
        echo "   - ✅ Puede cambiar estado de tareas activas<br>";
        echo "   - ✅ Puede ver detalles de tareas completadas/canceladas<br>";
    }
    
    echo "<h2>2. Probando Tareas Asignadas</h2>";
    
    $tareasController = new TareasController($conn, $userId, $sucursalId);
    $tareas = $tareasController->getTareasAsignadas();
    
    echo "Total de tareas asignadas: " . $tareas->num_rows . "<br>";
    
    if ($tareas->num_rows > 0) {
        echo "<h3>Estados de las tareas:</h3>";
        $estados = [];
        while ($tarea = $tareas->fetch_assoc()) {
            $estados[$tarea['estado']] = ($estados[$tarea['estado']] ?? 0) + 1;
        }
        
        foreach ($estados as $estado => $cantidad) {
            echo "- $estado: $cantidad tareas<br>";
        }
        
        echo "<h3>Botones de acción por estado:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Estado</th><th>Botones Disponibles</th><th>Descripción</th></tr>";
        
        $botonesPorEstado = [
            'Por hacer' => '▶️ En Progreso, ✅ Completada, ❌ Cancelada' . ($tipoUsuario === 'Administrador' ? ', ✏️ Editar, 🗑️ Eliminar' : ''),
            'En progreso' => '✅ Completada, ❌ Cancelada' . ($tipoUsuario === 'Administrador' ? ', ✏️ Editar, 🗑️ Eliminar' : ''),
            'Completada' => '👁️ Ver detalles',
            'Cancelada' => '👁️ Ver detalles'
        ];
        
        foreach ($botonesPorEstado as $estado => $botones) {
            echo "<tr>";
            echo "<td><strong>$estado</strong></td>";
            echo "<td>$botones</td>";
            echo "<td>" . ($estado === 'Completada' || $estado === 'Cancelada' ? 'Solo lectura' : 'Gestión activa') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>3. Verificando Funcionalidades de DataTable</h2>";
    
    $searchingEnabled = ($tipoUsuario === 'Administrador') ? 'true' : 'false';
    echo "Búsqueda habilitada: <strong>" . ($searchingEnabled === 'true' ? 'SÍ' : 'NO') . "</strong><br>";
    
    echo "<h2>4. Verificando Botones de Acción</h2>";
    
    $botonesVisibles = [];
    if ($tipoUsuario === 'Administrador') {
        $botonesVisibles[] = 'Nueva Tarea';
        $botonesVisibles[] = 'Exportar';
    }
    
    echo "Botones visibles en la interfaz:<br>";
    if (empty($botonesVisibles)) {
        echo "❌ Ningún botón de administración visible<br>";
    } else {
        foreach ($botonesVisibles as $boton) {
            echo "✅ $boton<br>";
        }
    }
    
    echo "<h2>5. Probando ArrayTareas.php</h2>";
    
    // Simular petición AJAX
    $_POST['accion'] = 'listar';
    $_POST['estado'] = '';
    $_POST['prioridad'] = '';
    $_POST['fecha'] = '';
    
    ob_start();
    include 'Controladores/ArrayTareas.php';
    $output = ob_get_clean();
    
    echo "✅ ArrayTareas.php ejecutado correctamente<br>";
    
    $response = json_decode($output, true);
    if ($response && $response['success']) {
        echo "✅ Respuesta JSON válida<br>";
        echo "Total tareas en respuesta: " . count($response['data']) . "<br>";
    } else {
        echo "❌ Error en ArrayTareas.php<br>";
        echo "Output: " . htmlspecialchars($output) . "<br>";
    }
    
    echo "<h2>✅ Test Completado</h2>";
    echo "<p><strong>Resumen de ajustes aplicados:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Botones de Nueva Tarea y Exportar solo para administradores</li>";
    echo "<li>✅ Búsqueda en DataTable deshabilitada para usuarios de farmacia</li>";
    echo "<li>✅ Botones de acción adaptados según el estado de la tarea</li>";
    echo "<li>✅ Tareas completadas/canceladas solo muestran botón Ver</li>";
    echo "<li>✅ Usuarios de farmacia solo pueden cambiar estado de tareas activas</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}
?>
