<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>🔍 Diagnóstico del Sistema de Tareas</h1>";
echo "<hr>";

// 1. Verificar archivos necesarios
echo "<h2>1. Verificación de Archivos</h2>";

$archivos_requeridos = [
    'Controladores/ControladorUsuario.php',
    'Controladores/TareasController.php',
    'Controladores/ArrayTareas.php',
    'Controladores/exportar_tareas.php',
    'Controladores/db_connect.php'
];

foreach ($archivos_requeridos as $archivo) {
    if (file_exists($archivo)) {
        echo "✅ $archivo - Existe<br>";
    } else {
        echo "❌ $archivo - NO EXISTE<br>";
    }
}

echo "<hr>";

// 2. Verificar includes
echo "<h2>2. Verificación de Includes</h2>";

try {
    if (file_exists('Controladores/ControladorUsuario.php')) {
        include_once 'Controladores/ControladorUsuario.php';
        echo "✅ ControladorUsuario.php incluido correctamente<br>";
        
        // Verificar variables del usuario
        if (isset($row)) {
            echo "✅ Variable \$row disponible<br>";
            echo "   - Usuario ID: " . (isset($row['Id_PvUser']) ? $row['Id_PvUser'] : 'NO DEFINIDO') . "<br>";
            echo "   - Sucursal ID: " . (isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 'NO DEFINIDO') . "<br>";
            echo "   - Nombre: " . (isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : 'NO DEFINIDO') . "<br>";
        } else {
            echo "❌ Variable \$row NO disponible<br>";
        }
        
        if (isset($userId)) {
            echo "✅ Variable \$userId disponible: $userId<br>";
        } else {
            echo "❌ Variable \$userId NO disponible<br>";
        }
        
        if (isset($sucursalId)) {
            echo "✅ Variable \$sucursalId disponible: $sucursalId<br>";
        } else {
            echo "❌ Variable \$sucursalId NO disponible<br>";
        }
        
    } else {
        echo "❌ No se puede incluir ControladorUsuario.php<br>";
    }
} catch (Exception $e) {
    echo "❌ Error al incluir ControladorUsuario.php: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 3. Verificar conexión a base de datos
echo "<h2>3. Verificación de Base de Datos</h2>";

try {
    if (isset($conn) && $conn) {
        echo "✅ Conexión a base de datos establecida<br>";
        
        // Verificar si existe la tabla Tareas
        $sql = "SHOW TABLES LIKE 'Tareas'";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            echo "✅ Tabla 'Tareas' existe<br>";
            
            // Mostrar estructura de la tabla
            $sql = "DESCRIBE Tareas";
            $result = $conn->query($sql);
            
            if ($result) {
                echo "<h3>Estructura de la tabla Tareas:</h3>";
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                
                while ($row_table = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row_table['Field']) . "</td>";
                    echo "<td>" . htmlspecialchars($row_table['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row_table['Null']) . "</td>";
                    echo "<td>" . htmlspecialchars($row_table['Key']) . "</td>";
                    echo "<td>" . htmlspecialchars($row_table['Default'] ?? 'NULL') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            // Contar tareas
            $sql = "SELECT COUNT(*) as total FROM Tareas";
            $result = $conn->query($sql);
            if ($result) {
                $count = $result->fetch_assoc();
                echo "<br>Total de tareas en la base de datos: " . $count['total'] . "<br>";
            }
            
        } else {
            echo "❌ Tabla 'Tareas' NO existe<br>";
            echo "<h3>Script para crear la tabla:</h3>";
            echo "<pre>";
            echo "CREATE TABLE Tareas (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            echo "</pre>";
        }
        
    } else {
        echo "❌ No hay conexión a la base de datos<br>";
    }
} catch (Exception $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 4. Verificar TareasController
echo "<h2>4. Verificación de TareasController</h2>";

try {
    if (file_exists('Controladores/TareasController.php')) {
        include_once 'Controladores/TareasController.php';
        echo "✅ TareasController.php incluido correctamente<br>";
        
        if (isset($userId) && isset($sucursalId) && isset($conn)) {
            $tareasController = new TareasController($conn, $userId, $sucursalId);
            echo "✅ TareasController instanciado correctamente<br>";
            
            // Probar método getEstadisticas
            try {
                $estadisticas = $tareasController->getEstadisticas();
                echo "✅ Método getEstadisticas() funciona<br>";
                
                if ($estadisticas) {
                    echo "<h3>Estadísticas de tareas:</h3>";
                    echo "<table border='1' style='border-collapse: collapse;'>";
                    echo "<tr><th>Estado</th><th>Cantidad</th></tr>";
                    
                    while ($row_stat = $estadisticas->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row_stat['estado']) . "</td>";
                        echo "<td>" . htmlspecialchars($row_stat['cantidad']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } catch (Exception $e) {
                echo "❌ Error en getEstadisticas(): " . $e->getMessage() . "<br>";
            }
            
        } else {
            echo "❌ No se pueden instanciar TareasController - variables faltantes<br>";
        }
    } else {
        echo "❌ TareasController.php no existe<br>";
    }
} catch (Exception $e) {
    echo "❌ Error al incluir TareasController.php: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 5. Verificar ArrayTareas.php
echo "<h2>5. Verificación de ArrayTareas.php</h2>";

try {
    if (file_exists('Controladores/ArrayTareas.php')) {
        echo "✅ ArrayTareas.php existe<br>";
        
        // Simular una petición POST para probar
        $_POST['accion'] = 'listar';
        $_POST['estado'] = '';
        $_POST['prioridad'] = '';
        $_POST['asignado_a'] = '';
        
        ob_start();
        include 'Controladores/ArrayTareas.php';
        $output = ob_get_clean();
        
        echo "✅ ArrayTareas.php se ejecutó sin errores fatales<br>";
        echo "<h3>Respuesta de ArrayTareas.php:</h3>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
        
    } else {
        echo "❌ ArrayTareas.php no existe<br>";
    }
} catch (Exception $e) {
    echo "❌ Error al probar ArrayTareas.php: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 6. Información del servidor
echo "<h2>6. Información del Servidor</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No disponible') . "<br>";
echo "Directorio actual: " . getcwd() . "<br>";
echo "Archivo actual: " . __FILE__ . "<br>";

echo "<hr>";
echo "<h2>✅ Diagnóstico Completado</h2>";
echo "<p>Si todos los elementos están marcados con ✅, el sistema debería funcionar correctamente.</p>";
echo "<p>Si hay elementos marcados con ❌, esos son los problemas que necesitas resolver.</p>";
?>
