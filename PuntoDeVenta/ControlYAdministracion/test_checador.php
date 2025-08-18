<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Consultas/db_connect.php";

echo "<h2>Prueba de Conexión y Tablas del Checador</h2>";

// Verificar conexión
if ($conn) {
    echo "<p style='color: green;'>✅ Conexión a la base de datos: OK</p>";
} else {
    echo "<p style='color: red;'>❌ Error de conexión a la base de datos</p>";
    exit;
}

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo "<p style='color: red;'>❌ No hay sesión activa</p>";
} else {
    $userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);
    echo "<p style='color: green;'>✅ Sesión activa - Usuario ID: $userId</p>";
}

// Verificar tabla Usuarios_PV
$result = $conn->query("SHOW TABLES LIKE 'Usuarios_PV'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Tabla Usuarios_PV: Existe</p>";
    
    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT Id_PvUser, Nombre_Apellidos FROM Usuarios_PV WHERE Id_PvUser = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<p style='color: green;'>✅ Usuario encontrado: " . $user['Nombre_Apellidos'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Usuario no encontrado en Usuarios_PV</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Tabla Usuarios_PV: No existe</p>";
}

// Verificar tabla asistencias
$result = $conn->query("SHOW TABLES LIKE 'asistencias'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Tabla asistencias: Existe</p>";
    
    // Mostrar estructura de la tabla
    $result = $conn->query("DESCRIBE asistencias");
    echo "<h3>Estructura de la tabla asistencias:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Tabla asistencias: No existe</p>";
}

// Verificar tabla ubicaciones_trabajo
$result = $conn->query("SHOW TABLES LIKE 'ubicaciones_trabajo'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Tabla ubicaciones_trabajo: Existe</p>";
} else {
    echo "<p style='color: red;'>❌ Tabla ubicaciones_trabajo: No existe</p>";
}

// Probar inserción de prueba
echo "<h3>Prueba de inserción:</h3>";
try {
    $stmt = $conn->prepare("
        INSERT INTO asistencias (usuario_id, tipo, latitud, longitud, fecha_hora, created_at) 
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ");
    
    if ($stmt) {
        $test_user_id = $userId;
        $test_tipo = 'prueba';
        $test_lat = 20.9674;
        $test_lng = -89.5926;
        
        $stmt->bind_param("isdd", $test_user_id, $test_tipo, $test_lat, $test_lng);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Inserción de prueba: Exitosa</p>";
            
            // Eliminar el registro de prueba
            $conn->query("DELETE FROM asistencias WHERE tipo = 'prueba'");
            echo "<p style='color: blue;'>🗑️ Registro de prueba eliminado</p>";
        } else {
            echo "<p style='color: red;'>❌ Error en inserción de prueba: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error preparando inserción de prueba: " . $conn->error . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Excepción en prueba: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='Checador.php'>Volver al Checador</a></p>";
?>
