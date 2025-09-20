<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/db_connect.php";

echo "<h2>Información del Usuario</h2>";
echo "Usuario ID: " . $row['Id_PvUser'] . "<br>";
echo "Sucursal ID: " . $row['Fk_Sucursal'] . "<br>";
echo "Nombre: " . $row['Nombre_Apellidos'] . "<br>";

echo "<h2>Verificación de Base de Datos</h2>";

// Verificar si existe la tabla tareas
$sql = "SHOW TABLES LIKE 'tareas'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    echo "✅ La tabla 'tareas' existe<br>";
    
    // Mostrar estructura de la tabla
    $sql = "DESCRIBE tareas";
    $result = mysqli_query($conn, $sql);
    echo "<h3>Estructura de la tabla tareas:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row_table = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row_table['Field'] . "</td>";
        echo "<td>" . $row_table['Type'] . "</td>";
        echo "<td>" . $row_table['Null'] . "</td>";
        echo "<td>" . $row_table['Key'] . "</td>";
        echo "<td>" . $row_table['Default'] . "</td>";
        echo "<td>" . $row_table['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Contar tareas
    $sql = "SELECT COUNT(*) as total FROM tareas";
    $result = mysqli_query($conn, $sql);
    $count = mysqli_fetch_assoc($result);
    echo "<br>Total de tareas en la base de datos: " . $count['total'] . "<br>";
    
    // Mostrar tareas del usuario actual
    $sql = "SELECT * FROM tareas WHERE asignado_a = " . $row['Id_PvUser'];
    $result = mysqli_query($conn, $sql);
    echo "<br>Tareas asignadas al usuario actual: " . mysqli_num_rows($result) . "<br>";
    
    if (mysqli_num_rows($result) > 0) {
        echo "<h3>Tareas del usuario:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Prioridad</th><th>Asignado a</th></tr>";
        while ($tarea = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $tarea['id'] . "</td>";
            echo "<td>" . $tarea['titulo'] . "</td>";
            echo "<td>" . $tarea['estado'] . "</td>";
            echo "<td>" . $tarea['prioridad'] . "</td>";
            echo "<td>" . $tarea['asignado_a'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} else {
    echo "❌ La tabla 'tareas' NO existe<br>";
    echo "Necesitas crear la tabla primero.<br>";
    
    echo "<h3>Script para crear la tabla tareas:</h3>";
    echo "<pre>";
    echo "CREATE TABLE tareas (
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
    FOREIGN KEY (asignado_a) REFERENCES Usuarios_PV(Id_PvUser),
    FOREIGN KEY (creado_por) REFERENCES Usuarios_PV(Id_PvUser)
);";
    echo "</pre>";
}

// Verificar conexión a la base de datos
if ($conn) {
    echo "<br>✅ Conexión a la base de datos exitosa<br>";
} else {
    echo "<br>❌ Error en la conexión a la base de datos<br>";
}
?>
