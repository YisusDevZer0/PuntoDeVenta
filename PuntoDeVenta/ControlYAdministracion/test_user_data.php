<?php
session_start();

echo "<h2>Datos del Usuario Actual</h2>";

// Incluir el controlador de usuario
include_once "Controladores/ControladorUsuario.php";

echo "<h3>Información de Sesión:</h3>";
echo "ControlMaestro: " . (isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 'NO') . "<br>";
echo "AdministradorRH: " . (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : 'NO') . "<br>";
echo "Marketing: " . (isset($_SESSION['Marketing']) ? $_SESSION['Marketing'] : 'NO') . "<br>";

echo "<h3>Datos del Usuario (Variable \$row):</h3>";
if (isset($row)) {
    echo "<pre>";
    print_r($row);
    echo "</pre>";
    
    echo "<h3>Análisis del Menú:</h3>";
    echo "TipoUsuario: " . $row['TipoUsuario'] . "<br>";
    echo "Condición del menú: " . ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT' ? 'TRUE' : 'FALSE') . "<br>";
    
    if ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') {
        echo "✅ El usuario debería ver el menú completo<br>";
    } else {
        echo "❌ El usuario NO debería ver el menú completo<br>";
        echo "Tipo de usuario actual: " . $row['TipoUsuario'] . "<br>";
    }
} else {
    echo "❌ Variable \$row no está disponible<br>";
}

echo "<h3>Verificación de Variables:</h3>";
echo "Variable \$disabledAttr: " . (isset($disabledAttr) ? $disabledAttr : 'NO DEFINIDA') . "<br>";
echo "Variable \$currentPage: " . (isset($currentPage) ? $currentPage : 'NO DEFINIDA') . "<br>";
?> 