<?php
// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>🔍 Debug Final - Verificando Datos del Usuario</h2>";

// Verificar sesión
echo "<h3>1. Sesión actual:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Determinar el ID de usuario
$userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

if (!$userId) {
    echo "<h3>❌ No hay ID de usuario en la sesión</h3>";
    echo "<p>Esto significa que necesitas hacer login primero.</p>";
    exit();
}

echo "<h3>2. ID de usuario: $userId</h3>";

// Incluir ControladorUsuario.php
include_once("Controladores/ControladorUsuario.php");

echo "<h3>3. Datos del usuario después de ControladorUsuario.php:</h3>";
if (isset($row)) {
    echo "<p>✅ Variable \$row está definida</p>";
    echo "<pre>";
    print_r($row);
    echo "</pre>";
    
    echo "<h3>4. Verificando variables específicas:</h3>";
    echo "<p><strong>\$row['Nombre_Apellidos']:</strong> " . (isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : 'NO DEFINIDO') . "</p>";
    echo "<p><strong>\$row['TipoUsuario']:</strong> " . (isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'NO DEFINIDO') . "</p>";
    echo "<p><strong>\$row['Licencia']:</strong> " . (isset($row['Licencia']) ? $row['Licencia'] : 'NO DEFINIDO') . "</p>";
    echo "<p><strong>\$row['file_name']:</strong> " . (isset($row['file_name']) ? $row['file_name'] : 'NO DEFINIDO') . "</p>";
    
    echo "<h3>5. Prueba de imagen de perfil:</h3>";
    $imageUrl = "https://doctorpez.mx/PuntoDeVenta/PerfilesImg/" . $row['file_name'];
    echo "<p>URL de imagen: $imageUrl</p>";
    echo "<img src='$imageUrl' alt='Perfil' style='width: 50px; height: 50px;' onerror=\"this.style.display='none'; this.nextElementSibling.style.display='inline';\" />";
    echo "<span style='display:none; color:red;'>❌ Error cargando imagen</span>";
    
} else {
    echo "<p>❌ Variable \$row NO está definida</p>";
    echo "<p>Esto indica un problema en ControladorUsuario.php</p>";
}

echo "<h3>6. Prueba del dashboard:</h3>";
echo "<p><a href='index.php' target='_blank'>Abrir Dashboard</a></p>";
?> 