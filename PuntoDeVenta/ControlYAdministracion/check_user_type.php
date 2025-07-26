<?php
session_start();

echo "<h2>Verificación del Tipo de Usuario</h2>";

// Verificar sesión
if (!isset($_SESSION['ControlMaestro'])) {
    echo "❌ No hay sesión activa";
    exit;
}

$userId = $_SESSION['ControlMaestro'];
echo "ID de Usuario: $userId<br>";

// Conectar a la base de datos
include_once "../Consultas/db_connect.php";

// Consulta para obtener el tipo de usuario
$sql = "SELECT 
    u.Id_PvUser,
    u.Nombre_Apellidos,
    t.TipoUsuario
FROM Usuarios_PV u
INNER JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User
WHERE u.Id_PvUser = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo "<h3>Datos del Usuario:</h3>";
    echo "ID: " . $user['Id_PvUser'] . "<br>";
    echo "Nombre: " . $user['Nombre_Apellidos'] . "<br>";
    echo "Tipo: " . $user['TipoUsuario'] . "<br>";
    
    echo "<h3>Análisis del Menú:</h3>";
    if ($user['TipoUsuario'] == 'Administrador' || $user['TipoUsuario'] == 'MKT') {
        echo "✅ El usuario debería ver el menú completo<br>";
    } else {
        echo "❌ El usuario NO debería ver el menú completo<br>";
        echo "Tipo actual: " . $user['TipoUsuario'] . "<br>";
        echo "Tipos permitidos: Administrador, MKT<br>";
    }
} else {
    echo "❌ Usuario no encontrado";
}

$stmt->close();
$conn->close();
?> 