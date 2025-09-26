<?php
// Archivo de prueba para verificar la sesión
include_once "Controladores/ControladorUsuario.php";

echo "<h2>Prueba de Sesión - Devoluciones</h2>";
echo "<hr>";

echo "<h3>Variables de Sesión:</h3>";
echo "<pre>";
echo "ControlMaestro: " . (isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 'NO DEFINIDA') . "\n";
echo "AdministradorRH: " . (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : 'NO DEFINIDA') . "\n";
echo "Marketing: " . (isset($_SESSION['Marketing']) ? $_SESSION['Marketing'] : 'NO DEFINIDA') . "\n";
echo "</pre>";

echo "<h3>Variable \$row:</h3>";
if (isset($row)) {
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>Variable \$row NO está definida</p>";
}

echo "<h3>Datos del Usuario:</h3>";
$usuario_id = isset($row['Id_PvUser']) ? $row['Id_PvUser'] : 'NO DEFINIDO';
$sucursal_id = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 'NO DEFINIDO';
$tipo_usuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'NO DEFINIDO';

echo "<p>Usuario ID: $usuario_id</p>";
echo "<p>Sucursal ID: $sucursal_id</p>";
echo "<p>Tipo Usuario: $tipo_usuario</p>";

echo "<h3>Verificación de Sesión:</h3>";
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo "<p style='color: red;'>❌ SESIÓN NO VÁLIDA - Sería redirigido a Expiro.php</p>";
} else {
    echo "<p style='color: green;'>✅ SESIÓN VÁLIDA</p>";
}

echo "<hr>";
echo "<p><a href='Devoluciones.php'>Ir a Devoluciones</a></p>";
?>
