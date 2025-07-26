<?php
session_start();

echo "<h2>Prueba del Menú Corregido</h2>";

// Simular las variables que deberían estar disponibles
$currentPage = 'dashboard';
$showDashboard = true;

// Simular datos de usuario (para pruebas)
$row = [
    'TipoUsuario' => 'Administrador',
    'Nombre_Apellidos' => 'Usuario Test',
    'Licencia' => 'Doctor Pez',
    'file_name' => 'user.jpg'
];

echo "<h3>Variables de Prueba:</h3>";
echo "currentPage: " . $currentPage . "<br>";
echo "showDashboard: " . ($showDashboard ? 'true' : 'false') . "<br>";
echo "TipoUsuario: " . $row['TipoUsuario'] . "<br>";

echo "<h3>Condiciones del Menú:</h3>";
echo "showDashboard existe y es true: " . (isset($showDashboard) && $showDashboard ? '✅' : '❌') . "<br>";
echo "TipoUsuario es Administrador o MKT: " . (($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT') ? '✅' : '❌') . "<br>";

echo "<h3>Resultado Esperado:</h3>";
echo "✅ Dashboard debería mostrarse<br>";
echo "✅ Menú completo debería mostrarse<br>";

echo "<h3>Prueba de Inclusión:</h3>";
// Incluir el menú para ver si funciona
ob_start();
include "Menu.php";
$menuOutput = ob_get_clean();

if (strpos($menuOutput, 'Dashboard') !== false) {
    echo "✅ Dashboard encontrado en el menú<br>";
} else {
    echo "❌ Dashboard NO encontrado en el menú<br>";
}

if (strpos($menuOutput, 'Punto de venta') !== false) {
    echo "✅ Menú completo encontrado<br>";
} else {
    echo "❌ Menú completo NO encontrado<br>";
}

echo "<h3>HTML del Menú:</h3>";
echo "<pre>" . htmlspecialchars($menuOutput) . "</pre>";
?> 