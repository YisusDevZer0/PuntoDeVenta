<?php
/**
 * Instalador completo del sistema de chat
 * Incluye todas las tablas, datos iniciales y configuraciones
 */

// Incluir conexión a la base de datos
include_once "Consultas/db_connect.php";

if (!$conn) {
    die("Error de conexión a la base de datos");
}

echo "<h2>Instalador Completo del Sistema de Chat</h2>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";

// Función para ejecutar SQL y mostrar resultado
function ejecutarSQL($sql, $descripcion) {
    global $conn;
    
    echo "<div style='margin: 10px 0; padding: 10px; border-left: 4px solid #009CFF; background: #f8f9fa;'>";
    echo "<strong>$descripcion</strong><br>";
    
    if ($conn->multi_query($sql)) {
        echo "<span style='color: green;'>✅ Ejecutado correctamente</span>";
    } else {
        echo "<span style='color: red;'>❌ Error: " . $conn->error . "</span>";
    }
    echo "</div>";
}

// 1. Crear tablas del chat
echo "<h3>1. Creando tablas del chat...</h3>";
$sql_tablas = file_get_contents('database/chat_sql_compatible.sql');
ejecutarSQL($sql_tablas, "Creando tablas del chat");

// 2. Actualizar tabla de notificaciones
echo "<h3>2. Actualizando tabla de notificaciones...</h3>";
$sql_notificaciones = file_get_contents('database/actualizar_notificaciones.sql');
ejecutarSQL($sql_notificaciones, "Actualizando tabla de notificaciones");

// 3. Crear conversaciones por sucursal
echo "<h3>3. Creando conversaciones por sucursal...</h3>";
$sql_conversaciones = "
-- Crear conversaciones por sucursal
INSERT IGNORE INTO chat_conversaciones (nombre_conversacion, descripcion, tipo_conversacion, sucursal_id, creado_por, privado)
SELECT 
    CONCAT('Chat - ', s.Nombre_Sucursal) as nombre_conversacion,
    CONCAT('Conversación interna de la sucursal ', s.Nombre_Sucursal) as descripcion,
    'sucursal' as tipo_conversacion,
    s.ID_Sucursal as sucursal_id,
    1 as creado_por,
    0 as privado
FROM Sucursales s
WHERE s.ID_Sucursal IS NOT NULL;
";
ejecutarSQL($sql_conversaciones, "Creando conversaciones por sucursal");

// 4. Agregar usuarios a conversaciones
echo "<h3>4. Agregando usuarios a conversaciones...</h3>";
$sql_usuarios = "
-- Agregar usuarios a conversaciones de su sucursal
INSERT IGNORE INTO chat_participantes (conversacion_id, usuario_id, rol)
SELECT 
    c.id_conversacion,
    u.Id_PvUser,
    CASE 
        WHEN u.Fk_Usuario IN (SELECT ID_User FROM Tipos_Usuarios WHERE TipoUsuario IN ('Administrador', 'MKT')) THEN 'admin'
        ELSE 'miembro'
    END as rol
FROM chat_conversaciones c
INNER JOIN Usuarios_PV u ON c.sucursal_id = u.Fk_Sucursal
WHERE c.tipo_conversacion = 'sucursal' AND u.Estatus = 'Activo';

-- Agregar usuarios a conversación general
INSERT IGNORE INTO chat_participantes (conversacion_id, usuario_id, rol)
SELECT 
    1 as conversacion_id,
    u.Id_PvUser,
    CASE 
        WHEN u.Fk_Usuario IN (SELECT ID_User FROM Tipos_Usuarios WHERE TipoUsuario IN ('Administrador', 'MKT')) THEN 'admin'
        ELSE 'miembro'
    END as rol
FROM Usuarios_PV u
WHERE u.Estatus = 'Activo';
";
ejecutarSQL($sql_usuarios, "Agregando usuarios a conversaciones");

// 5. Crear configuraciones por defecto
echo "<h3>5. Creando configuraciones por defecto...</h3>";
$sql_configuraciones = "
-- Insertar configuraciones por defecto para usuarios existentes
INSERT IGNORE INTO chat_configuraciones (usuario_id, notificaciones_sonido, notificaciones_push, tema_oscuro, mensajes_por_pagina, auto_borrar_mensajes)
SELECT 
    Id_PvUser,
    1 as notificaciones_sonido,
    1 as notificaciones_push,
    0 as tema_oscuro,
    50 as mensajes_por_pagina,
    0 as auto_borrar_mensajes
FROM Usuarios_PV 
WHERE Estatus = 'Activo';

-- Insertar estados de usuario para usuarios existentes
INSERT IGNORE INTO chat_estados_usuario (usuario_id, estado, ultima_actividad)
SELECT 
    Id_PvUser,
    'offline' as estado,
    NOW() as ultima_actividad
FROM Usuarios_PV 
WHERE Estatus = 'Activo';
";
ejecutarSQL($sql_configuraciones, "Creando configuraciones por defecto");

