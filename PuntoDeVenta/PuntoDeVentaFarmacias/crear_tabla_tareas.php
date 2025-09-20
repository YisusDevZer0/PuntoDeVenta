<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/db_connect.php";

echo "<h2>Creando tabla de tareas...</h2>";

// Script para crear la tabla tareas
$sql = "CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    prioridad ENUM('Alta', 'Media', 'Baja') DEFAULT 'Media',
    fecha_limite DATE,
    estado ENUM('Por hacer', 'En progreso', 'Completada', 'Cancelada') DEFAULT 'Por hacer',
    asignado_a INT NOT NULL,
    creado_por INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_asignado_a (asignado_a),
    INDEX idx_estado (estado),
    INDEX idx_prioridad (prioridad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "✅ Tabla 'tareas' creada exitosamente<br>";
    
    // Insertar algunas tareas de ejemplo
    $tareas_ejemplo = [
        [
            'titulo' => 'Revisar inventario de medicamentos',
            'descripcion' => 'Verificar que todos los medicamentos estén correctamente etiquetados y con fechas de vencimiento válidas',
            'prioridad' => 'Alta',
            'fecha_limite' => date('Y-m-d', strtotime('+3 days')),
            'estado' => 'Por hacer',
            'asignado_a' => $row['Id_PvUser'],
            'creado_por' => $row['Id_PvUser']
        ],
        [
            'titulo' => 'Actualizar precios de productos',
            'descripcion' => 'Revisar y actualizar los precios de los productos según la lista más reciente',
            'prioridad' => 'Media',
            'fecha_limite' => date('Y-m-d', strtotime('+1 week')),
            'estado' => 'En progreso',
            'asignado_a' => $row['Id_PvUser'],
            'creado_por' => $row['Id_PvUser']
        ],
        [
            'titulo' => 'Limpieza de área de ventas',
            'descripcion' => 'Realizar limpieza profunda del área de ventas y reorganizar productos',
            'prioridad' => 'Baja',
            'fecha_limite' => date('Y-m-d', strtotime('+2 weeks')),
            'estado' => 'Por hacer',
            'asignado_a' => $row['Id_PvUser'],
            'creado_por' => $row['Id_PvUser']
        ]
    ];
    
    echo "<h3>Insertando tareas de ejemplo...</h3>";
    
    foreach ($tareas_ejemplo as $tarea) {
        $sql = "INSERT INTO tareas (titulo, descripcion, prioridad, fecha_limite, estado, asignado_a, creado_por) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssii", 
                $tarea['titulo'], 
                $tarea['descripcion'], 
                $tarea['prioridad'], 
                $tarea['fecha_limite'], 
                $tarea['estado'], 
                $tarea['asignado_a'], 
                $tarea['creado_por']
            );
            
            if (mysqli_stmt_execute($stmt)) {
                echo "✅ Tarea insertada: " . $tarea['titulo'] . "<br>";
            } else {
                echo "❌ Error al insertar tarea: " . mysqli_error($conn) . "<br>";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "❌ Error al preparar statement: " . mysqli_error($conn) . "<br>";
        }
    }
    
    echo "<br><a href='TareasPorHacer.php' class='btn btn-primary'>Ver Mis Tareas</a>";
    
} else {
    echo "❌ Error al crear la tabla: " . mysqli_error($conn) . "<br>";
}

// Verificar que la tabla se creó correctamente
$sql = "SELECT COUNT(*) as total FROM tareas";
$result = mysqli_query($conn, $sql);
if ($result) {
    $count = mysqli_fetch_assoc($result);
    echo "<br>Total de tareas en la base de datos: " . $count['total'] . "<br>";
}
?>
