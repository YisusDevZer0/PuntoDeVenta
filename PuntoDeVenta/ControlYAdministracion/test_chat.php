<?php
/**
 * Archivo de prueba para el sistema de chat
 * Verifica que todas las funcionalidades estén funcionando
 */

// Incluir conexión a la base de datos
include_once "Controladores/db_connect.php";

echo "<h2>Prueba del Sistema de Chat</h2>";

// Verificar conexión a la base de datos
if ($conn) {
    echo "<p style='color: green;'>✅ Conexión a la base de datos: OK</p>";
} else {
    echo "<p style='color: red;'>❌ Error de conexión a la base de datos</p>";
    exit();
}

// Verificar que las tablas existan
$tablas_chat = [
    'chat_conversaciones',
    'chat_participantes', 
    'chat_mensajes',
    'chat_lecturas',
    'chat_reacciones',
    'chat_configuraciones',
    'chat_estados_usuario',
    'chat_mensajes_eliminados'
];

echo "<h3>Verificando tablas del chat:</h3>";
foreach ($tablas_chat as $tabla) {
    $query = "SHOW TABLES LIKE '$tabla'";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Tabla $tabla: Existe</p>";
    } else {
        echo "<p style='color: red;'>❌ Tabla $tabla: No existe</p>";
    }
}

// Verificar conversaciones iniciales
echo "<h3>Verificando conversaciones iniciales:</h3>";
$query = "SELECT COUNT(*) as count FROM chat_conversaciones";
$result = $conn->query($query);
$count = $result->fetch_assoc()['count'];

if ($count > 0) {
    echo "<p style='color: green;'>✅ Conversaciones existentes: $count</p>";
    
    // Mostrar conversaciones
    $query = "SELECT id_conversacion, nombre_conversacion, tipo_conversacion FROM chat_conversaciones";
    $result = $conn->query($query);
    
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['nombre_conversacion']} ({$row['tipo_conversacion']})</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ No hay conversaciones</p>";
}

// Verificar usuarios
echo "<h3>Verificando usuarios:</h3>";
$query = "SELECT COUNT(*) as count FROM Usuarios_PV WHERE Estatus = 'Activo'";
$result = $conn->query($query);
$count = $result->fetch_assoc()['count'];

if ($count > 0) {
    echo "<p style='color: green;'>✅ Usuarios activos: $count</p>";
} else {
    echo "<p style='color: red;'>❌ No hay usuarios activos</p>";
}

// Verificar configuraciones de chat
echo "<h3>Verificando configuraciones:</h3>";
$query = "SELECT COUNT(*) as count FROM chat_configuraciones";
$result = $conn->query($query);
$count = $result->fetch_assoc()['count'];

if ($count > 0) {
    echo "<p style='color: green;'>✅ Configuraciones de chat: $count</p>";
} else {
    echo "<p style='color: red;'>❌ No hay configuraciones de chat</p>";
}

// Verificar carpeta de uploads
echo "<h3>Verificando carpeta de uploads:</h3>";
$upload_dir = __DIR__ . '/uploads/chat/';
if (is_dir($upload_dir)) {
    echo "<p style='color: green;'>✅ Carpeta de uploads: Existe</p>";
    
    if (is_writable($upload_dir)) {
        echo "<p style='color: green;'>✅ Carpeta de uploads: Escribible</p>";
    } else {
        echo "<p style='color: red;'>❌ Carpeta de uploads: No escribible</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Carpeta de uploads: No existe</p>";
}

// Verificar archivos del sistema
echo "<h3>Verificando archivos del sistema:</h3>";
$archivos = [
    'Mensajes.php',
    'Controladores/ChatController.php',
    'api/chat_api.php',
    'js/chat.js',
    'css/chat.css'
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "<p style='color: green;'>✅ $archivo: Existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $archivo: No existe</p>";
    }
}

// Probar API
echo "<h3>Probando API del chat:</h3>";
$test_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/api/chat_api.php?action=conversaciones';

echo "<p>URL de prueba: <a href='$test_url' target='_blank'>$test_url</a></p>";

// Crear conversación de prueba si no existe
echo "<h3>Creando conversación de prueba:</h3>";
$query = "SELECT COUNT(*) as count FROM chat_conversaciones WHERE nombre_conversacion = 'Prueba del Sistema'";
$result = $conn->query($query);
$exists = $result->fetch_assoc()['count'] > 0;

if (!$exists) {
    $query = "INSERT INTO chat_conversaciones (nombre_conversacion, descripcion, tipo_conversacion, creado_por) VALUES ('Prueba del Sistema', 'Conversación de prueba para verificar el funcionamiento', 'grupo', 1)";
    
    if ($conn->query($query)) {
        $conversacion_id = $conn->insert_id;
        echo "<p style='color: green;'>✅ Conversación de prueba creada (ID: $conversacion_id)</p>";
        
        // Agregar participantes
        $query = "INSERT INTO chat_participantes (conversacion_id, usuario_id, rol) VALUES ($conversacion_id, 1, 'admin')";
        $conn->query($query);
        echo "<p style='color: green;'>✅ Participante agregado a la conversación de prueba</p>";
    } else {
        echo "<p style='color: red;'>❌ Error al crear conversación de prueba: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ Conversación de prueba ya existe</p>";
}

echo "<hr>";
echo "<h3>Resumen:</h3>";
echo "<p>Si todos los elementos están marcados con ✅, el sistema de chat debería funcionar correctamente.</p>";
echo "<p><a href='Mensajes.php' style='background: #009CFF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Chat</a></p>";

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}
h2, h3 {
    color: #333;
}
p {
    margin: 5px 0;
}
hr {
    margin: 20px 0;
    border: 1px solid #ddd;
}
ul {
    margin: 10px 0;
    padding-left: 20px;
}
</style>
