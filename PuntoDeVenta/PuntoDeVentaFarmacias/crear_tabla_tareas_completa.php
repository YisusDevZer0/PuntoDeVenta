<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Creación/Verificación de Tabla Tareas</h1>";

try {
    // Incluir archivos necesarios
    include_once "Controladores/ControladorUsuario.php";
    include_once "Controladores/db_connect.php";
    
    echo "<h2>1. Verificando conexión a base de datos</h2>";
    
    if (!isset($conn) || !$conn) {
        throw new Exception("No hay conexión a la base de datos");
    }
    
    echo "✅ Conexión a base de datos establecida<br>";
    
    echo "<h2>2. Verificando si existe la tabla Tareas</h2>";
    
    $sql = "SHOW TABLES LIKE 'Tareas'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "✅ La tabla 'Tareas' ya existe<br>";
        
        // Mostrar estructura actual
        echo "<h3>Estructura actual de la tabla:</h3>";
        $sql = "DESCRIBE Tareas";
        $result = $conn->query($sql);
        
        if ($result) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            while ($row_table = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row_table['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row_table['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row_table['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row_table['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row_table['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row_table['Extra']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "❌ La tabla 'Tareas' NO existe. Creándola...<br>";
        
        // Script para crear la tabla
        $sql = "CREATE TABLE Tareas (
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
            INDEX idx_creado_por (creado_por),
            INDEX idx_estado (estado),
            INDEX idx_prioridad (prioridad),
            INDEX idx_fecha_limite (fecha_limite)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($sql) === TRUE) {
            echo "✅ Tabla 'Tareas' creada exitosamente<br>";
        } else {
            throw new Exception("Error creando tabla: " . $conn->error);
        }
    }
    
    echo "<h2>3. Verificando datos de prueba</h2>";
    
    // Contar tareas existentes
    $sql = "SELECT COUNT(*) as total FROM Tareas";
    $result = $conn->query($sql);
    $count = $result->fetch_assoc();
    
    echo "Total de tareas en la base de datos: " . $count['total'] . "<br>";
    
    if ($count['total'] == 0) {
        echo "No hay tareas. ¿Deseas crear algunas de prueba?<br>";
        echo "<a href='?crear_datos_prueba=1'>Sí, crear datos de prueba</a><br>";
        
        if (isset($_GET['crear_datos_prueba'])) {
            echo "<h3>Creando datos de prueba...</h3>";
            
            // Obtener usuario actual para crear tareas de prueba
            if (isset($row) && isset($row['Id_PvUser'])) {
                $userId = $row['Id_PvUser'];
                
                $tareas_prueba = [
                    [
                        'titulo' => 'Revisar inventario de medicamentos',
                        'descripcion' => 'Verificar que todos los medicamentos estén correctamente etiquetados y con fechas válidas',
                        'prioridad' => 'Alta',
                        'fecha_limite' => date('Y-m-d', strtotime('+3 days')),
                        'estado' => 'Por hacer',
                        'asignado_a' => $userId
                    ],
                    [
                        'titulo' => 'Actualizar precios de productos',
                        'descripcion' => 'Revisar y actualizar los precios según la lista más reciente del proveedor',
                        'prioridad' => 'Media',
                        'fecha_limite' => date('Y-m-d', strtotime('+7 days')),
                        'estado' => 'En progreso',
                        'asignado_a' => $userId
                    ],
                    [
                        'titulo' => 'Limpieza general de la farmacia',
                        'descripcion' => 'Realizar limpieza profunda de estantes y vitrinas',
                        'prioridad' => 'Baja',
                        'fecha_limite' => date('Y-m-d', strtotime('+14 days')),
                        'estado' => 'Por hacer',
                        'asignado_a' => $userId
                    ]
                ];
                
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
                    } else {
                        echo "❌ Error creando tarea: " . $tarea['titulo'] . "<br>";
                    }
                }
            } else {
                echo "❌ No se puede crear datos de prueba - usuario no identificado<br>";
            }
        }
    }
    
    echo "<h2>4. Verificando integridad de la tabla</h2>";
    
    // Verificar que la tabla tiene la estructura correcta
    $campos_requeridos = ['id', 'titulo', 'descripcion', 'prioridad', 'fecha_limite', 'estado', 'asignado_a', 'creado_por', 'fecha_creacion', 'fecha_actualizacion'];
    
    $sql = "DESCRIBE Tareas";
    $result = $conn->query($sql);
    $campos_existentes = [];
    
    while ($row_table = $result->fetch_assoc()) {
        $campos_existentes[] = $row_table['Field'];
    }
    
    $campos_faltantes = array_diff($campos_requeridos, $campos_existentes);
    
    if (empty($campos_faltantes)) {
        echo "✅ Todos los campos requeridos están presentes<br>";
    } else {
        echo "❌ Faltan campos: " . implode(', ', $campos_faltantes) . "<br>";
    }
    
    echo "<h2>✅ Verificación Completada</h2>";
    echo "<p>La tabla Tareas está lista para usar.</p>";
    echo "<p><a href='TareasPorHacer.php'>Ir a TareasPorHacer.php</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}
?>
