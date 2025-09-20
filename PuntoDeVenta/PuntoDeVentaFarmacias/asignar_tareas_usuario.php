<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>📋 Asignar Tareas al Usuario Actual</h1>";

try {
    // Incluir archivos necesarios
    include_once "Controladores/ControladorUsuario.php";
    include_once "Controladores/db_connect.php";
    
    $userId = $row['Id_PvUser'];
    $sucursalId = $row['Fk_Sucursal'];
    
    echo "<h2>Datos del Usuario</h2>";
    echo "Usuario ID: $userId<br>";
    echo "Sucursal ID: $sucursalId<br>";
    echo "Nombre: " . $row['Nombre_Apellidos'] . "<br>";
    
    echo "<h2>1. Verificando tareas existentes</h2>";
    
    $sql = "SELECT COUNT(*) as total FROM Tareas WHERE asignado_a = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();
    
    echo "Tareas actuales del usuario: " . $count['total'] . "<br>";
    
    if ($count['total'] > 0) {
        echo "<h3>Tareas existentes:</h3>";
        $sql = "SELECT * FROM Tareas WHERE asignado_a = ? ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Prioridad</th><th>Fecha Límite</th></tr>";
        
        while ($tarea = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $tarea['id'] . "</td>";
            echo "<td>" . htmlspecialchars($tarea['titulo']) . "</td>";
            echo "<td>" . $tarea['estado'] . "</td>";
            echo "<td>" . $tarea['prioridad'] . "</td>";
            echo "<td>" . $tarea['fecha_limite'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>2. Creando tareas para el usuario actual</h2>";
    
    $tareas_nuevas = [
        [
            'titulo' => 'Revisar inventario de medicamentos',
            'descripcion' => 'Verificar que todos los medicamentos estén correctamente etiquetados y con fechas válidas. Revisar especialmente los medicamentos próximos a vencer.',
            'prioridad' => 'Alta',
            'fecha_limite' => date('Y-m-d', strtotime('+2 days')),
            'estado' => 'Por hacer'
        ],
        [
            'titulo' => 'Actualizar precios de productos',
            'descripcion' => 'Revisar y actualizar los precios según la lista más reciente del proveedor. Incluir descuentos y promociones vigentes.',
            'prioridad' => 'Media',
            'fecha_limite' => date('Y-m-d', strtotime('+5 days')),
            'estado' => 'En progreso'
        ],
        [
            'titulo' => 'Limpieza general de la farmacia',
            'descripcion' => 'Realizar limpieza profunda de estantes, vitrinas y área de trabajo. Organizar productos por categorías.',
            'prioridad' => 'Baja',
            'fecha_limite' => date('Y-m-d', strtotime('+10 days')),
            'estado' => 'Por hacer'
        ],
        [
            'titulo' => 'Verificar recetas médicas pendientes',
            'descripcion' => 'Revisar las recetas médicas que están pendientes de entrega y contactar a los pacientes.',
            'prioridad' => 'Alta',
            'fecha_limite' => date('Y-m-d', strtotime('+1 day')),
            'estado' => 'Por hacer'
        ],
        [
            'titulo' => 'Actualizar base de datos de clientes',
            'descripcion' => 'Verificar y actualizar la información de contacto de los clientes frecuentes.',
            'prioridad' => 'Media',
            'fecha_limite' => date('Y-m-d', strtotime('-1 day')), // Vencida
            'estado' => 'Por hacer'
        ],
        [
            'titulo' => 'Revisar caja registradora',
            'descripcion' => 'Verificar que la caja registradora esté funcionando correctamente y que tenga suficiente cambio.',
            'prioridad' => 'Alta',
            'fecha_limite' => date('Y-m-d'), // Hoy
            'estado' => 'Por hacer'
        ]
    ];
    
    $tareas_creadas = 0;
    $tareas_con_error = 0;
    
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
            echo "✅ Tarea creada: " . $tarea['titulo'] . " (Vence: " . $tarea['fecha_limite'] . ")<br>";
            $tareas_creadas++;
        } else {
            echo "❌ Error creando tarea: " . $tarea['titulo'] . " - " . $stmt->error . "<br>";
            $tareas_con_error++;
        }
    }
    
    echo "<h3>Resumen:</h3>";
    echo "Tareas creadas: $tareas_creadas<br>";
    echo "Tareas con error: $tareas_con_error<br>";
    
    if ($tareas_creadas > 0) {
        echo "<h3>✅ Tareas creadas exitosamente</h3>";
        echo "<p>Ahora puedes ir a <a href='TareasPorHacer.php' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>TareasPorHacer.php</a> para ver tus tareas.</p>";
        
        echo "<h3>Tipos de tareas creadas:</h3>";
        echo "<ul>";
        echo "<li><strong>Alta prioridad:</strong> Revisar inventario, Verificar recetas, Revisar caja</li>";
        echo "<li><strong>Media prioridad:</strong> Actualizar precios, Actualizar base de datos</li>";
        echo "<li><strong>Baja prioridad:</strong> Limpieza general</li>";
        echo "<li><strong>Vencida:</strong> Actualizar base de datos (para probar filtros)</li>";
        echo "<li><strong>Hoy:</strong> Revisar caja registradora (para probar filtros)</li>";
        echo "</ul>";
    }
    
    echo "<h2>3. Verificando el sistema</h2>";
    echo "<p>Puedes probar los siguientes filtros en TareasPorHacer.php:</p>";
    echo "<ul>";
    echo "<li><strong>Estado:</strong> Por hacer, En progreso, Completada, Cancelada</li>";
    echo "<li><strong>Prioridad:</strong> Alta, Media, Baja</li>";
    echo "<li><strong>Fecha:</strong> Hoy, Vencidas, Próximas (3 días)</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}
?>