// 6. Crear carpeta de uploads
echo "<h3>6. Creando carpeta de uploads...</h3>";
$upload_dir = __DIR__ . '/uploads/chat/';
if (!is_dir($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "<div style='color: green;'>✅ Carpeta de uploads creada: $upload_dir</div>";
    } else {
        echo "<div style='color: red;'>❌ Error al crear carpeta de uploads</div>";
    }
} else {
    echo "<div style='color: blue;'>ℹ Carpeta de uploads ya existe</div>";
}

// 7. Crear archivo .htaccess para uploads
echo "<h3>7. Creando archivo .htaccess para uploads...</h3>";
$htaccess_content = "# Proteger archivos de uploads
<Files \"*\">
    Order Allow,Deny
    Deny from all
</Files>

# Permitir solo archivos de imagen, video y audio
<FilesMatch \"\\.(jpg|jpeg|png|gif|mp4|avi|mov|mp3|wav|pdf|doc|docx|txt)$\">
    Order Allow,Deny
    Allow from all
</FilesMatch>";

$htaccess_path = __DIR__ . '/uploads/.htaccess';
if (file_put_contents($htaccess_path, $htaccess_content)) {
    echo "<div style='color: green;'>✅ Archivo .htaccess creado</div>";
} else {
    echo "<div style='color: red;'>❌ Error al crear archivo .htaccess</div>";
}

// 8. Verificar instalación
echo "<h3>8. Verificando instalación...</h3>";

// Verificar tablas
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

$tablas_ok = 0;
foreach ($tablas_chat as $tabla) {
    $query = "SHOW TABLES LIKE '$tabla'";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $tablas_ok++;
    }
}

echo "<div style='color: " . ($tablas_ok == count($tablas_chat) ? 'green' : 'red') . ";'>";
echo "Tablas del chat: $tablas_ok/" . count($tablas_chat);
echo "</div>";

// Verificar conversaciones
$query = "SELECT COUNT(*) as count FROM chat_conversaciones";
$result = $conn->query($query);
$conversaciones_count = $result->fetch_assoc()['count'];

echo "<div style='color: " . ($conversaciones_count > 0 ? 'green' : 'red') . ";'>";
echo "Conversaciones creadas: $conversaciones_count";
echo "</div>";

// Verificar participantes
$query = "SELECT COUNT(*) as count FROM chat_participantes";
$result = $conn->query($query);
$participantes_count = $result->fetch_assoc()['count'];

echo "<div style='color: " . ($participantes_count > 0 ? 'green' : 'red') . ";'>";
echo "Participantes agregados: $participantes_count";
echo "</div>";

// Verificar configuraciones
$query = "SELECT COUNT(*) as count FROM chat_configuraciones";
$result = $conn->query($query);
$configuraciones_count = $result->fetch_assoc()['count'];

echo "<div style='color: " . ($configuraciones_count > 0 ? 'green' : 'red') . ";'>";
echo "Configuraciones creadas: $configuraciones_count";
echo "</div>";

// 9. Resumen final
echo "<h3>9. Resumen de la instalación</h3>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0;'>";

if ($tablas_ok == count($tablas_chat) && $conversaciones_count > 0 && $participantes_count > 0) {
    echo "<h4 style='color: green; margin: 0 0 10px 0;'>✅ Instalación completada exitosamente</h4>";
    echo "<p>El sistema de chat está listo para usar. Puedes acceder a él desde el menú principal.</p>";
    echo "<p><strong>Funcionalidades disponibles:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Chat en tiempo real</li>";
    echo "<li>✅ Conversaciones por sucursal</li>";
    echo "<li>✅ Notificaciones push y por email</li>";
    echo "<li>✅ Subida de archivos</li>";
    echo "<li>✅ Configuración personalizada</li>";
    echo "<li>✅ Integración con sistema de notificaciones</li>";
    echo "</ul>";
} else {
    echo "<h4 style='color: red; margin: 0 0 10px 0;'>❌ Instalación incompleta</h4>";
    echo "<p>Algunos elementos no se instalaron correctamente. Revisa los errores anteriores.</p>";
}

echo "</div>";

// 10. Enlaces de prueba
echo "<h3>10. Enlaces de prueba</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='test_chat.php' style='background: #009CFF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Verificar Instalación</a>";
echo "<a href='Mensajes.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Chat</a>";
echo "</div>";

echo "</div>";

$conn->close();
?>

<style>
body {
    background-color: #f5f5f5;
    margin: 0;
    padding: 20px;
}

h2, h3 {
    color: #333;
}

h2 {
    text-align: center;
    border-bottom: 2px solid #009CFF;
    padding-bottom: 10px;
}

h3 {
    border-left: 4px solid #009CFF;
    padding-left: 10px;
    margin-top: 30px;
}

ul {
    margin: 10px 0;
    padding-left: 20px;
}

li {
    margin: 5px 0;
}
</style>
