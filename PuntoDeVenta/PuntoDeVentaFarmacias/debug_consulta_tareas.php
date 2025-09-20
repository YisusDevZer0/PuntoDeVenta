<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debug de Consulta de Tareas</h1>";

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
    
    echo "<h2>1. Verificando si existen tareas asignadas al usuario</h2>";
    
    $sql = "SELECT COUNT(*) as total FROM Tareas WHERE asignado_a = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();
    
    echo "Tareas asignadas al usuario $userId: " . $count['total'] . "<br>";
    
    if ($count['total'] > 0) {
        echo "<h3>Detalles de las tareas asignadas:</h3>";
        
        $sql = "SELECT * FROM Tareas WHERE asignado_a = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
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
    }
    
    echo "<h2>2. Verificando datos del usuario en Usuarios_PV</h2>";
    
    $sql = "SELECT Id_PvUser, Nombre_Apellidos, Fk_Sucursal FROM Usuarios_PV WHERE Id_PvUser = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    
    if ($usuario) {
        echo "✅ Usuario encontrado en Usuarios_PV:<br>";
        echo "ID: " . $usuario['Id_PvUser'] . "<br>";
        echo "Nombre: " . $usuario['Nombre_Apellidos'] . "<br>";
        echo "Sucursal: " . $usuario['Fk_Sucursal'] . "<br>";
    } else {
        echo "❌ Usuario NO encontrado en Usuarios_PV<br>";
    }
    
    echo "<h2>3. Probando la consulta con JOIN</h2>";
    
    $sql = "SELECT 
                t.id,
                t.titulo,
                t.estado,
                t.asignado_a,
                u_asignado.Nombre_Apellidos as asignado_nombre,
                u_asignado.Fk_Sucursal as sucursal_asignado
            FROM Tareas t
            LEFT JOIN Usuarios_PV u_asignado ON t.asignado_a = u_asignado.Id_PvUser
            WHERE t.asignado_a = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Tareas con JOIN (sin filtro de sucursal): " . $result->num_rows . "<br>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Asignado a</th><th>Nombre Asignado</th><th>Sucursal Asignado</th></tr>";
        
        while ($tarea = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $tarea['id'] . "</td>";
            echo "<td>" . htmlspecialchars($tarea['titulo']) . "</td>";
            echo "<td>" . $tarea['estado'] . "</td>";
            echo "<td>" . $tarea['asignado_a'] . "</td>";
            echo "<td>" . ($tarea['asignado_nombre'] ?? 'NULL') . "</td>";
            echo "<td>" . ($tarea['sucursal_asignado'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>4. Probando la consulta completa con filtro de sucursal</h2>";
    
    $sql = "SELECT 
                t.id,
                t.titulo,
                t.estado,
                t.asignado_a,
                u_asignado.Nombre_Apellidos as asignado_nombre,
                u_asignado.Fk_Sucursal as sucursal_asignado
            FROM Tareas t
            LEFT JOIN Usuarios_PV u_asignado ON t.asignado_a = u_asignado.Id_PvUser
            WHERE t.asignado_a = ? AND u_asignado.Fk_Sucursal = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $sucursalId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Tareas con JOIN y filtro de sucursal: " . $result->num_rows . "<br>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Asignado a</th><th>Nombre Asignado</th><th>Sucursal Asignado</th></tr>";
        
        while ($tarea = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $tarea['id'] . "</td>";
            echo "<td>" . htmlspecialchars($tarea['titulo']) . "</td>";
            echo "<td>" . $tarea['estado'] . "</td>";
            echo "<td>" . $tarea['asignado_a'] . "</td>";
            echo "<td>" . ($tarea['asignado_nombre'] ?? 'NULL') . "</td>";
            echo "<td>" . ($tarea['sucursal_asignado'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No se encontraron tareas con el filtro de sucursal<br>";
        echo "Esto puede indicar que:<br>";
        echo "1. El usuario asignado a las tareas no tiene la sucursal correcta<br>";
        echo "2. Hay un problema con el JOIN<br>";
        echo "3. Los datos de sucursal no coinciden<br>";
    }
    
    echo "<h2>5. Verificando todos los usuarios en la sucursal</h2>";
    
    $sql = "SELECT Id_PvUser, Nombre_Apellidos, Fk_Sucursal FROM Usuarios_PV WHERE Fk_Sucursal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sucursalId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Usuarios en la sucursal $sucursalId: " . $result->num_rows . "<br>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Sucursal</th></tr>";
        
        while ($usuario = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $usuario['Id_PvUser'] . "</td>";
            echo "<td>" . $usuario['Nombre_Apellidos'] . "</td>";
            echo "<td>" . $usuario['Fk_Sucursal'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>6. Probando TareasController</h2>";
    
    $tareasController = new TareasController($conn, $userId, $sucursalId);
    
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
    
    try {
        $tareas = $tareasController->getTareasAsignadas();
        echo "✅ getTareasAsignadas() ejecutado<br>";
        echo "Tareas encontradas: " . $tareas->num_rows . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Error en getTareasAsignadas(): " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}
?>
