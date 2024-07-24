<?php
// Verifica el parámetro recibido
if (!isset($_GET['id_sucursal'])) {
    die("El parámetro 'id_sucursal' no está presente en la URL.");
}

$id_sucursal = $_GET['id_sucursal'];
echo "Parámetro recibido: " . htmlspecialchars($id_sucursal); // Para depurar

if (!is_numeric($id_sucursal)) {
    die("ID de sucursal no válido. Parámetro recibido: " . htmlspecialchars($id_sucursal));
}

$id_sucursal = intval($id_sucursal);
if ($id_sucursal <= 0) {
    die("ID de sucursal no válido.");
}

?>