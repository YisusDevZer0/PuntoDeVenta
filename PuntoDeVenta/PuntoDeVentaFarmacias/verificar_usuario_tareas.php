<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Verificación de Usuario y Tareas</h1>";

try {
    // Incluir archivos necesarios
    include_once "Controladores/ControladorUsuario.php";
    include_once "Controladores/db_connect.php";
    
    $userId = $row['Id_PvUser'];
    $sucursalId = $row['Fk_Sucursal'];
    
    echo "<h2>Datos del Usuario Actual</h2>";
    echo "Usuario ID: $userId<br>";
    echo "Sucursal ID: $sucursalId<br>";
    echo "Nombre: " . $row['Nombre_Apellidos'] . "<br>";
    
    echo "<h2>1. Verificando tareas asignadas al usuario actual (ID: $userId)</h2>";
    
    $sql = "SELECT * FROM Tareas WHERE asignado_a = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Tareas asignadas al usuario $userId: " . $result->num_rows . "<br>";
    
    if ($result->num_rows > 0) {
        echo "<h3>Tareas del usuario actual:</h3>";
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
        echo "❌ No hay tareas asignadas al usuario actual<br>";
    }
    
    echo "<h2>2. Verificando todas las tareas en la base de datos</h2>";
    
    $sql = "SELECT t.*, u_asignado.Nombre_Apellidos as asignado_nombre, u_creador.Nombre_Apellidos as creador_nombre 
            FROM Tareas t
            LEFT JOIN Usuarios_PV u_asignado ON t.asignado_a = u_asignado.Id_PvUser
            LEFT JOIN Usuarios_PV u_creador ON t.creado_por = u_creador.Id_PvUser
            ORDER BY t.id";
    $result = $conn->query($sql);
    
    echo "Total de tareas en la base de datos: " . $result->num_rows . "<br>";
    
    if ($result->num_rows > 0) {
        echo "<h3>Todas las tareas:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Asignado a</th><th>Nombre Asignado</th><th>Creado por</th><th>Nombre Creador</th></tr>";
        
        while ($tarea = $result->fetch_assoc()) {
            $highlight = ($tarea['asignado_a'] == $userId) ? 'style="background-color: #90EE90;"' : '';
            echo "<tr $highlight>";
            echo "<td>" . $tarea['id'] . "</td>";
            echo "<td>" . htmlspecialchars($tarea['titulo']) . "</td>";
            echo "<td>" . $tarea['estado'] . "</td>";
            echo "<td>" . $tarea['asignado_a'] . "</td>";
            echo "<td>" . ($tarea['asignado_nombre'] ?? 'NULL') . "</td>";
            echo "<td>" . $tarea['creado_por'] . "</td>";
            echo "<td>" . ($tarea['creador_nombre'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><small>Las filas resaltadas en verde son las tareas asignadas al usuario actual.</small></p>";
    }
    
    echo "<h2>3. Verificando usuarios en la base de datos</h2>";
    
    $sql = "SELECT Id_PvUser, Nombre_Apellidos, Fk_Sucursal FROM Usuarios_PV ORDER BY Id_PvUser";
    $result = $conn->query($sql);
    
    echo "<h3>Usuarios disponibles:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Sucursal</th><th>Es Actual</th></tr>";
    
    while ($usuario = $result->fetch_assoc()) {
        $esActual = ($usuario['Id_PvUser'] == $userId) ? '✅ SÍ' : '❌ No';
        $highlight = ($usuario['Id_PvUser'] == $userId) ? 'style="background-color: #90EE90;"' : '';
        echo "<tr $highlight>";
        echo "<td>" . $usuario['Id_PvUser'] . "</td>";
        echo "<td>" . $usuario['Nombre_Apellidos'] . "</td>";
        echo "<td>" . $usuario['Fk_Sucursal'] . "</td>";
        echo "<td>$esActual</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>4. ¿Quieres asignar tareas al usuario actual?</h2>";
    
    if ($result->num_rows == 0) {
        echo "<p>No hay tareas asignadas al usuario actual. ¿Quieres crear algunas?</p>";
        echo "<a href='?crear_tareas=1' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Crear Tareas para Usuario Actual</a><br>";
    }
    
    if (isset($_GET['crear_tareas'])) {
        echo "<h3>Creando tareas para el usuario actual...</h3>";
        
        $tareas_nuevas = [
            [
                'titulo' => 'Revisar inventario de medicamentos',
                'descripcion' => 'Verificar que todos los medicamentos estén correctamente etiquetados y con fechas válidas.',
                'prioridad' => 'Alta',
                'fecha_limite' => date('Y-m-d', strtotime('+3 days')),
                'estado' => 'Por hacer'
            ],
            [
                'titulo' => 'Actualizar precios de productos',
                'descripcion' => 'Revisar y actualizar los precios según la lista más reciente del proveedor.',
                'prioridad' => 'Media',
                'fecha_limite' => date('Y-m-d', strtotime('+7 days')),
                'estado' => 'En progreso'
            ],
            [
                'titulo' => 'Limpieza general de la farmacia',
                'descripcion' => 'Realizar limpieza profunda de estantes, vitrinas y área de trabajo.',
                'prioridad' => 'Baja',
                'fecha_limite' => date('Y-m-d', strtotime('+14 days')),
                'estado' => 'Por hacer'
            ]
        ];
        
        $tareas_creadas = 0;
        
        foreach ($tareas_nuevas as $tarea) {
            $sql = "INSERT INTO Tareas (titulo, descripcion, prioridad, fecha_limite, estado, asignado_a, creado_por) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", 
                $tarea['titulo'],
                $tarea['descripcion'],
                $tarea['prioridad'],
                $tarea['fecha_limite'],
                $tarea['estado'],
                $userId,
                $userId
            );
            
            if ($stmt->execute()) {
                echo "✅ Tarea creada: " . $tarea['titulo'] . "<br>";
                $tareas_creadas++;
            } else {
                echo "❌ Error creando tarea: " . $tarea['titulo'] . "<br>";
            }
        }
        
        if ($tareas_creadas > 0) {
            echo "<h3>✅ Se crearon $tareas_creadas tareas para el usuario actual</h3>";
            echo "<a href='TareasPorHacer.php'>Ir a TareasPorHacer.php</a><br>";
        }
    }
    
    echo "<h2>✅ Verificación Completada</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}
?>
