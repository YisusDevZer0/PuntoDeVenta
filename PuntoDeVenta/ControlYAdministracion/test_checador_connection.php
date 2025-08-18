<?php
// Simular el mismo contexto que el checador
include_once "Controladores/ControladorUsuario.php";
include_once "Consultas/db_connect.php";

echo "<h2>Prueba de Conexión Específica del Checador</h2>";

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

// Probar la misma consulta que usa el ChecadorController
echo "<h3>Prueba de consulta específica del Checador:</h3>";

try {
    // Probar la consulta de usuario que usa el ChecadorController
    $stmt = $conn->prepare("SELECT Id_PvUser, Nombre_Apellidos FROM Usuarios_PV WHERE Id_PvUser = ?");
    if (!$stmt) {
        echo "<p style='color: red;'>❌ Error preparando consulta de usuario: " . $conn->error . "</p>";
    } else {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo "<p style='color: green;'>✅ Usuario encontrado: " . $user['Nombre_Apellidos'] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Usuario no encontrado (ID: $userId)</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en consulta de usuario: " . $e->getMessage() . "</p>";
}

// Probar inserción exacta como la del ChecadorController
echo "<h3>Prueba de inserción exacta del Checador:</h3>";

try {
    $stmt = $conn->prepare("
        INSERT INTO asistencias (usuario_id, tipo, latitud, longitud, fecha_hora, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    if (!$stmt) {
        echo "<p style='color: red;'>❌ Error preparando inserción: " . $conn->error . "</p>";
    } else {
        $test_user_id = $userId;
        $test_tipo = 'prueba_checador';
        $test_lat = 20.9674;
        $test_lng = -89.5926;
        $test_timestamp = date('Y-m-d H:i:s');
        
        $stmt->bind_param("isdd", $test_user_id, $test_tipo, $test_lat, $test_lng, $test_timestamp);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Inserción de prueba exitosa</p>";
            echo "<p style='color: blue;'>📊 ID del registro insertado: " . $stmt->insert_id . "</p>";
            
            // Eliminar el registro de prueba
            $conn->query("DELETE FROM asistencias WHERE tipo = 'prueba_checador'");
            echo "<p style='color: blue;'>🗑️ Registro de prueba eliminado</p>";
        } else {
            echo "<p style='color: red;'>❌ Error en inserción: " . $stmt->error . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Excepción en inserción: " . $e->getMessage() . "</p>";
}

// Probar verificación de duplicados
echo "<h3>Prueba de verificación de duplicados:</h3>";

try {
    $fecha_hoy = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT id FROM asistencias 
        WHERE usuario_id = ? AND tipo = ? AND DATE(fecha_hora) = ?
    ");
    
    if (!$stmt) {
        echo "<p style='color: red;'>❌ Error preparando verificación: " . $conn->error . "</p>";
    } else {
        $test_tipo = 'entrada';
        $stmt->bind_param("iss", $userId, $test_tipo, $fecha_hoy);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "<p style='color: blue;'>📊 Registros existentes para hoy: " . $result->num_rows . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en verificación: " . $e->getMessage() . "</p>";
}

// Probar la URL del controlador
echo "<h3>Prueba de acceso al controlador:</h3>";

$controllerUrl = 'Controladores/ChecadorController.php';
if (file_exists($controllerUrl)) {
    echo "<p style='color: green;'>✅ Archivo del controlador existe: $controllerUrl</p>";
} else {
    echo "<p style='color: red;'>❌ Archivo del controlador no encontrado: $controllerUrl</p>";
}

// Probar acceso web al controlador
$testUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $controllerUrl;
echo "<p style='color: blue;'>🔗 URL completa del controlador: $testUrl</p>";

echo "<hr>";
echo "<p><a href='Checador.php'>Volver al Checador</a></p>";
?>
