<?php
// Archivo de prueba para verificar la consulta de personal
include("Controladores/db_connect.php");
include("Controladores/ControladorUsuario.php");

echo "<h2>Prueba de Consulta de Personal</h2>";

// Verificar si $row está definido
if (isset($row)) {
    echo "<p><strong>Licencia del usuario:</strong> " . ($row['Licencia'] ?? 'No definida') . "</p>";
    echo "<p><strong>Nombre del usuario:</strong> " . ($row['Nombre_Apellidos'] ?? 'No definido') . "</p>";
} else {
    echo "<p><strong>Error:</strong> Variable \$row no está definida</p>";
}

// Hacer una consulta simple para verificar si hay datos
$sql_test = "SELECT COUNT(*) as total FROM Usuarios_PV WHERE Estatus = 'Activo'";
$result_test = $conn->query($sql_test);

if ($result_test) {
    $row_test = $result_test->fetch_assoc();
    echo "<p><strong>Total de usuarios activos:</strong> " . $row_test['total'] . "</p>";
} else {
    echo "<p><strong>Error en consulta de prueba:</strong> " . $conn->error . "</p>";
}

// Mostrar algunos usuarios de ejemplo
$sql_users = "SELECT Id_PvUser, Nombre_Apellidos, Estatus FROM Usuarios_PV LIMIT 5";
$result_users = $conn->query($sql_users);

if ($result_users && $result_users->num_rows > 0) {
    echo "<h3>Primeros 5 usuarios:</h3>";
    echo "<ul>";
    while ($user = $result_users->fetch_assoc()) {
        echo "<li>ID: " . $user['Id_PvUser'] . " - Nombre: " . $user['Nombre_Apellidos'] . " - Estatus: " . $user['Estatus'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p><strong>No se encontraron usuarios</strong></p>";
}

$conn->close();
?> 