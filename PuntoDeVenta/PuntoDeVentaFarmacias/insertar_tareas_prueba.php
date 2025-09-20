<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>📝 Insertar Tareas de Prueba</h1>";

try {
    // Incluir archivos necesarios
    include_once "Controladores/ControladorUsuario.php";
    include_once "Controladores/db_connect.php";
    
    $userId = $row['Id_PvUser'];
    $sucursalId = $row['Fk_Sucursal'];
    
    echo "<h2>Datos del Usuario</h2>";
    echo "Usuario ID: $userId<br>";
    echo "Sucursal ID: $sucursalId<br>";
    
    echo "<h2>1. Verificando si ya existen tareas</h2>";
    
    $sql = "SELECT COUNT(*) as total FROM Tareas WHERE asignado_a = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();
    
    echo "Tareas existentes para el usuario: " . $count['total'] . "<br>";
    
    if ($count['total'] > 0) {
        echo "✅ Ya existen tareas para este usuario<br>";
        echo "<a href='test_tareas_simple.php'>Ver tareas existentes</a><br>";
    } else {
        echo "❌ No hay tareas. Creando tareas de prueba...<br>";
        
        $tareas_prueba = [
            [
                'titulo' => 'Revisar inventario de medicamentos',
                'descripcion' => 'Verificar que todos los medicamentos estén correctamente etiquetados y con fechas válidas. Revisar especialmente los medicamentos próximos a vencer.',
                'prioridad' => 'Alta',
                'fecha_limite' => date('Y-m-d', strtotime('+3 days')),
                'estado' => 'Por hacer',
                'asignado_a' => $userId
            ],
            [
                'titulo' => 'Actualizar precios de productos',
                'descripcion' => 'Revisar y actualizar los precios según la lista más reciente del proveedor. Incluir descuentos y promociones vigentes.',
                'prioridad' => 'Media',
                'fecha_limite' => date('Y-m-d', strtotime('+7 days')),
                'estado' => 'En progreso',
                'asignado_a' => $userId
            ],
            [
                'titulo' => 'Limpieza general de la farmacia',
                'descripcion' => 'Realizar limpieza profunda de estantes, vitrinas y área de trabajo. Organizar productos por categorías.',
                'prioridad' => 'Baja',
                'fecha_limite' => date('Y-m-d', strtotime('+14 days')),
                'estado' => 'Por hacer',
                'asignado_a' => $userId
            ],
            [
                'titulo' => 'Verificar recetas médicas pendientes',
                'descripcion' => 'Revisar las recetas médicas que están pendientes de entrega y contactar a los pacientes.',
                'prioridad' => 'Alta',
                'fecha_limite' => date('Y-m-d', strtotime('+1 day')),
                'estado' => 'Por hacer',
                'asignado_a' => $userId
            ],
            [
                'titulo' => 'Actualizar base de datos de clientes',
                'descripcion' => 'Verificar y actualizar la información de contacto de los clientes frecuentes.',
                'prioridad' => 'Media',
                'fecha_limite' => date('Y-m-d', strtotime('+10 days')),
                'estado' => 'Completada',
                'asignado_a' => $userId
            ]
        ];
        
        $tareas_creadas = 0;
        $tareas_con_error = 0;
        
        foreach ($tareas_prueba as $tarea) {
            $sql = "INSERT INTO Tareas (titulo, descripcion, prioridad, fecha_limite, estado, asignado_a, creado_por) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", 
                $tarea['titulo'],
                $tarea['descripcion'],
                $tarea['prioridad'],
                $tarea['fecha_limite'],
                $tarea['estado'],
                $tarea['asignado_a'],
                $userId
            );
            
            if ($stmt->execute()) {
                echo "✅ Tarea creada: " . $tarea['titulo'] . "<br>";
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
            echo "<h3>✅ Tareas de prueba creadas exitosamente</h3>";
            echo "<a href='test_tareas_simple.php'>Ver las tareas creadas</a><br>";
            echo "<a href='TareasPorHacer.php'>Ir a TareasPorHacer.php</a><br>";
        }
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
        echo "Esto puede causar problemas con los JOINs en las consultas<br>";
    }
    
    echo "<h2>✅ Proceso Completado</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}
?>
