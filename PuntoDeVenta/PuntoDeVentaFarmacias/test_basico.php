<?php
echo "Página de prueba - Si ves esto, PHP funciona<br>";

include_once "Controladores/ControladorUsuario.php";
echo "ControladorUsuario incluido<br>";

echo "Usuario: " . $row['Nombre_Apellidos'] . "<br>";
echo "Sucursal: " . $row['Nombre_Sucursal'] . "<br>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Básico</title>
</head>
<body>
    <h1>Test Básico Funcionando</h1>
    <p>Si ves esto, la página carga correctamente</p>
</body>
</html>
